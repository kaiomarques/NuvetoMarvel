<?php
/**
 * Controller que lista quadrinhos de Marvel
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
use Inertia\Inertia;
use App\Services\ApiComicService;
use App\Library\PaginationTrait;

/**
 * Controller que lista quadrinhos de Marvel
 * e envia para a view do Inertia
 * 
 * @category Controller
 * @package  App\Controllers
 * @author   Kaio Luiz Marques <kaiolmarques@gmail.com>
 * @license  https://opensource.org/license/MIT MIT
 * @link     https://github.com/kaiomarques/
 */
class ComicsController extends Controller
{
    use PaginationTrait;

    protected $comicService;

    const REGISTROS_PAGINA = 21;

    /**
     * Cria uma nova instância do controlador.
     *
     * @param ApiComicService $comicService Serviço de quadrinhos da API.
     */
    public function __construct(ApiComicService $comicService)
    {
        $this->comicService = $comicService;
    }

    /**
     * Retorna a página principal com os quadrinhos da Marvel.
     *
     * @param Request $request Requisição HTTP.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        try {
            $currentPage = $request->input('page') ?? 1;

            $comics = $this->comicService->getAllComics(
                $this->_limit(),
                $this->offset($currentPage, self::REGISTROS_PAGINA)
            );
            
            return Inertia::render(
                "Comics", [
                'comics' => $comics["dados"], 
                'totalPages' => 
                    $this->totalPages($comics["total"], self::REGISTROS_PAGINA), 
                'currentPage' => $currentPage
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                ['error' => 'Erro ao buscar quadrinhos: ' . $e->getMessage()], 500
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