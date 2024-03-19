<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\FavoriteCharacters;
use App\Models\FavoriteComics;
use App\Services\ApiComicService;
use App\Services\ApiCharactersService;
use Illuminate\Support\Facades\Auth;

class FavoritesController extends Controller
{
    protected $comicService;
    protected $charactersService;

    public function __construct(ApiComicService $comicService, ApiCharactersService $charactersService)
    {
        $this->comicService = $comicService;
        $this->charactersService = $charactersService;
    }

    public function index()
    {
        $favoriteComics = FavoriteComics::where('id_usuario', Auth::user()->id)
            ->pluck('id_comic')
            ->toArray();
        
        $favoriteCharacters = FavoriteCharacters::where('id_usuario', Auth::user()->id)
            ->pluck('id_character')
            ->toArray();

        $comics = $this->comicService->getAllComics();
        $characters = $this->charactersService->getAllCharacters();

        $favoriteComicsData = $this->filterFavorites($comics['dados'], $favoriteComics);
        $favoriteCharactersData = $this->filterFavorites($characters['dados'], $favoriteCharacters);

        return Inertia::render(
            "Favorites", [
            "favoriteComics" => $favoriteComicsData,
            "favoriteCharacters" => $favoriteCharactersData
            ]
        );
    }

    private function filterFavorites($data, $favoriteIds)
    {
        return array_filter(
            $data, function ($item) use ($favoriteIds) {
                return in_array($item['id'], $favoriteIds);
            }
        );
    }
}