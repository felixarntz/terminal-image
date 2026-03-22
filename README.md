# Terminal Image (PHP)

A PHP port of the popular [JavaScript library of the same name](https://www.npmjs.com/package/terminal-image), enabling the display of images directly in the terminal.

## Installation

```bash
composer require felixarntz/terminal-image
```

## Usage

```php
use FelixArntz\TerminalImage\TerminalImage;

echo TerminalImage::file('unicorn.jpg');
```

## License

MIT
