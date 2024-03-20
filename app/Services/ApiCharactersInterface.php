<?php
/**
 * Contrato das classes para listar personagens na API da Marvel
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
interface ApiCharactersInterface
{
    /**
     * Obtém todos os personagens da API.
     *
     * @return array Dados dos personagens obtidos da API.
     */
    public function getAllCharacters();
}