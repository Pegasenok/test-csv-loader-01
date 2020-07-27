<?php


namespace App\Cache;


class RedisCache implements CacheInterface
{
    const DEFAULT_TIMEOUT = 300;
    private \Redis $redis;

    /**
     * RedisCache constructor.
     * @param \Redis $redis
     */
    public function __construct(\Redis $redis)
    {
        $this->redis = $redis;
    }

    public function get(string $key, callable $callback)
    {
        $result = $this->redis->get($key);
        if ($result === false) {
            $result = call_user_func($callback);
        }
        $this->redis->set($key, $result, self::DEFAULT_TIMEOUT);
        return $result;
    }

    public function del(string $key)
    {
        $this->redis->del($key);
    }

}