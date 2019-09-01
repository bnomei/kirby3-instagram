# Kirby 3 Instagram

![Release](https://flat.badgen.net/packagist/v/bnomei/kirby3-instagram?color=ae81ff)
![Stars](https://flat.badgen.net/packagist/ghs/bnomei/kirby3-instagram?color=272822)
![Downloads](https://flat.badgen.net/packagist/dt/bnomei/kirby3-instagram?color=272822)
![Issues](https://flat.badgen.net/packagist/ghi/bnomei/kirby3-instagram?color=e6db74)
[![Build Status](https://flat.badgen.net/travis/bnomei/kirby3-instagram)](https://travis-ci.com/bnomei/kirby3-instagram)
[![Coverage Status](https://flat.badgen.net/coveralls/c/github/bnomei/kirby3-instagram)](https://coveralls.io/github/bnomei/kirby3-instagram) 
[![Maintainability](https://flat.badgen.net/codeclimate/maintainability/bnomei/kirby3-instagram)](https://codeclimate.com/github/bnomei/kirby3-instagram) 
[![Demo](https://flat.badgen.net/badge/website/examples?color=f92672)](https://kirby3-plugins.bnomei.com/instagram) 
[![Gitter](https://flat.badgen.net/badge/gitter/chat?color=982ab3)](https://gitter.im/bnomei-kirby-3-plugins/community) 
[![Twitter](https://flat.badgen.net/badge/twitter/bnomei?color=66d9ef)](https://twitter.com/bnomei)


Kirby 3 Plugin to call Instagram (or any other) API Endpoints

1. [Instagram](https://github.com/bnomei/kirby3-instagram#instagram)
2. [Any API](https://github.com/bnomei/kirby3-instagram#any-api)

## Commercial Usage

This plugin is free but if you use it in a commercial project please consider to 
- [make a donation 🍻](https://www.paypal.me/bnomei/5) or
- [buy me ☕](https://buymeacoff.ee/bnomei) or
- [buy a Kirby license using this affiliate link](https://a.paddle.com/v2/click/1129/35731?link=1170)

## Installation

- unzip [master.zip](https://github.com/bnomei/kirby3-instagram/archive/master.zip) as folder `site/plugins/kirby3-instagram` or
- `git submodule add https://github.com/bnomei/kirby3-instagram.git site/plugins/kirby3-instagram` or
- `composer require bnomei/kirby3-instagram`

## Setup

You can set the token in the config.

**site/config/config.php**
```php
return [
    // other config settings ...
    'bnomei.instagram.token' => 'YOUR-TOKEN-HERE',
];
```

You can also set a callback if you use the [dotenv Plugin](https://github.com/bnomei/kirby3-dotenv).

**site/config/config.php**
```php
return [
    // other config settings ...
    'bnomei.instagram.token' => function() { return env('INSTAGRAM_TOKEN'); },
];
```

## Usage

#### Instagram

**site/templates/default.php**
```php
<?php
    // default. this will cause loading from the config file or set it here...
    $token = null; 
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

> TIP: all `site()->instagram()` function parameters are optional if their value is set in config.

#### Any API

Since you can configure the `api`-url, `endpoint` and `json-root` data element you could actually query any API you want not just Instagram.

```php
$inst = new Bnomei\Instagram([
    'token' => null,
    'api' => 'https://repo.packagist.org/',
    'endpoint' => 'p/bnomei/kirby3-instagram.json',
    'json-root' => 'packages',
]);
$data = $inst->api()['bnomei/kirby3-instagram']['1.2.0']['authors'][0]['name'];
// Bruno Meilick
```

> TIP: since the `params` can be a callback you can forward any data you want and again even from an .env file. Go wild!

## Cache

This plugin does have a cache unless global `debug` options is set or your `$force` the refresh because the instagram api will stop working if you push to may requests in a short period of time.

## Settings

| bnomei.instagram.         | Default        | Description               |            
|---------------------------|----------------|---------------------------|
| expire | `60*24` | in minutes. `0` will never expire (aka forever). |
| token | `null` | you could add a default token |
| api | `https://api.instagram.com/v1` | |
| endpoint | `users/self/media/recent` | you could change default endpoint |
| params | `[]` | you could change default params for api |
| json-root | `data` | node to unwrap in json response |

> TIP: All setting params could be callbacks. Example see [Setup with DotEnv](https://github.com/bnomei/kirby3-instagram#setup).

## Disclaimer

This plugin is provided "as is" with no guarantee. Use it at your own risk and always test it yourself before using it in a production environment. If you find any issues, please [create a new issue](https://github.com/bnomei/kirby3-instagram/issues/new).

## License

[MIT](https://opensource.org/licenses/MIT)

It is discouraged to use this plugin in any project that promotes racism, sexism, homophobia, animal abuse, violence or any other form of hate speech.
