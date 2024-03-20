<?php
/**
 * Controller que contabiliza o like de um personagem
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
use App\Models\FavoriteCharacters;
use Exception;

/**
 * Controller que contabiliza o like de um personagem
 * 
 * @category Controller
 * @package  App\Controllers
 * @author   Kaio Luiz Marques <kaiolmarques@gmail.com>
 * @license  https://opensource.org/license/MIT MIT
 * @link     https://github.com/kaiomarques/
 */
class LikeCharacterController extends Controller
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
            $likeComic = FavoriteCharacters::where(
                ["id_character" => $idComic, "id_usuario" => $userId]
            )
                ->exists();
            
            $message = "";

            if ($likeComic) {
                FavoriteCharacters::where(
                    ["id_character" => $idComic, "id_usuario" => $userId]
                )->delete();
                $message = 'Personagem não é mais favorito.';
            } else {
                FavoriteCharacters::insert(
                    ["id_character" => $idComic, "id_usuario" => $userId]
                );
                $message = 'Personagem favoritado.';
            }
            return redirect()->back();

        } catch (AuthenticationException $exception) {
            return response()->json(['error' => $exception->getMessage()], 401);
        } catch (Exception $exception) {
            return response()->json(
                [
                    'error' => 'Ocorreu um erro inesperado.', 
                    'message'=>$exception->getMessage()
                ], 500
            );
        }
    }
}