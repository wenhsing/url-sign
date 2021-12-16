<?php

namespace Wenhsing\UrlSign;

use InvalidArgumentException;

class UrlSignManager
{
    /**
     * The config instance.
     *
     * @var Config
     */
    protected $config;

    protected $userDrivers = [];

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Driver list.
     *
     * @var array
     */
    protected $drivers = [];

    public function createMd5Driver()
    {
        return new Md5Driver($this->config['md5'] ?? []);
    }

    /**
     * Get driver instance.
     *
     * @author Wenhsing <wenhsing@qq.com>
     *
     * @param string $name
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function driver($name = null)
    {
        $name = $name ?? $this->getDefaultDriver();

        if (is_null($name)) {
            throw new InvalidArgumentException(sprintf('Invalid [%s].', static::class));
        }

        // 当驱动不存在时创建驱动
        if (! isset($this->drivers[$name])) {
            $this->drivers[$name] = $this->createDriver($name);
        }

        return $this->drivers[$name];
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->config['default'] ?? 'md5';
    }

    /**
     * Create dirver instance.
     *
     * @param string $name
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function createDriver($name)
    {
        if (isset($this->extends[$name])) {
            return $this->callExtend($name);
        }

        $name = ucwords(str_replace(['-', '_'], ' ', $name));
        $name = str_replace(' ', '', $name);
        $method = 'create'.$name.'Driver';

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        throw new InvalidArgumentException('Unsupported '.$name);
    }

    protected function callExtend($name)
    {
        return $this->userDrivers[$name]($this->config);
    }

    // 扩展 URL 签名服务
    public function extend($name, Closure $callback)
    {
        $this->userDrivers[$name] = $callback;

        return $this;
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param string $method
     * @param array $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->driver()->$method(...$parameters);
    }
}
