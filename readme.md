# Decompresso

Библиотека для извлечения содержимых архивов с поддержкой паролей. 
Она предлагает простое и гибкое решение для работы с архивами.

## Возможности

- **Поддержка паролей**: Работайте с архивами, защищёнными паролем, используя различные способы предоставления паролей.
- **Гибкость обработчиков**: Подключайте и настраивайте обработчики для различных форматов архивов.
- **Интуитивный интерфейс**: Используйте Fluent API для простого конфигурирования и обработки событий успешного или неудачного извлечения файлов.

## Установка

Для установки используйте Composer:

```bash
composer require esplora/decompresso
```

## Использование

Вот пример использования библиотеки для извлечения архива:

```php
use Esplora\Decompresso\Extractor;
use Esplora\Decompresso\Handlers\ZipArchiveHandler;
use Esplora\Decompresso\Providers\ArrayPasswordProvider;

// Создаем провайдер паролей
$passwordProvider = new ArrayPasswordProvider(['123', 'xxx123']);

// Создаем обработчик архива
$archiveHandler = new ZipArchiveHandler();

// Создаем объект Extractor
$extractor = new Extractor();

// Настраиваем Extractor
$extractor->withPasswords($passwordProvider)
          ->withHandler($archiveHandler)
          ->onSuccess(fn($filePath) => $filePath . ' извлечен успешно.')
          ->onFailure(fn($filePath) => 'Не удалось извлечь ' . $filePath);

// Извлекаем архив
$extractor->extract('/path/to/your/archive.zip', '/path/to/extract/to');
```


### Как это работает

#### Провайдеры паролей

Библиотека поддерживает различные провайдеры паролей для работы с защищёнными архивами. 
В примере используется `ArrayPasswordProvider`, который принимает массив паролей.
Вы можете создать свой провайдер, реализуя `PasswordProviderInterface`, например,
`DataBasePasswordProvider` для получения паролей из базы данных и добавления кеширования.

#### Обработчики архивов

Для работы с архивами библиотека использует обработчики. 
В примере используется `ZipArchiveHandler` для ZIP-файлов. 
Вы можете создать собственный обработчик для поддержки других форматов архивов.

