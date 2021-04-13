## Introduction

Checkpoint provides an expressive, fluent interface to [Tencent Face ID](https://cloud.tencent.com/product/faceid) services.

## Documentation

### Installation

To get started with Checkpoint, use the Composer package manager to add the package to your project's dependencies:

```shell
composer require lizhineng/checkpoint-tencent
```

### Configuration

Before using Checkpoint, you will need to add credentials for Tencent Cloud. The credentials should be placed in your application's `config/services.php` configuration file, and should use the key `checkpoint`, the structure is as following:

```php
<?php

// in config/services.php

return [

    // ...

    'checkpoint' => [
        'key' => env('TENCENT_CLOUD_ACCESS_KEY'),
        'secret' => env('TENCENT_CLOUD_ACCESS_SECRET'),
    ],

    // ...
    
];
```

### Using for WeChat Mini Program

Before you can use the service in Mini Program, first you need to [apply a merchant id](https://cloud.tencent.com/document/product/1007/54116) from Tencent Cloud console.

```php
use Zhineng\Checkpoint\Tencent\Checkpoint;

$merchantId = 'your-merchant-id';
Checkpoint::verifyViaEid($merchantId)->request();
```

You can also send the passthrough data to the request, and it will send it back to Mini Program when the user completed the verification process.

```php
Checkpoint::verifyViaEid('your-merchant-id')
    ->withMetadata(['return_to' => '/pages/home/home'])
    ->request();
```

When the user has completed the identity verification process in the Mini Program, you'll have the token which you can use it to get the verification result.

```php
use Zhineng\Checkpoint\Tencent\Checkpoint;

Checkpoint::resultViaEid()->get('your-token-from-mini-program');
```
