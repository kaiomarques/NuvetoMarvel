<?php
/**
 * Classe que se comunica diretamente com a API da Marvel
 * para listar personagens
 * 
 * Php version 8.2.0
 *
 * @category Serviço
 * @package  App\Services
 * @author   Kaio Luiz Marques <kaiolmarques@gmail.com>
 * @license  https://opensource.org/license/MIT MIT
 * @link     https://github.com/kaiomarques/
 */
namespace App\Services;

use App\Models\FavoriteCharacters;
use Psr\Cache\CacheItemPoolInterface;
use Illuminate\Support\Facades\Auth;

/**
 * Classe que se comunica diretamente com a API da Marvel
 * para listar personagens
 * 
 * @category Serviço
 * @package  App\Services
 * @author   Kaio Luiz Marques <kaiolmarques@gmail.com>
 * @license  https://opensource.org/license/MIT MIT
 * @link     https://github.com/kaiomarques/
 */
class ApiCharactersService extends ApiServiceBase implements ApiCharactersInterface
{
    const IMAGE_SIZE = 'standard_fantastic';

    /**
     * Construtor da classe.
     *
     * @param CacheItemPoolInterface $cache Pool de cache para 
     *                                      armazenamento de dados em cache.
     */
    public function __construct(CacheItemPoolInterface $cache)
    {
        $api_public_key = config('app.api_public_key');
        $api_private_key = config('app.api_private_key');
        $ts = 1234;
        $hash = md5($ts . $api_private_key . $api_public_key);
        $baseUrl = config('app.api_endpoint') . 'characters';
        $apiKey = "?ts=" . $ts . "&apikey=" . $api_public_key . "&hash=" . $hash;

        parent::__construct($cache, $baseUrl, $apiKey);
    }

    /**
     * Obtém todos os personagens da API da Marvel.
     *
     * @param int|null $limit  Limite de resultados por página.
     * @param int|null $offset Número de registros a serem ignorados.
     *
     * @return array Dados dos personagens obtidos da API.
     */
    public function getAllCharacters($limit = null, $offset = null)
    {
        $url = $this->apiBaseUrl . $this->apiKey;

        if ($limit !== null && $offset !== null) {
            $url .= '&limit=' . $limit . '&offset=' . $offset;
        }

        return $this->mapCharacters($this->fetch($url));
    }

    /**
     * Mapeia os dados de um personagem retornado pela API.
     *
     * @param array $character Dados do personagem retornado pela API.
     *
     * @return array Dados do personagem mapeado.
     */    
    protected function mapCharacter($character)
    {
        return [
            'id' => $character['id'],
            'name' => $character['name'],
            'image' => $this->_urlImage($character),
            'like' => $this->_getLike($character)
        ];
    }

    /**
     * Verifica se um personagem é favorito do usuário autenticado.
     *
     * @param array $character Dados do personagem.
     *
     * @return bool Retorna true se o personagem for favorito do usuário, 
     * caso contrário, retorna false.
     */    
    private function _getLike($character)
    {
        if (!Auth::check()) {
            return false;
        }

        $like = FavoriteCharacters::where(
            [
                'id_character' => $character["id"], 
                "id_usuario" => Auth::user()->id
            ]
        )
            ->exists();
        return ($like) ? true : false;
    }

    /**
     * Mapeia os dados de todos os personagens retornados pela API.
     *
     * @param array $characters Dados de todos os personagens retornados pela API.
     *
     * @return array Dados dos personagens mapeados.
     */
    protected function mapCharacters($characters)
    {
        $dados = array_map(
            function ($character) {
                return $this->mapCharacter($character);
            }, $characters['data']['results']
        );

        $resultado = array(
            "dados" => $dados, 
            "total" => $characters['data']['total']);
        return $resultado;
    }

    /**
     * Obtém a URL da imagem do personagem.
     *
     * @param array $character Dados do personagem.
     *
     * @return string URL da imagem do personagem.
     */    
    private function _urlImage($character)
    {
        return $character['thumbnail']['path'] 
            . '/' . self::IMAGE_SIZE 
            . '.' . $character['thumbnail']['extension'];
    }
}