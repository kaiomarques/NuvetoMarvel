<?php
/**
 * Controller que contabiliza o like de um quadrinho
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

use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use App\Models\FavoriteComics;
use Exception;

/**
 * Controller que contabiliza o like de um quadrinho
 * 
 * @category Controller
 * @package  App\Controllers
 * @author   Kaio Luiz Marques <kaiolmarques@gmail.com>
 * @license  https://opensource.org/license/MIT MIT
 * @link     https://github.com/kaiomarques/
 */
class LikeComicController extends Controller
{
    /**
     * Alternar o estado do like de um personagem.
     *
     * @param int $idComic ID do personagem.
     * 
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function toggle($idComic)
    {
        try {
            if (!Auth::check()) {
                throw new AuthenticationException('Usuário não autenticado.');
            }
            
            $userId = Auth::id();
            $likeComic = FavoriteComics::where(
                ["id_comic" => $idComic, "id_usuario" => $userId])->exists();
            
            $message = "";

            if ($likeComic) {
                FavoriteComics::where(
                    ["id_comic" => $idComic, "id_usuario" => $userId])->delete();
                $message = 'Comic não é mais favorito.';
            } else {
                FavoriteComics::insert(
                    ["id_comic" => $idComic, "id_usuario" => $userId]);
                $message = 'Comic favoritado.';
            }
            return redirect()->back();

        } catch (AuthenticationException $exception) {
            return response()->json(['error' => $exception->getMessage()], 401);
        } catch (Exception $exception) {
            return response()->json(
                [
                    'error' => 'Ocorreu um erro inesperado.', 
                    'message'=>$exception->getMessage()
                ], 500);
        }
    }
}