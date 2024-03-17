<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use App\Services\ApiComicService;
use Session;

class ComicsController extends Controller
{
    protected $comicService;

    private $urlAutenticada = '';

    function __construct(ApiComicService $comicService) {
        $this->comicService = $comicService;
    }

    public function index(Request $request) {
        $page = $request->input('page') ?? 1;
        $limit = 21;
        $offset = ($page - 1) * $limit;

        try {
            $comics = $this->comicService->getAllComics($limit, $offset);
            return Inertia::render("Comics", ['comics' => $comics, 'page' => $page] );
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao buscar quadrinhos: ' . $e->getMessage()], 500);
        }
    }
}
