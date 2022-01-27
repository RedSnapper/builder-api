# Builder API

---
A Laravel package for querying the Builder API

## Installation

You can install the package via composer:

```bash
composer require rs/builder-api
```

By default, the API client will attempt to find settings from the following environment variables

```bash
# .env

BUILDER_SITE_NAME=sitename # the name of the site in Builder
BUILDER_PREVIEW=false # whether to query the 'preview' version of the Builder site or not
BUILDER_API_USER=builderuser 
BUILDER_API_KEY=key-123
```

Alternatively, you can publish the config file and define your settings there:

```bash
php artisan vendor:publish --provider="RedSnapper\Builder\BuilderApiServiceProvider" --tag="config"
```

``` php
# config/builder-api.php

return [
    'site'     => 'sitename',
    'preview'  => false,
    'user'     => 'builderuser',
    'password' => 'key-123',
];
```

## Usage

You can type-hint the `BuilderClient` class in your constructor methods or use `app()->make()`

```php
use RedSnapper\Builder\BuilderClient;

    public function __construct(BuilderClient $client)
    {
        $this->client = $client;
        
        # or...
        $client = app()->make(BuilderClient::class);
    }
```
You can modify the client settings with the setter methods:
```php
  $client->setAuth('user', 'key');
  $client->setSiteName('sitename');
  $client->setPreview(true);
```

To make a GET request provide the Builder macro name and optionally provide parameters as a second argument.

```php
 $response = $client->get('apiPages', ['foo', 'bar']);
```

A `BuilderResponse` object will be returned which extends the `Illuminate\Http\Client\Response` class and provides methods for retrieving the Builder data or Builder error messages on a failed request.
```php
 $data = $response->data(); # get json decoded data
 
 if($response->failed()) {
    $errorMsg = $response->builderErrorMsg();
 }
```

## Testing

```bash
vendor/bin/phpunit
```

## Changelog

Please see [CHANGELOG](CHANGELOG.MD) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.MD) for details.

## License

The MIT License (MIT). Please see [Licence File](LICENCE.MD) for more information.
