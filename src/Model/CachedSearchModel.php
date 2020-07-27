<?php


namespace App\Model;


use App\Cache\CacheInterface;

class CachedSearchModel implements UserSearchModelInterface
{
    /**
     * @var UserSearchModelInterface
     */
    private UserSearchModelInterface $model;
    /**
     * @var CacheInterface
     */
    private CacheInterface $cache;

    /**
     * CachedSearchModel constructor.
     * @param UserSearchModelInterface $model
     * @param CacheInterface $cache
     */
    public function __construct(UserSearchModelInterface $model, CacheInterface $cache)
    {
        $this->model = $model;
        $this->cache = $cache;
    }

    public function findByFioOrEmail(string $query)
    {
        return $this->cache->get($this->getQueryCacheKey($query), fn() => $this->model->findByFioOrEmail($query));
    }

    public function invalidateKey(string $query)
    {
        return $this->cache->del($this->getQueryCacheKey($query));
    }

    /**
     * @param string $query
     * @return string
     */
    private function getQueryCacheKey(string $query): string
    {
        return 'search:query:' . md5($query);
    }
}