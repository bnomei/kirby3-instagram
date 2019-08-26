<?php

declare(strict_types=1);

namespace Bnomei;

use Kirby\Http\Remote;
use Kirby\Toolkit\A;

final class Instagram
{

    /**
     * @var array
     */
    private $options;

    /**
     * Instagram constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $defaults = [
            'debug' => option('debug'),
            'expires' => option('bnomei.instagram.expires'),
            'token' => option('bnomei.instagram.token'),
            'api' => option('bnomei.instagram.api'),
            'endpoint' => option('bnomei.instagram.endpoint'),
            'params' => option('bnomei.instagram.params'),
            'json-root' => option('bnomei.instagram.json-root'),
        ];
        $this->options = array_merge($defaults, $options);

        foreach ($this->options as $key => $call) {
            if (is_callable($call) && in_array($key, ['expires', 'token', 'api', 'endpoint', 'params', 'json-root'])) {
                $this->options[$key] = $call();
            }
        }

        $this->options['api'] = rtrim(trim($this->options['api']), '/') . '/';

        if ($this->option('debug')) {
            kirby()->cache('bnomei.instagram')->flush();
        }
    }

    /**
     * @param string|null $key
     * @return array
     */
    public function option(?string $key = null)
    {
        if ($key) {
            return A::get($this->options, $key);
        }

        return $this->options;
    }

    /**
     * @param string $url
     * @param string|null $root
     * @return array|null
     */
    public function remoteGet(string $url, ?string $root = 'data'): ?array
    {
        $data = Remote::get($url, ['method' => 'GET'])->content();
        if ($data && $this->isJSON($data)) {
            $data = json_decode($data, true);
            if ($root && is_array($data) && array_key_exists($root, $data)) {
                return $data[$root];
            }
        }
        return [];
    }

    /**
     * @param string|null $token
     * @param string|null $endpoint
     * @param array|null $params
     * @param null $force
     * @return array|null
     */
    public function api(?string $token = null, ?string $endpoint = null, ?array $params = null, $force = null): ?array
    {
        $token = $token ?? $this->option('token');
        $endpoint = $endpoint ?? $this->option('endpoint');
        $endpoint = rtrim(trim($endpoint), '/');
        $force = $force ?? $this->option('debug');

        $data = $this->read($token . $endpoint, $force);
        if ($data) {
            return $data;
        }
        $data = $this->remoteGet($this->url($token, $endpoint, $params), (string) $this->option('json-root'));
        $this->write($token . $endpoint, $data);

        return $data;
    }

    /**
     * @param string|null $token
     * @param string|null $endpoint
     * @param array|null $params
     * @return string
     */
    public function url(?string $token = null, ?string $endpoint = null, ?array $params = null): string
    {
        $url = [
            $this->option('api'),
            $endpoint,
            $token ? "?access_token=" . $token : '',
        ];
        $params = $params ?? $this->option('params');
        $append = $token ? '&' : '?';
        foreach ($params as $key => $val) {
            $url[] = $append . trim($key) . '=' . $val;
            $append = '&';
        }
        return implode('', $url);
    }

    /**
     * @param string $key
     * @return string
     */
    public function cacheId(string $key)
    {
        return implode('-', [
            strval(crc32($key)),
            str_replace('.', '-', kirby()->plugin('bnomei/instagram')->version()),
            kirby()->language() ? kirby()->language()->code() : 'NONE',
        ]);
    }

    /**
     * @param string $key
     * @param array|null $data
     * @return bool
     */
    public function write(string $key, ?array $data): bool
    {
        if ($this->option('debug')) {
            return false;
        }

        return kirby()->cache('bnomei.instagram')->set(
            $this->cacheId($key),
            $data,
            $this->option('expires')
        );
    }

    /**
     * @param string $key
     * @param bool|null $force
     * @return misc|null
     */
    public function read(string $key, ?bool $force = false)
    {
        if ($force || $this->option('debug')) {
            return null;
        }

        return kirby()->cache('bnomei.instagram')->get($this->cacheId($key));
    }

    /**
     * @param string $string
     * @return bool
     */
    public function isJson(string $string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * @param string|null $token
     * @param string|null $endpoint
     * @param array|null $params
     * @param bool|null $force
     * @return array|null
     */
    public static function instagram(?string $token = null, ?string $endpoint = null, ?array $params = null, ?bool $force = null): ?array
    {
        return (new self())->api($token, $endpoint, $params, $force);
    }
}
