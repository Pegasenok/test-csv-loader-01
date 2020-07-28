<?php

use App\Builder\UserBuilder;
use App\Command\RedisCommandDeployer;
use App\Database\DatabaseStorage;
use App\Model\BatchLoadingModel;
use App\Model\FileConductor\FileConductor;
use App\Model\FileConductor\UnsecuredFileConductor;
use App\Model\SearchModel;
use App\Model\UploadActionModel;
use App\Model\UserLoadingModel;
use App\Parser\CsvFileParser;
use App\Repository\UserRepository;
use App\Validation\UserValidation;

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
    return new UploadActionModel(new RedisCommandDeployer($config['redis']), new UnsecuredFileConductor());
})();
$config['UploadActionModelSecured'] = (function () use ($config) {
    return new UploadActionModel(new RedisCommandDeployer($config['redis']), new FileConductor());
})();
$config['UserLoadingModel'] = (function () use ($config): UserLoadingModel {
    return new UserLoadingModel(
        new CsvFileParser(
            new UserBuilder(
                new UserValidation()
            )
        ),
        new BatchLoadingModel(
            new UserRepository($config['database'])
        )
    );
})();

return $config;