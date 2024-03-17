<?php

namespace App\Services;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\Psr16Adapter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

abstract class ApiServiceBase
{
    protected $apiBaseUrl;
    protected $apiKey;
    protected $cache;

    public function __construct(CacheItemPoolInterface $cache, $baseUrl, $apiKey)
    {
        $this->apiBaseUrl = $baseUrl;
        $this->apiKey = $apiKey;
        $this->cache = $cache;
    }

    protected function fetch($url)
    {
        $cacheKey = md5($url);
        $cacheItem = $this->cache->getItem($cacheKey);

        if (!$cacheItem->isHit()) {
            $response = Http::get($url);

            if (!$response->successful()) {
                throw new \RuntimeException('Erro ao obter dados da API.');
            }

            $data = $response->json();

            if (!isset($data['data']['results'])) {
                throw new \RuntimeException('Resposta da API não contém resultados.');
            }

            $cacheItem->set($data);
            $cacheItem->expiresAfter(3600); // Tempo de vida do cache: 1 hora
            $this->cache->save($cacheItem);
        } else {
            $data = $cacheItem->get();
        }

        return $data;
    }
}