<?php

use App\Command\CommandRegistry;
use App\Worker\RedisCommandListener;

require dirname(__DIR__).'/vendor/autoload.php';

set_time_limit(0);

// todo inline redis initialization
$redis = new \Redis();
$redis->connect('redis');
$redis->auth($_ENV['REDIS_PASS']);

$listener = new RedisCommandListener($redis, new CommandRegistry());
$listener->listen();