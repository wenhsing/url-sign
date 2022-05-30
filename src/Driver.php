<?php

namespace Wenhsing\UrlSign;

abstract class Driver
{
    abstract public function verify(string $url, array $query);

    /**
     * 判断是否在排除的路径中
     *
     * @author Wenhsing <wenhsing@qq.com>
     * @param string $uri
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
     * @param int $timestamp
     * @param int $error
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
