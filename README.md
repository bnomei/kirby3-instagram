# Kirby 3 Instagram

![Release](https://flat.badgen.net/packagist/v/bnomei/kirby3-instagram?color=ae81ff)
![Stars](https://flat.badgen.net/packagist/ghs/bnomei/kirby3-instagram?color=272822)
![Downloads](https://flat.badgen.net/packagist/dt/bnomei/kirby3-instagram?color=272822)
![Issues](https://flat.badgen.net/packagist/ghi/bnomei/kirby3-instagram?color=e6db74)
[![Build Status](https://flat.badgen.net/travis/bnomei/kirby3-instagram)](https://travis-ci.com/bnomei/kirby3-instagram)
[![Coverage Status](https://flat.badgen.net/coveralls/c/github/bnomei/kirby3-instagram)](https://coveralls.io/github/bnomei/kirby3-instagram) 
[![Demo](https://flat.badgen.net/badge/website/examples?color=f92672)](https://kirby3-plugins.bnomei.com/instagram) 
[![Gitter](https://flat.badgen.net/badge/gitter/chat?color=982ab3)](https://gitter.im/bnomei-kirby-3-plugins/community) 
[![Twitter](https://flat.badgen.net/badge/twitter/bnomei?color=66d9ef)](https://twitter.com/bnomei)


Kirby 3 Plugin to call Instagram API Endpoints

## Commercial Usage

This plugin is free but if you use it in a commercial project please consider to 
- [make a donation 🍻](https://www.paypal.me/bnomei/3) or
- [buy me ☕](https://buymeacoff.ee/bnomei) or
- [buy a Kirby license using this affiliate link](https://a.paddle.com/v2/click/1129/35731?link=1170)

## Installation

- unzip [master.zip](https://github.com/bnomei/kirby3-instagram/archive/master.zip) as folder `site/plugins/kirby3-instagram` or
- `git submodule add https://github.com/bnomei/kirby3-instagram.git site/plugins/kirby3-instagram` or
- `composer require bnomei/kirby3-instagram`

## Usage

### Config

You can set the token in the config.

```php
return [
    // other config settings ...
    'bnomei.instagram.token' => 'YOUR-TOKEN-HERE',
];
```

### Template

```php
<?php
    $token = null; // default. this will cause loading from the config file or set it here...
    $token = 'YOUR-TOKEN-HERE';
    $endpoint = 'users/self/media/recent';
    $params = [
        'count' => 4
    ];
    $force = null; // default. this will cause refresh on global debug == true
    // $force = true; // always force refresh
    foreach(site()->instagram($token, $endpoint, $params, $force) as $data) {
        echo Kirby\Toolkit\Html::img(
            $data['images']['standard_resolution']['url']
        );
    }
```

## Cache

This plugin does have a cache unless global `debug` options is set or your `$force` the refresh because the instagram api will stop working if you push to may requests in a period of short time.

> TIP: all `site()->instagram()` function parameters are optional if their value is set in config.

## Settings

**debugforce**
- default: `true` will only write but never read cache in debug mode

**expire**
- default: `60*24` in minutes. `0` will never expire (aka forever).

**token**
- default: `null` you could add a default token

**api**
- default: `https://api.instagram.com/v1`

**endpoint**
- default: `users/self/media/recent` you could change default endpoint

**params**
- default: `[]` you could change default params for api

## Disclaimer

This plugin is provided "as is" with no guarantee. Use it at your own risk and always test it yourself before using it in a production environment. If you find any issues, please [create a new issue](https://github.com/bnomei/kirby3-instagram/issues/new).

## License

[MIT](https://opensource.org/licenses/MIT)

It is discouraged to use this plugin in any project that promotes racism, sexism, homophobia, animal abuse, violence or any other form of hate speech.
