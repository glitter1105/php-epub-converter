# EPUB 简繁体转换工具

这是一个简单的 PHP 命令行工具，用于将 EPUB 电子书中的文本内容进行简体、繁体、日文之间的转换。

## 安装方法(php>=8.0)

```bash
git clone  https://github.com/glitter1105/php-epub-converter.git
```

```bash
composer install 
```

## 使用方法

```bash
php converter.php <epub文件路径> [转换策略] [输出文件路径]
```

- **`<epub文件路径>`**: 必填参数，指定要转换的 epub 文件的路径。
- **`[转换策略]`**: 可选参数，指定转换策略，默认为 `s2t` (简体到繁体)
  。支持的策略请参考 [overtrue/php-opencc](https://github.com/overtrue/php-opencc) 的文档。
- **`[输出文件路径]`**: 可选参数，指定转换后的 epub 文件保存的路径。如果不指定此参数，将覆盖原始文件。

**示例：**

1. 将 `book.epub` 从简体中文转换为繁体中文，并保存为 `output.epub`：

    ```bash
    php converter.php book.epub s2t output.epub
    ```

2. 将 `book.epub` 从繁体中文转换为简体中文，并保存为 `traditional.epub`

    ```bash
    php converter.php book.epub t2s traditional.epub
    ```

3. 将 `book.epub` 从简体中文转换为繁体中文，直接覆盖原文件：

    ```bash
    php converter.php book.epub s2t
    ```

## 注意

- 此工具会解压 epub 文件到一个临时目录，进行转换，然后重新打包。
- 如果未指定输出路径，转换后的 epub 文件将**覆盖**原始文件，请**务必备份**您的 epub 文件。
- 此工具现在支持转换元数据文件 (OPF) 以及所有 HTML/XHTML 文件中的文本内容。

## 依赖

- [overtrue/php-opencc](https://github.com/overtrue/php-opencc): 中文简繁体转换库。
- PHP 的 Zip 扩展 (通常默认启用)。

## 转换策略

| 策略 （别名）                                   | 说明              |
|-------------------------------------------|-----------------|
| `SIMPLIFIED_TO_TRADITIONAL(S2T)`          | 简体到繁体           |
| `SIMPLIFIED_TO_HONGKONG(S2HK)`            | 简体到香港繁体         |
| `SIMPLIFIED_TO_JAPANESE(S2JP)`            | 简体到日文           |
| `SIMPLIFIED_TO_TAIWAN(S2TW)`              | 简体到台湾正体         |
| `SIMPLIFIED_TO_TAIWAN_WITH_PHRASE(2TWP)`  | 简体到台湾正体, 带词汇本地化 |
| `HONGKONG_TO_TRADITIONAL(HK2T)`           | 香港繁体到正体         |
| `HONGKONG_TO_SIMPLIFIED(HK2S)`            | 香港繁体到简体         |
| `TAIWAN_TO_SIMPLIFIED(TW2S)`              | 台湾正体到简体         |
| `TAIWAN_TO_TRADITIONAL(TW2T)`             | 台湾正体到繁体         |
| `TAIWAN_TO_SIMPLIFIED_WITH_PHRASE(TW2SP)` | 台湾正体到简体, 带词汇本地化 |
| `TRADITIONAL_TO_HONGKONG(T2HK)`           | 正体到香港繁体         |
| `TRADITIONAL_TO_SIMPLIFIED(T2S)`          | 繁体到简体           |
| `TRADITIONAL_TO_TAIWAN(T2TW)`             | 繁体到台湾正体         |
| `TRADITIONAL_TO_JAPANESE(T2JP)`           | 繁体到日文           |
| `JAPANESE_TO_TRADITIONAL(JP2T)`           | 日文到繁体           |
| `JAPANESE_TO_SIMPLIFIED(JP2S)`            | 日文到简体           |

## 贡献

欢迎提出问题和改进建议，您可以通过 Issue 或 Pull Request 的方式参与贡献。

## 协议

本项目基于 MIT 协议开源。