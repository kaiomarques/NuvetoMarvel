<?php

namespace App\Services;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\Psr16Adapter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\FavoriteComics;

class ApiComicService implements ApiComicsInterface
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

    public function getAllComics($limit = 21, $offset = 0)
    {
        $cacheKey = 'comics1_' . $limit . '_' . $offset;
        $cacheItem = $this->cache->getItem($cacheKey);

        if (!$cacheItem->isHit()) {
            $url = $this->apiBaseUrl . 'comics'. $this->apiKey . '&limit=' . $limit . '&offset=' . $offset;

            $response = Http::get($url);

            // Verificar se a resposta da API está no formato esperado
            if (!$response->successful()) {
                throw new \RuntimeException('Erro ao obter quadrinhos da API da Marvel.');
            }

            $responseData = $response->json();

            if (!isset($responseData['data']['results'])) {
                throw new \RuntimeException('Resposta da API da Marvel não contém resultados de quadrinhos.');
            }

            $comics = $responseData['data'];

            $cacheItem->set($comics);
            $cacheItem->expiresAfter(3600); // Tempo de vida do cache: 1 hora
            $this->cache->save($cacheItem);
        } else {
            $comics = $cacheItem->get();
        }

        return $this->mapComics($comics);
    }

    public function getComicById($comicId)
    {
        $response = Http::get($this->apiBaseUrl . 'comics/' . $comicId, [
            'apikey' => $this->apiKey
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Erro ao obter quadrinho da API da Marvel.');
        }

        $responseData = $response->json();

        if (!isset($responseData['data']['results']) || empty($responseData['data']['results'])) {
            throw new \RuntimeException('Quadrinho não encontrado.');
        }

        $comic = $responseData['data']['results'][0];

        return $this->mapComic($comic);
    }

    protected function mapComic($comic)
    {
        return [
            'id'          => $comic['id'],
            'title'       => $comic['title'],
            'image'       => $this->urlImage($comic),
            'creators'    => $this->creators($comic),
            'like'        => $this->getLike($comic)
        ];
    }

    private function getLike($comic) {
        if (!Auth::check()) 
            return false;

        $like = FavoriteComics::where(['id_comic' => $comic["id"], "id_usuario" => Auth::user()->id])->exists();
        return ($like) ? true : false;
    }

    protected function mapComics($comics)
    {
        $dados = array_map(function ($comic) {
            return $this->mapComic($comic);
        }, $comics['results']);

        $resultado = array("dados" => $dados,"total" => $comics['total']);
        return $resultado;
    }

    private function urlImage($comic) {
      return $comic['thumbnail']['path'].'/'.self::IMAGE_SIZE.'.'.$comic['thumbnail']['extension'];
    }

    private function creators($comic) {
      $nomes = 'Autor não cadastrado';
      if(isset($comic["creators"]["items"]) && is_array($comic["creators"]["items"]) && count($comic["creators"]["items"]) > 0) {
        $nomes = array_column(array_slice($comic["creators"]['items'], 0, 2), 'name');
        $nomes = implode(', ', $nomes);
      }
      return $nomes;
    }
}