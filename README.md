# World-Text SDK for PHP

[![Build Status](https://img.shields.io/travis/d4h/php-world-text.svg?style=flat-square)](https://travis-ci.org/d4h/php-world-text)
[![Latest Stable Version](https://img.shields.io/packagist/v/d4h/world-text.svg?style=flat-square)](https://packagist.org/packages/d4h/world-text)
[![License](https://img.shields.io/github/license/d4h/php-world-text?style=flat-square)](#license)

PHP SDK for World Text SMS Text Messaging

## Installation

```
composer require d4h/world-text
```

## API Documentation

World Text SMS API is documented here: [World Text HTTP REST API](http://www.world-text.com/docs/interfaces/HTTP/)

## Introduction

### Send an SMS Text Message

```php
$id = 'XXXXXX'; // Your Account ID
$apiKey = 'XXXXXX'; // Your secret API Key

$sms = WorldText\SmsClient::create($id, $apiKey);

try {
    $info = $sms->send('447989000000', 'Example message');
} catch (Exception $e) {
    echo 'Caught exception: ', $e->getMessage(), "\n";
}
```

## Credits

Sponsored by [D4H](https://d4htechnologies.com/).
