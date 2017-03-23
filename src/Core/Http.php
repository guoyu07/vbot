<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) pei.greet <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Hanson\Vbot\Core;

use GuzzleHttp\Client as HttpClient;

class Http
{
    public static $instance;

    protected $client;

    /**
     * @return Http
     */
    public static function getInstance()
    {
        if (!static::$instance) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    public function get($url, array $query = [], array $options = [])
    {
        if ($query) {
            $options['query'] = $query;
        }

        $options['connect_timeout'] = 60;

        return $this->request($url, 'GET', $options);
    }

    public function post($url, $query = [], $array = false)
    {
        $key = is_array($query) ? 'form_params' : 'body';

        $content = $this->request($url, 'POST', [$key => $query]);

        return $array ? json_decode($content, true) : $content;
    }

    public function json($url, $params = [], $array = false, $extra = [])
    {
        $params = array_merge(['json' => $params], $extra);

        $content = $this->request($url, 'POST', $params);

        return $array ? json_decode($content, true) : $content;
    }

    public function setClient(HttpClient $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Return GuzzleHttp\Client instance.
     *
     * @return \GuzzleHttp\Client
     */
    public function getClient()
    {
        if (!($this->client instanceof HttpClient)) {
            $this->client = new HttpClient(['cookies' => true]);
        }

        return $this->client;
    }

    /**
     * @param $url
     * @param string $method
     * @param array  $options
     *
     * @return string
     */
    public function request($url, $method = 'GET', $options = [])
    {
        $response = $this->getClient()->request($method, $url, $options);

        return $response->getBody()->getContents();
    }
}
