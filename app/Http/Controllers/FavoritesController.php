<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class FavoritesController extends Controller
{
    public function index() {
        return Inertia::render("Favorites");
    }
}
