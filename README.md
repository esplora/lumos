# <img src=".github/logo.svg?sanitize=true" width="32" height="32" alt="Lumos"> Lumos

[![Tests](https://github.com/esplora/decompresso/actions/workflows/phpunit.yml/badge.svg)](https://github.com/esplora/decompresso/actions/workflows/phpunit.yml)
[![Quality Assurance](https://github.com/esplora/lumos/actions/workflows/quality.yml/badge.svg)](https://github.com/esplora/lumos/actions/workflows/quality.yml)
[![Coding Guidelines](https://github.com/esplora/lumos/actions/workflows/php-cs-fixer.yml/badge.svg)](https://github.com/esplora/lumos/actions/workflows/php-cs-fixer.yml)

Lumos is a library that provides a interface for removing passwords from protected documents and archives (extracting
content), making these tasks simple and accessible.


###### What can I use this for?

- Unlocking password-protected documents and archives before data extraction with tools like [Apache Tika](https://tika.apache.org/).
- Online services for removing or attempting password recovery, such as through brute force.

## External Dependencies

Lumos relies on the following third-party tools for specific operations.
Each adapter is provided out of the box in the `Esplora\Lumos\Adapters\*` namespace:

| **File Type**     | **Tool**                                                         | **Adapter Class**         |
|-------------------|------------------------------------------------------------------|---------------------------|
| PDF               | [qpdf](https://github.com/qpdf/qpdf)                             | QpdfAdapter               |
| Microsoft Office  | [msoffcrypto-tool](https://github.com/nolze/msoffcrypto-tool)    | MSOfficeCryptoToolAdapter |
| Archive (ZIP, 7z) | [7-zip](https://www.7-zip.org/)                                  | SevenZipAdapter           |

## Installation

Install the library using Composer:

```bash
composer require esplora/lumos
```

## Usage

To get started, create an instance of the `Extractor` class and add the necessary adapters for file formats. The example
below demonstrates using `SevenZipAdapter` for archive, but you can add your own adapters:

```php
use Esplora\Lumos\Extractor;
use Esplora\Lumos\Adapters\SevenZipAdapter;

$lumos = Extractor::make()
    ->withAdapters([
        new SevenZipAdapter(),
    ])
    ->extract('/path/to/your/archive.zip');

$lumos->isSuccessful(); // true
$lumos->attempts(); // 1
```

> [!NOTE]
> When multiple adapters are suitable for a given file, the first adapter in the list will be selected.


## Handling Password-Protected Files

To work with password-protected documents, add a password provider. The example below uses `ArrayPasswordProvider`,
which accepts an array of passwords.

```php
use Esplora\Lumos\Extractor;
use Esplora\Lumos\Adapters\SevenZipAdapter;
use Esplora\Lumos\Providers\ArrayPasswordProvider;

$passwords = new ArrayPasswordProvider([
    'qwerty',
    'xxx123',
]);

Extractor::make()
    ->withAdapters([
        new SevenZipAdapter(),
    ])
    ->withPasswords($passwords)
    ->extract('/path/to/your/archive.zip', '/path/to/save/to')
    ->isSuccessful(); // false
```

If needed, you can create your own password provider by implementing the `PasswordProviderInterface`.

> [!TIP]
> If you don’t have a password database but want to try all possible combinations, you can
> use [SecLists](https://github.com/danielmiessler/SecLists/tree/master/Passwords) as a source of popular passwords for
> brute-forcing.


## Extending File Support

Lumos allows you to easily add support for new file types by creating custom adapters.
To do so, implement a class that conforms to the `Esplora\Lumos\Contracts\AdapterInterface`.

Example of a custom adapter implementation:

```php
namespace Esplora\Lumos\Adapters;

use Esplora\Lumos\Contracts\AdapterInterface;
use Esplora\Lumos\Contracts\PasswordProviderInterface;
use Esplora\Lumos\Results\SummaryInterface;

class CustomAdapter implements AdapterInterface
{
    /**
     * Checks if the adapter supports the given file.
     */
    public function canSupport(string $filePath): bool
    {
        return str_ends_with($filePath, '.custom');
    }

    /**
     * Checks if the environment is properly configured.
     */
    public function isSupportedEnvironment(): bool
    {
        return true; // Check external dependencies here
    }

    /**
     * Extracts the content to the specified directory.
     */
    public function extract(string $filePath, string $destination, PasswordProviderInterface $passwords): SummaryInterface
    {
        // Implement extraction logic here
    }
}
```

The library provides common tools to check if a specific file is supported using built-in traits:

To check based on the file's MIME type, use the trait `SupportsMimeTypes`:

```php
use Esplora\Lumos\Concerns\SupportsMimeTypes;

class CustomAdapter implements AdapterInterface
{
    use SupportsMimeTypes;

    /**
     * Returns a list of supported MIME types.
     *
     * @return array<string> An array of supported MIME types.
     */
    protected function supportedMimeTypes(): array
    {
        return [
            'application/pdf', // Support for PDF files
            'image/jpeg',      // Support for JPEG images
        ];
    }

    // ...
}
```

To check based on file extensions, use the trait `SupportsFileExtensions`:

```php
use Esplora\Lumos\Concerns\SupportsFileExtensions;

class CustomAdapter implements AdapterInterface
{
    use SupportsFileExtensions;

    /**
     * Returns a list of allowed file extensions.
     *
     * @return array<string> An array of allowed extensions.
     */
    protected function allowedExtensions(): array
    {
        return [
            'txt',  // Support for text files
            'zip',  // Support for ZIP archives
        ];
    }

    // ...
}
```


## Testing

Testing an application that depends on other services can be challenging, but this should not prevent you from
contributing to the project.

For adapters that depend on executable files, you can pass the path via the constructor:

```php
use Esplora\Lumos\Adapters\SevenZipAdapter;

new SevenZipAdapter('/usr/bin/7z'),
```

For convenience, we also support using environment variables from a `.env` file to store paths to dependency executables
in one place. To do this, create a `.env` file at the root of your project and add the environment variables as shown in
the `.env.example`.

> [!WARNING]  
> Environment variables from the `.env` file will be loaded only for local testing and are added solely for the
> convenience of developing this package.
