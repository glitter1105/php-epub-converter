<?php

require_once 'vendor/autoload.php';

use Overtrue\PHPOpenCC\OpenCC;

class EPubConverter
{
    protected $filePath;
    protected $strategy;
    protected $outputPath;
    protected $tempDir;

    public function __construct($filePath, $strategy = 's2t', $outputPath = null)
    {
        $this->filePath = $filePath;
        $this->strategy = $strategy;
        $this->outputPath = $outputPath;
        $this->tempDir = sys_get_temp_dir() . '/epub_converter_' . uniqid();
    }

    public function convert()
    {
        // 1. 解压 EPUB 文件
        $this->unzipEPub();

        // 2. 转换内容
        $this->convertContent();

        // 3. 重新打包 EPUB
        $this->zipEPub();

        // 4. 清理临时文件
        $this->cleanup();
    }

    protected function unzipEPub()
    {
        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0777, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($this->filePath) === true) {
            $zip->extractTo($this->tempDir);
            $zip->close();
        } else {
            throw new Exception('无法打开 EPUB 文件');
        }
    }

    protected function convertContent()
    {
        // 转换元数据 (OPF 文件)
        $opfFile = $this->findOPFFile();

        if ($opfFile) {
            $content = file_get_contents($opfFile);
            $convertedContent = OpenCC::convert($content, $this->strategy);
            file_put_contents($opfFile, $convertedContent);
        }

        // 遍历所有 HTML/XHTML 文件并转换
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->tempDir),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $file) {
            if ($file->isFile() && in_array(strtolower($file->getExtension()), ['html', 'xhtml', 'htm'])) {
                $content = file_get_contents($file->getPathname());
                $convertedContent = OpenCC::convert($content, $this->strategy);
                file_put_contents($file->getPathname(), $convertedContent);
            }
        }
    }

    protected function findOPFFile()
    {
        $containerFile = $this->tempDir . '/META-INF/container.xml';
        if (file_exists($containerFile)) {
            $xml = simplexml_load_file($containerFile);
            $rootfile = $xml->rootfiles->rootfile;
            if ($rootfile && $rootfile['full-path']) {
                return $this->tempDir . '/' . (string)$rootfile['full-path'];
            }
        }
        return null;
    }


    protected function zipEPub()
    {
        $outputFile = $this->outputPath ?: $this->filePath;

        $zip = new ZipArchive();
        if ($zip->open($outputFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($this->tempDir),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($this->tempDir) + 1);
                    // 添加 mimetype 文件时，不进行压缩
                    if ($relativePath === 'mimetype') {
                        $zip->addFile($filePath, $relativePath, ZipArchive::FL_NOCASE);
                    } else {
                        $zip->addFile($filePath, $relativePath);
                    }
                }
            }

            $zip->close();
        } else {
            throw new Exception('无法创建 ZIP 文件');
        }
    }

    protected function cleanup()
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->tempDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }

        rmdir($this->tempDir);
    }
}

// 获取命令行参数
$filePath = $argv[1] ?? null;
$strategy = $argv[2] ?? 's2t'; // 默认策略为简体到繁体
$outputPath = $argv[3] ?? null;

// 检查文件路径
if (!$filePath || !file_exists($filePath)) {
    echo "请提供有效的 epub 文件路径。\n";
    exit(1);
}

// 执行转换
try {
    $converter = new EPubConverter($filePath, $strategy, $outputPath);
    $converter->convert();
    echo "转换完成。\n";
} catch (Exception $e) {
    echo "转换失败: " . $e->getMessage() . "\n";
}