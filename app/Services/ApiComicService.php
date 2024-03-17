<?php

namespace App\Services;

use App\Models\FavoriteComics;
use Psr\Cache\CacheItemPoolInterface;
use Illuminate\Support\Facades\Auth;

class ApiComicService extends ApiServiceBase implements ApiComicsInterface
{
    const IMAGE_SIZE = 'standard_fantastic';

    public function __construct(CacheItemPoolInterface $cache)
    {
        $api_public_key = config('app.api_public_key');
        $api_private_key = config('app.api_private_key');
        $ts = 1234;
        $hash = md5($ts . $api_private_key . $api_public_key);
        $baseUrl = config('app.api_endpoint') . 'comics';
        $apiKey = "?ts=" . $ts . "&apikey=" . $api_public_key . "&hash=" . $hash;

        parent::__construct($cache, $baseUrl, $apiKey);
    }

    public function getAllComics($limit = null, $offset = null)
    {
        $url = $this->apiBaseUrl . $this->apiKey;

        if ($limit !== null && $offset !== null) {
            $url .= '&limit=' . $limit . '&offset=' . $offset;
        }

        return $this->mapComics($this->fetch($url));
    }

    protected function mapComic($comic)
    {
        return [
            'id' => $comic['id'],
            'title' => $comic['title'],
            'image' => $this->urlImage($comic),
            'creators' => $this->creators($comic),
            'like' => $this->getLike($comic)
        ];
    }

    private function getLike($comic)
    {
        if (!Auth::check())
            return false;

        $like = FavoriteComics::where(['id_comic' => $comic["id"], "id_usuario" => Auth::user()->id])->exists();
        return ($like) ? true : false;
    }

    protected function mapComics($comics)
    {
        $dados = array_map(function ($comic) {
            return $this->mapComic($comic);
        }, $comics["data"]['results']);

        $resultado = array("dados" => $dados, "total" => $comics['data']['total']);
        return $resultado;
    }

    private function urlImage($comic)
    {
        return $comic['thumbnail']['path'] . '/' . self::IMAGE_SIZE . '.' . $comic['thumbnail']['extension'];
    }

    private function creators($comic)
    {
        $nomes = 'Autor não cadastrado';
        if (isset($comic["creators"]["items"]) && is_array($comic["creators"]["items"]) && count($comic["creators"]["items"]) > 0) {
            $nomes = array_column(array_slice($comic["creators"]['items'], 0, 2), 'name');
            $nomes = implode(', ', $nomes);
        }
        return $nomes;
    }
}