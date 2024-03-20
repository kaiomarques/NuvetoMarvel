<?php
/**
 * Classe abstrata de comunicação com API
 * php version 8.2.0
 *
 * @category Serviço
 * @package  App\Services
 * @author   Kaio Luiz Marques <kaiolmarques@gmail.com>
 * @license  https://opensource.org/license/MIT MIT
 * @link     https://github.com/kaiomarques/
 */
namespace App\Services;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\Psr16Adapter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

/**
 * Classe abstrata para comunicação com APIs.
 * 
 * @category Serviço
 * @package  App\Services
 * @author   Kaio Luiz Marques <kaiolmarques@gmail.com>
 * @license  https://opensource.org/license/MIT MIT
 * @link     https://github.com/kaiomarques/
 */
abstract class ApiServiceBase
{
    protected $apiBaseUrl;
    protected $apiKey;
    protected $cache;

    /**
     * Construtor da classe.
     *
     * @param CacheItemPoolInterface $cache   Pool de cache para armazenamento 
     *                                        de dados em cache.
     * @param string                 $baseUrl URL base da API.
     * @param string                 $apiKey  Chave de acesso à API.
     */
    public function __construct(CacheItemPoolInterface $cache, $baseUrl, $apiKey)
    {
        $this->apiBaseUrl = $baseUrl;
        $this->apiKey = $apiKey;
        $this->cache = $cache;
    }

    /**
     * Busca os dados da API.
     *
     * @param string $url URL da API.
     *
     * @throws \RuntimeException Se houver um erro ao obter os dados da API.
     *
     * @return array Dados obtidos da API.
     */
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
                throw new \RuntimeException(
                    'Resposta da API não contém resultados.'
                );
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