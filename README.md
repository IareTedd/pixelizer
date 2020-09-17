# Pixelizer
Pixelizer is a PHP library that allows you to convert any data into a PNG picture and decode it back into the original data.

## Installation
Use the package manager [composer](https://getcomposer.org/) to install Pixelizer.

```bash
composer require iaretedd/pixelizer
composer install
```

## Getting Started

### Encode a file into a picture
```php
require 'vendor/autoload.php';

$pixelizer = new Pixelizer\Pixelizer();
$pixelizer->setData(file_get_contents('file_to_encode.exe'));
$pixelizer->encode('encoded.png');
```

### Decode a file from a picture
```php
require 'vendor/autoload.php';

$pixelizer = new Pixelizer\Pixelizer();
$pixelizer->loadImage('encoded.png');
$pixelizer->decode('original_file.exe');
```

## Tests
To run unit tests:
```bash
./vendor/bin/phpunit
```

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](https://choosealicense.com/licenses/mit/)