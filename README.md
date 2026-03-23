# Terminal Image (PHP)

A PHP port of the popular [JavaScript library of the same name](https://www.npmjs.com/package/terminal-image), enabling the display of images directly in the terminal using ANSI escape sequences.

- Supports JPEG, PNG, WebP, and any other format supported by PHP's GD library.
- Works in any terminal that supports colors.
- Requires PHP >= 7.4 with the GD extension.

## Installation

```bash
composer require felixarntz/terminal-image
```

## Usage

From a file path:

```php
echo TerminalImage::file('unicorn.jpg');
```

From a binary string (e.g. an HTTP response body):

```php
$data = file_get_contents('https://example.com/unicorn.jpg');
echo TerminalImage::buffer($data);
```

### Scaling

By default, images are scaled to fit within the terminal window while preserving their aspect ratio.

Set a specific width in columns; height is derived from the aspect ratio:

```php
echo TerminalImage::file('unicorn.jpg', ['width' => 40]);
```

Set a specific height in rows; width is derived from the aspect ratio:

```php
echo TerminalImage::file('unicorn.jpg', ['height' => 20]);
```

Use a percentage of the terminal size:

```php
echo TerminalImage::file('unicorn.jpg', ['width' => '50%']);
```

Set both dimensions; the image is scaled to fit within the box:

```php
echo TerminalImage::file('unicorn.jpg', ['width' => 60, 'height' => 20]);
```

Disable aspect ratio preservation to stretch to exact dimensions:

```php
echo TerminalImage::file('unicorn.jpg', [
    'width' => 60,
    'height' => 20,
    'preserveAspectRatio' => false,
]);
```

## API

### `TerminalImage::file(string $filePath, array $options = []): string`

Renders an image from a file path as a string of ANSI escape sequences.

- `$filePath` — Absolute or relative path to the image file.
- `$options` — Optional associative array of options (see below).

Throws `InvalidArgumentException` if the file does not exist, cannot be read, or contains invalid image data.

### `TerminalImage::buffer(string $data, array $options = []): string`

Renders an image from a binary string as a string of ANSI escape sequences.

- `$data` — Binary string of image data.
- `$options` — Optional associative array of options (see below).

Throws `InvalidArgumentException` if the data is not a valid image.

### Options

| Key                    | Type               | Default | Description                                                                                    |
|------------------------|--------------------|---------|------------------------------------------------------------------------------------------------|
| `width`                | `int` or `string`  | —       | Target width in columns, or a percentage string (e.g. `'50%'`) relative to the terminal width. |
| `height`               | `int` or `string`  | —       | Target height in rows, or a percentage string (e.g. `'50%'`) relative to the terminal height.  |
| `preserveAspectRatio`  | `bool`             | `true`  | Whether to preserve the aspect ratio when both `width` and `height` are provided.              |

When neither `width` nor `height` is provided, the image is scaled to fit within the terminal dimensions.

## License

MIT
