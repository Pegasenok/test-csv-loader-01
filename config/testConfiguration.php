<?php

use App\Command\RedisCommandDeployer;
use App\Database\DatabaseStorage;
use App\Model\SearchModel;
use App\Model\UploadActionModel;
use App\Repository\UserRepository;

$config = [];
$config['redis'] = (function () {
    $redis = new \Redis();
    $redis->connect('redis');
    $redis->auth($_ENV['REDIS_PASS']);
    $redis->select(10);
    return $redis;
})();
$config['cache'] = (function () use ($config) {
    return new App\Cache\RedisCache($config['redis']);
})();
$config['database'] = (function () {
    return new DatabaseStorage($_ENV['DATABASE_URL']);
})();
$config['SearchModel'] = (function () use ($config) {
    return new SearchModel(
        new UserRepository(
            $config['database']
        )
    );
})();
$config['UploadActionModel'] = (function () use ($config) {
    return new UploadActionModel(new RedisCommandDeployer($config['redis']));
})();

return $config;