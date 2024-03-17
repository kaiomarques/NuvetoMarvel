<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use App\Services\ApiCharactersService;
use Session;

class CharactersController extends Controller
{
    protected $chractersService;

    private $urlAutenticada = '';

    function __construct(ApiCharactersService $chractersService) {
        $this->chractersService = $chractersService;
    }

    public function index(Request $request) {
        $page = $request->input('page') ?? 1;
        $limit = 21;
        $offset = ($page - 1) * $limit;

        try {
            $chracters = $this->chractersService->getAllCharacters($limit, $offset);
            return Inertia::render("Characters", ['characters' => $chracters, 'page' => $page] );
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao buscar personagens: ' . $e->getMessage()], 500);
        }
    }
}
