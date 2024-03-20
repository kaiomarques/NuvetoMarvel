<?php
/**
 * Contrato das classes para listar quadrinhos na API da Marvel
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
 * Contrato das classes para listar quadrinhos na API da Marvel
 * 
 * @category Serviço
 * @package  App\Services
 * @author   Kaio Luiz Marques <kaiolmarques@gmail.com>
 * @license  https://opensource.org/license/MIT MIT
 * @link     https://github.com/kaiomarques/
 */
interface ApiComicsInterface
{
        /**
         * Obtém todos os quadrinhos da API.
         *
         * @return array Dados dos quadrinhos obtidos da API.
         */
    public function getAllComics();
}