<?php
/**
 * Controller que lista personagens de Marvel
 * e envia para a view do Inertia
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
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use App\Services\ApiCharactersService;
use App\Library\PaginationTrait;

/**
 * Controller que lista personagens de Marvel
 * e envia para a view do Inertia
 * 
 * @category Controller
 * @package  App\Controllers
 * @author   Kaio Luiz Marques <kaiolmarques@gmail.com>
 * @license  https://opensource.org/license/MIT MIT
 * @link     https://github.com/kaiomarques/
 */
class CharactersController extends Controller
{
    use PaginationTrait;

    const REGISTROS_PAGINA = 20;

    protected $charactersService;

    /**
     * Cria uma nova instância do controlador.
     *
     * @param ApiCharactersService $charactersService Serviço de personagens da API.
     */    
    public function __construct(ApiCharactersService $charactersService)
    {
        $this->charactersService = $charactersService;
    }

    /**
     * Retorna a página principal com os personagens da Marvel.
     *
     * @param Request $request Requisição HTTP.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        try {
            $currentPage = $request->input('page') ?? 1;

            $characters = $this->charactersService->getAllCharacters(
                $this->_limit(),
                $this->offset($currentPage, self::REGISTROS_PAGINA)
            );

            return Inertia::render(
                "Characters", [
                'characters' => $characters["dados"], 
                'totalPages' => $this->totalPages(
                    $characters["total"], self::REGISTROS_PAGINA
                ), 
                'currentPage' => $currentPage
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                ['error' => 'Erro ao buscar personagens: ' . $e->getMessage()], 500
            );
        }
    }
    /**
     * Retorna o número máximo de registros por página.
     *
     * @return int
     */
    private function _limit()
    {
        return self::REGISTROS_PAGINA;
    }
}