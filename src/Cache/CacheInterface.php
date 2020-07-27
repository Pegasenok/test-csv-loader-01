<?php

namespace App\Cache;

interface CacheInterface
{
    public function get(string $key, callable $callback);
    public function del(string $key);
}