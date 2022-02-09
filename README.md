# Builder API

---
A Laravel package for querying the Builder API

## Installation

You can install the package via composer:

```bash
composer require rs/builder-api
```

## Configuration

By default, the API client will attempt to find settings from the following environment variables

```bash
# .env

BUILDER_SITE_NAME=sitename # the name of the site in Builder
BUILDER_PUBLISHED=true # whether to query the published or unpublished data of the Builder site (defaults to true if not defined)
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
    'site'       => 'sitename',
    'published'  => true,
    'user'       => 'builderuser',
    'password'   => 'key-123',
];
```

## Authorisation

If you provide both a user and password then basic authentication will be attempted.

Alternatively you can provide just a user - this will add an `X-USER` header to the request. This will only work if your
application lives on the same network as the Builder instance

## Usage

You can use the `BuilderApi` facade to create a `PendingRequest`. You can overwrite any configuration using the fluent
methods available on this object.

```php
$pendingRequest = BuilderApi::new();
$pendingRequest->forSite('sitename')
    ->withAuth('builderuser', 'key-123')
    ->unpublished()
    ->get('apiPages');
```

To make a GET request provide the Builder macro name and optionally provide parameters as a second argument.

```php
 $response = $pendingRequest->forSite('sitename')->get('apiPages', ['foo', 'bar']);
```

You can also make a request directly with the facade. This will make use of the configuration defined in your config
file or environment variables

```php
$response = BuilderApi::get('apiPages'); // returns BuilderResponse object
```

If you don't want to use the facade, you can type-hint the `BuilderRequestFactory` class in your constructor methods or use `app()->make()`

```php
use RedSnapper\Builder\BuilderRequestFactory;

    public function __construct(BuilderRequestFactory $factory)
    {
        $this->factory = $factory;
        
        # or...
        $factory = app()->make(BuilderRequestFactory::class);
        
        $pendingRequest = $factory->new();
        $response = $pendingRequest->get('apiPages');
    }
```

When a request is made a `BuilderResponse` object is returned, this provides
methods for retrieving the Builder data or Builder error messages on a failed request.

```php
 $data = $response->data(); # get json decoded data
 
 if($response->failed()) {
    $errorMsg = $response->errorMsg();
 }
```

Below is the full list of methods provided to help you analyse the response
```php
$response->data();

$response->successful();

$response->failed();

$response->errorMsg(); # error msg provided by Builder

$response->status(); # status code of the response

$response->throw(); # throw an exception if a server or client error occurred

$response->getResponse(); # get the underlying 'Illuminate\Http\Client\Response' object
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
