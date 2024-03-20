<?php
/**
 * Classe que se comunica diretamente com a API da Marvel
 * para listar quadrinhos
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

use App\Models\FavoriteComics;
use Psr\Cache\CacheItemPoolInterface;
use Illuminate\Support\Facades\Auth;

/**
 * Classe que se comunica diretamente com a API da Marvel
 * para listar quadrinhos
 * 
 * @category Serviço
 * @package  App\Services
 * @author   Kaio Luiz Marques <kaiolmarques@gmail.com>
 * @license  https://opensource.org/license/MIT MIT
 * @link     https://github.com/kaiomarques/
 */
class ApiComicService extends ApiServiceBase implements ApiComicsInterface
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
        $baseUrl = config('app.api_endpoint') . 'comics';
        $apiKey = "?ts=" . $ts . "&apikey=" . $api_public_key . "&hash=" . $hash;

        parent::__construct($cache, $baseUrl, $apiKey);
    }

    /**
     * Obtém todos os quadrinhos da API da Marvel.
     *
     * @param int|null $limit  Limite de resultados por página.
     * @param int|null $offset Número de registros a serem ignorados.
     *
     * @return array Dados dos quadrinhos obtidos da API.
     */
    public function getAllComics($limit = null, $offset = null)
    {
        $url = $this->apiBaseUrl . $this->apiKey;

        if ($limit !== null && $offset !== null) {
            $url .= '&limit=' . $limit . '&offset=' . $offset;
        }

        return $this->mapComics($this->fetch($url));
    }

    /**
     * Mapeia os dados de um quadrinho retornado pela API.
     *
     * @param array $comic Dados do quadrinho retornado pela API.
     *
     * @return array Dados do quadrinho mapeado.
     */    
    protected function mapComic($comic)
    {
        return [
            'id' => $comic['id'],
            'title' => $comic['title'],
            'image' => $this->_urlImage($comic),
            'creators' => $this->_creators($comic),
            'like' => $this->_getLike($comic)
        ];
    }

    /**
     * Verifica se um quadrinho é favorito do usuário autenticado.
     *
     * @param array $comic Dados do quadrinho.
     *
     * @return bool Retorna true se o quadrinho for favorito do usuário, 
     * caso contrário, retorna false.
     */    
    private function _getLike($comic)
    {
        if (!Auth::check()) {
            return false;
        }

        $like = FavoriteComics::where(
            ['id_comic' => $comic["id"], "id_usuario" => Auth::user()->id]
        )
            ->exists();
        return ($like) ? true : false;
    }

    /**
     * Mapeia os dados de todos os quadrinhos retornados pela API.
     *
     * @param array $comics Dados de todos os quadrinhos retornados pela API.
     *
     * @return array Dados dos quadrinhos mapeados.
     */
    protected function mapComics($comics)
    {
        $dados = array_map(
            function ($comic) {
                return $this->mapComic($comic);
            }, $comics["data"]['results']
        );

        $resultado = array("dados" => $dados, "total" => $comics['data']['total']);
        return $resultado;
    }

    /**
     * Obtém a URL da imagem do quadrinho.
     *
     * @param array $comic Dados do quadrinho.
     *
     * @return string URL da imagem do quadrinho.
     */    
    private function _urlImage($comic)
    {
        return $comic['thumbnail']['path'] 
            . '/' . self::IMAGE_SIZE . '.' . $comic['thumbnail']['extension'];
    }

    /**
     * Obtém os nomes dos criadores do quadrinho.
     *
     * @param array $comic Dados do quadrinho.
     *
     * @return string Nomes dos criadores do quadrinho.
     */    
    private function _creators($comic)
    {
        $nomes = 'Autor não cadastrado';
        if (isset($comic["creators"]["items"])  
            && is_array($comic["creators"]["items"]) 
            && count($comic["creators"]["items"]) > 0
        ) {
            $nomes = array_column(
                array_slice($comic["creators"]['items'], 0, 2), 'name'
            );
            $nomes = implode(', ', $nomes);
        }
        return $nomes;
    }
}