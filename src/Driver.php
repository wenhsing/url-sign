<?php

namespace Wenhsing\UrlSign;

abstract class Driver
{
    abstract public function verify(string $url);

    protected function parser(string $url)
    {
        preg_match(
            '/^(?:(http|https|ftp)\:\/\/)?([A-Za-z0-9\.\-]+)?(?:\:(\d+))?([^\?\#]*)?(?:\?([^\?\#]*))?(?:\#(.*))?$/',
            $url,
            $matches
        );

        $query = [];
        $queryArr = explode('&', $matches[5] ?? '');
        foreach ($queryArr as $v) {
            if (!empty($v)) {
                list($queryKey, $queryValue) = explode('=', $v);
                $query[$queryKey] = $queryValue;
            }
        }

        return [
            'url' => $matches[0] ?? $url,
            'scheme' => $matches[1] ?? '',
            'host' => $matches[2] ?? '',
            'port' => $matches[3] ?? '80',
            'path' => $matches[4] ?? '/',
            'query' => $query,
            'hash' => $matches[6] ?? '',
        ];
    }

    /**
     * @author Wenhsing <wenhsing@qq.com>
     *
     * @param string $uri
     *
     * @return bool
     */
    public function inExceptArray($uri, $arr)
    {
        $except = array_map(function ($i) {
            if ('/' !== $i) {
                return trim($i, '/');
            }
            return $i;
        }, $arr);

        if ($this->strMatch($except, $uri)) {
            return true;
        }

        return false;
    }

    /**
     * 判断用户请求是否在对应时间范围.
     *
     * @author Wenhsing <wenhsing@qq.com>
     *
     * @param int $timestamp
     *
     * @return bool
     */
    public function verifyTime($timestamp, $error)
    {
        $lfTime = time() - $error;
        $rfTime = time() + $error;
        if ($timestamp >= $lfTime && $timestamp <= $rfTime) {
            return true;
        }

        return false;
    }

    protected function strMatch(array $datas, $value)
    {
        foreach ($datas as $v) {
            if ($v == $value) {
                return true;
            }
            $v = preg_quote($v, '#');
            $v = str_replace('\*', '.*', $v);
            if (preg_match('#^'.$v.'\z#u', $value) > 0) {
                return true;
            }
        }

        return false;
    }
}
