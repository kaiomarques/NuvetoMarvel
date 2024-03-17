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

    function __construct(ApiComicService $comicService, ApiCharactersService $charactersService) {
        $this->comicService         = $comicService;
        $this->charactersService    = $charactersService;
    }

    public function index() {

        $favoriteComics = 
            FavoriteComics::where(['id_usuario' => Auth::user()->id])
                ->pluck('id_comic')
                ->toArray();
        $favoriteCharacters = 
            FavoriteCharacters::where(['id_usuario' => Auth::user()->id])
                ->pluck('id_character')
                ->toArray();

        $comics = $this->comicService->getAllComics();
        $characters = $this->charactersService->getAllCharacters();

        $favoriteComicsData = 
            array_filter($comics["dados"], function($comic) use ($favoriteComics) {
                return in_array($comic['id'], $favoriteComics);
            });

        $favoriteCharactersData = 
            array_filter($characters["dados"], function($character) use ($favoriteCharacters) {
                return in_array($character['id'], $favoriteCharacters);
            });


        return Inertia::render("Favorites", 
            [
                "favoriteComics" => $favoriteComicsData,
                "favoriteCharacters" => $favoriteCharactersData
            ]
        );
    }
}
