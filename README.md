
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status](https://travis-ci.org/czim/laravel-cms-auth-api.svg?branch=master)](https://travis-ci.org/czim/laravel-cms-auth-api)
[![Coverage Status](https://coveralls.io/repos/github/czim/laravel-cms-auth-api/badge.svg?branch=master)](https://coveralls.io/github/czim/laravel-cms-auth-api?branch=master)

# CMS for Laravel - API extension for the Auth Component

This is an optional extension for [the authentication component](https://github.com/czim/laravel-cms-auth) of the CMS.

This is not required for ordinary web-based use of the CMS.
It is an addon that allows OAuth2 API authentication. 

## Version Compatibility

 Laravel             | Package 
:--------------------|:--------
 5.5.x               | 1.5.0+


## Installation

To install this extension, you will need to register the Authenticator component of this package, instead of the default Authenticator. 
Additional service providers must also be registered.


### Install using Composer

```bash
composer require czim/laravel-cms-auth-api
```

### Register the Authenticator

In the `cms-core.php` config file, edit the following key in the `bindings` section:

```php
<?php
    'bindings' => [
        // ...
        Czim\CmsCore\Support\Enums\Component::AUTH        => Czim\CmsAuthApi\Auth\Authenticator::class,
        // ...
    ],
```

It should already be present, simply replace the line (or its value).

### Register the Service Providers

Add the following lines to the `cms-core.php` config file's `providers` section:

```php
<?php
    'providers' => [
        // ...
        Czim\CmsAuthApi\Providers\OAuthSetupServiceProvider::class,
        Czim\CmsAuthApi\Providers\OAuth2ServerServiceProvider::class,
        Czim\CmsAuthApi\Providers\FluentStorageServiceProvider::class,
        // ...
    ],
```


## API Documentation

The documentation for auth component API endpoints: 
https://czim.github.io/laravel-cms-auth


## Authenticating with the CMS API

This package uses [Luca Degasperi's OAuth2 Server package](https://github.com/lucadegasperi/oauth2-server-laravel)
for API authentication, slightly modified to allow it to be used inobtrusively with the CMS.

### Issueing tokens

Logging in, or getting issued an access token may be done using either the `password` or `refresh_token` grant.
Signing in a user by their credentials is done by sending a `POST` request to `/cms-api/auth/issue` with the following data:

```json
{
    "client_id":     "<the OAuth2 client id here>",
    "client_secret": "<the OAuth2 client secret here>",
    "grant_type":    "password",
    "username":      "<your username here>",
    "password":      "<your password here>"
}
```

If you have a refresh token, you can attempt to use it with:

```json
{
    "client_id":     "<the OAuth2 client id here>",
    "client_secret": "<the OAuth2 client secret here>",
    "grant_type":    "refresh_token",
    "refresh_token": "<your refresh token>"
}
```

The server may respond with `422` validation errors for these requests.

### Revoking tokens

Logging out, or revoking tokens, is implemented roughly according to [RFC7009](https://tools.ietf.org/html/rfc7009).

Send a `POST` request to `/cms-api/auth/revoke`, with a valid Authorization header, with the following data, 
to revoke your access token:

```json
{
    "token": "<your access token here>",
    "token_type_hint": "access_token"
}
```

If you want to stay logged in, but only revoke your *refresh* token:

```json
{
    "token": "<your refresh token here>",
    "token_type_hint": "refresh_token"
}
```

Note that, in compliance with the RFC, invalid tokens will be silently ignored.
The server will always respond with a `200 OK` (unless the bearer token fails to authorize).


## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


## Credits

- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/czim/laravel-cms-auth-api.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/czim/laravel-cms-auth-api.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/czim/laravel-cms-auth-api
[link-downloads]: https://packagist.org/packages/czim/laravel-cms-auth-api
[link-author]: https://github.com/czim
[link-contributors]: ../../contributors
