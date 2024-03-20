<?php
/**
 * Controller que lista quadrinhos e personagens
 * favoritados pelo usuário logado
 * 
 * Php version 8.2.0
 *
 * @category Controller
 * @package  App\Controllers
 * @author   Kaio Luiz Marques <kaiolmarques@gmail.com>
 * @license  https://opensource.org/license/MIT MIT
 * @link     https://github.com/kaiomarques/
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\FavoriteCharacters;
use App\Models\FavoriteComics;
use App\Services\ApiComicService;
use App\Services\ApiCharactersService;
use Illuminate\Support\Facades\Auth;

/**
 * Controller que lista quadrinhos e personagens
 * favoritados pelo usuário logado
 * 
 * @category Controller
 * @package  App\Controllers
 * @author   Kaio Luiz Marques <kaiolmarques@gmail.com>
 * @license  https://opensource.org/license/MIT MIT
 * @link     https://github.com/kaiomarques/
 */
class FavoritesController extends Controller
{
    protected $comicService;
    protected $charactersService;

    /**
     * Cria uma nova instância do controlador.
     *
     * @param ApiComicService      $comicService      Serviço de quadrinhos da
     *                                                API.
     * @param ApiCharactersService $charactersService Serviço de personagens da API.
     */
    public function __construct(
        ApiComicService $comicService, 
        ApiCharactersService $charactersService
    ) {
        $this->comicService = $comicService;
        $this->charactersService = $charactersService;
    }

    /**
     * Exibe os quadrinhos e personagens favoritados pelo usuário logado.
     *
     * @return \Inertia\Response
     */    
    public function index()
    {
        try{
            $favoriteComics = FavoriteComics::where('id_usuario', Auth::user()->id)
                ->pluck('id_comic')
                ->toArray();
            
            $favoriteCharacters = FavoriteCharacters::where(
                'id_usuario', Auth::user()->id
            )
                ->pluck('id_character')
                ->toArray();

            $comics = $this->comicService->getAllComics();
            $characters = $this->charactersService->getAllCharacters();

            $favoriteComicsData = $this->_filterFavorites(
                $comics['dados'], $favoriteComics
            );
            $favoriteCharactersData = $this->_filterFavorites(
                $characters['dados'], $favoriteCharacters
            );

            return Inertia::render(
                "Favorites", [
                "favoriteComics" => $favoriteComicsData,
                "favoriteCharacters" => $favoriteCharactersData
                ]
            );
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao buscar favoritos: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Filtra os itens favoritados pelo usuário logado.
     *
     * @param array $data        Dados a serem filtrados.
     * @param array $favoriteIds IDs dos itens favoritados.
     *
     * @return array
     */
    private function _filterFavorites($data, $favoriteIds)
    {
        return array_filter(
            $data, function ($item) use ($favoriteIds) {
                return in_array($item['id'], $favoriteIds);
            }
        );
    }
}