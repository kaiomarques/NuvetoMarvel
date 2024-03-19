<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\ApiComicService;
use App\Library\PaginationTrait;

class ComicsController extends Controller
{
    use PaginationTrait;

    protected $comicService;

    const REGISTROS_PAGINA = 21;

    public function __construct(ApiComicService $comicService)
    {
        $this->comicService = $comicService;
    }

    public function index(Request $request)
    {
        try {
            $currentPage = $request->input('page') ?? 1;

            $comics = $this->comicService->getAllComics(
                $this->limit(),
                $this->offset($currentPage, self::REGISTROS_PAGINA)
            );
            
            return Inertia::render(
                "Comics", [
                'comics' => $comics["dados"], 
                'totalPages' => $this->totalPages($comics["total"], self::REGISTROS_PAGINA), 
                'currentPage' => $currentPage
                ]
            );
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao buscar quadrinhos: ' . $e->getMessage()], 500);
        }
    }

    private function limit()
    {
        return self::REGISTROS_PAGINA;
    }
}