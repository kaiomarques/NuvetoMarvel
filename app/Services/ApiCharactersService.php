<?php

namespace App\Services;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\Psr16Adapter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\FavoriteCharacters;

class ApiCharactersService implements ApiCharactersInterface
{
    protected $apiBaseUrl;
    protected $apiKey;
    protected $hashedUrl;

    //portrait_small	      50x75px
    //portrait_medium	      100x150px
    //portrait_xlarge	      150x225px
    //portrait_fantastic	  168x252px
    //portrait_uncanny	    300x450px
    //portrait_incredible	  216x324px

    //standard_small	65x45px
    //standard_medium	100x100px
    //standard_large	140x140px
    //standard_xlarge	200x200px
    //standard_fantastic	250x250px
    //standard_amazing	180x180px

    //landscape_small	120x90px
    //landscape_medium	175x130px
    //landscape_large	190x140px
    //landscape_xlarge	270x200px
    //landscape_amazing	250x156px
    //landscape_incredible	464x261px

    const IMAGE_SIZE = 'standard_fantastic';

    public function __construct(CacheItemPoolInterface $cache)
    {
        $api_public_key     = config('app.api_public_key');
        $api_private_key    = config('app.api_private_key');
        $ts = 1234;
        $hash = md5($ts.$api_private_key.$api_public_key);

        $this->apiBaseUrl = config('app.api_endpoint');
        $this->apiKey = "?ts=".$ts."&apikey=".$api_public_key."&hash=".$hash;
        $this->cache = $cache;
    }

    public function getAllCharacters($limit = 21, $offset = 0)
    {
        $cacheKey = 'character1_' . $limit . '_' . $offset;
        $cacheItem = $this->cache->getItem($cacheKey);

        if (!$cacheItem->isHit()) {
            $url = $this->apiBaseUrl . 'characters'. $this->apiKey . '&limit=' . $limit . '&offset=' . $offset;
            $response = Http::get($url);

            // Verificar se a resposta da API está no formato esperado
            if (!$response->successful()) {
                throw new \RuntimeException('Erro ao obter personagens da API da Marvel.');
            }

            $responseData = $response->json();

            if (!isset($responseData['data']['results'])) {
                throw new \RuntimeException('Resposta da API da Marvel não contém resultados de personagens.');
            }

            $characters = $responseData['data'];

            $cacheItem->set($characters);
            $cacheItem->expiresAfter(3600); // Tempo de vida do cache: 1 hora
            $this->cache->save($cacheItem);
        } else {
            $characters = $cacheItem->get();
        }

        return $this->mapCharacters($characters);
    }

    public function getCharacterById($characterId)
    {
        $response = Http::get($this->apiBaseUrl . 'comics/' . $characterId, [
            'apikey' => $this->apiKey
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Erro ao obter quadrinho da API da Marvel.');
        }

        $responseData = $response->json();

        if (!isset($responseData['data']['results']) || empty($responseData['data']['results'])) {
            throw new \RuntimeException('Quadrinho não encontrado.');
        }

        $character = $responseData['data']['results'][0];

        return $this->mapComic($character);
    }

    protected function mapCharacter($character)
    {
        return [
            'id'          => $character['id'],
            'name'       => $character['name'],
            'image'       => $this->urlImage($character),
            'like'        => $this->getLike($character)
        ];
    }

    private function getLike($character) {
        if (!Auth::check()) 
            return false;

        $like = FavoriteCharacters::where(['id_character' => $character["id"], "id_usuario" => Auth::user()->id])->exists();
        return ($like) ? true : false;
    }

    protected function mapCharacters($characters)
    {
        $dados = array_map(function ($character) {
            return $this->mapCharacter($character);
        }, $characters['results']);

        $resultado = array("dados" => $dados,"total" => $characters['total']);
        return $resultado;
    }

    private function urlImage($character) {
      return $character['thumbnail']['path'].'/'.self::IMAGE_SIZE.'.'.$character['thumbnail']['extension'];
    }
}