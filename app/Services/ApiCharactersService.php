<?php

namespace App\Services;

use App\Models\FavoriteCharacters;
use Psr\Cache\CacheItemPoolInterface;
use Illuminate\Support\Facades\Auth;

class ApiCharactersService extends ApiServiceBase implements ApiCharactersInterface
{
    const IMAGE_SIZE = 'standard_fantastic';

    public function __construct(CacheItemPoolInterface $cache)
    {
        $api_public_key = config('app.api_public_key');
        $api_private_key = config('app.api_private_key');
        $ts = 1234;
        $hash = md5($ts . $api_private_key . $api_public_key);
        $baseUrl = config('app.api_endpoint') . 'characters';
        $apiKey = "?ts=" . $ts . "&apikey=" . $api_public_key . "&hash=" . $hash;

        parent::__construct($cache, $baseUrl, $apiKey);
    }

    public function getAllCharacters($limit = null, $offset = null)
    {
        $url = $this->apiBaseUrl . $this->apiKey;

        if ($limit !== null && $offset !== null) {
            $url .= '&limit=' . $limit . '&offset=' . $offset;
        }

        return $this->mapCharacters($this->fetch($url));
    }

    protected function mapCharacter($character)
    {
        return [
            'id' => $character['id'],
            'name' => $character['name'],
            'image' => $this->urlImage($character),
            'like' => $this->getLike($character)
        ];
    }

    private function getLike($character)
    {
        if (!Auth::check()) {
            return false;
        }

        $like = FavoriteCharacters::where(['id_character' => $character["id"], "id_usuario" => Auth::user()->id])->exists();
        return ($like) ? true : false;
    }

    protected function mapCharacters($characters)
    {
        $dados = array_map(function ($character) {
            return $this->mapCharacter($character);
        }, $characters['data']['results']);

        $resultado = array("dados" => $dados, "total" => $characters['data']['total']);
        return $resultado;
    }

    private function urlImage($character)
    {
        return $character['thumbnail']['path'] . '/' . self::IMAGE_SIZE . '.' . $character['thumbnail']['extension'];
    }
}