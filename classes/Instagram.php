<?php

namespace Bnomei;

class Instagram
{
    private static $indexname = null;
    private static $cache = null;
    private static function cache(): \Kirby\Cache\Cache
    {
        if (!static::$cache) {
            static::$cache = kirby()->cache('bnomei.instagram');
        }
        // create new index table on new version of plugin
        if (!static::$indexname) {
            static::$indexname = 'index'.str_replace('.', '', kirby()->plugin('bnomei/instagram')->version()[0]);
        }
        return static::$cache;
    }

    public static function flush()
    {
        return static::cache()->flush();
    }

    private static function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public static function api(string $url): ?array
    {
        $data = \Kirby\Http\Remote::get($url, ['method' => 'GET'])->content();
        if ($data && static::isJSON($data)) {
            $data = json_decode($data, true);
            if(array_key_exists('data', $data)) {
                $data = $data['data'];
            } else {
                $data = [];
            }
        } else {
            $data = [];
        }
        return $data;
    }

    public static function instagram(string $token = null, string $endpoint = null, array $params = null, $force = null): ?array
    {
        if (!$token) {
            $token = option('bnomei.instagram.token');
        }
        $token = trim($token);

        if (!$endpoint) {
            $endpoint = option('bnomei.instagram.endpoint');
        }
        $endpoint = rtrim(trim($endpoint), '/');

        if (!$params) {
            $params = option('bnomei.instagram.params');
        }

        if ($force == null && option('debug') && option('bnomei.feed.debugforce')) {
            $force = true;
        }

        $key = md5($token.$endpoint);
        $data = $force ? null : static::cache()->get($key);
        if (!$data) {
            $url  = [
                rtrim(trim(option('bnomei.instagram.api')),'/').'/',
                $endpoint,
                "?access_token=".$token
            ];
            foreach ($params as $k => $v) {
                $url[] = '&'.trim($k).'='.$v;
            }

            $data = static::api(implode('', $url));
            static::cache()->set(
                $key,
                $data,
                option('bnomei.instagram.expires')
            );
        }
        return $data;
    }
}
