<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use App\Services\ApiCharactersService;
use App\Library\PaginationTrait;

class CharactersController extends Controller
{
    use PaginationTrait;

    const REGISTROS_PAGINA = 20;

    protected $charactersService;

    public function __construct(ApiCharactersService $charactersService)
    {
        $this->charactersService = $charactersService;
    }

    public function index(Request $request)
    {
        try {
            $currentPage = $request->input('page') ?? 1;

            $characters = $this->charactersService->getAllCharacters(
                $this->limit(),
                $this->offset($currentPage, self::REGISTROS_PAGINA)
            );

            return Inertia::render(
                "Characters", [
                'characters' => $characters["dados"], 
                'totalPages' => $this->totalPages($characters["total"], self::REGISTROS_PAGINA), 
                'currentPage' => $currentPage
                ]
            );
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao buscar personagens: ' . $e->getMessage()], 500);
        }
    }

    private function limit()
    {
        return self::REGISTROS_PAGINA;
    }
}