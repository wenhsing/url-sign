<?php

namespace Wenhsing\UrlSign;

class Md5Driver extends Driver
{
    // 排除
    protected $except = [];

    // 时间误差
    protected $timeError = 300;

    // 密钥
    protected $secretKey = '';

    // 时间
    protected $tsField = 'timestamp';

    // 签名字段
    protected $signField = 'sign';

    public function __construct(array $config = [])
    {
        $this->except = $config['except'] ?? $this->except;
        $this->timeError = $config['timeError'] ?? $this->timeError;
        $this->secretKey = $config['secretKey'] ?? $this->secretKey;
        $this->tsField = $config['tsField'] ?? $this->tsField;
        $this->signField = $config['signField'] ?? $this->signField;
    }

    public function verify(string $uri, array $query)
    {
        if ($this->inExceptArray($uri, $this->except)
            || ($this->verifyTime((int)($query[$this->tsField] ?? 0), $this->timeError)
                && $this->signMatch($query)
            )
        ) {
            return true;
        }

        return false;
    }

    /**
     * 签名验证
     *
     * @author Wenhsing <wenhsing@qq.com>
     *
     * @return bool
     */
    public function signMatch(array $data)
    {
        ksort($data);
        $sign = '';
        foreach ($data as $k => $v) {
            if ($this->signField !== $k) {
                $sign .= $k.$v;
            }
        }
        if (md5($sign.$this->secretKey) === $data[$this->signField] ?? null) {
            return true;
        }

        return false;
    }
}
