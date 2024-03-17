<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\FavoriteComics;

class LikeComicController extends Controller
{
    public function toggle($idComic)
    {
        try {
            if (!Auth::check()) {
                throw new AuthenticationException('Usuário não autenticado.');
            }
            
            $userId = Auth::id();
            $likeComic = FavoriteComics::where(["id_comic" => $idComic, "id_usuario" => $userId])->exists();
            
            $message = "";

            if ($likeComic) {
                FavoriteComics::where(["id_comic" => $idComic, "id_usuario" => $userId])->delete();
                $message = 'Comic não é mais favorito.';
            } else {
                FavoriteComics::insert(["id_comic" => $idComic, "id_usuario" => $userId]);
                $message = 'Comic favoritado.';
            }
            return redirect()->back();

        } catch (\Illuminate\Auth\AuthenticationException $exception) {
            return response()->json(['error' => $exception->getMessage()], 401);
        } catch (\Exception $exception) {
            return response()->json(['error' => 'Ocorreu um erro inesperado.', 'message'=>$exception->getMessage()], 500);
        }
    }
}