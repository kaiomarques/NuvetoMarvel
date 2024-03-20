<?php
/**
 * Trait para organizar funções gerais de paginação
 * 
 * Php version 8.2.0
 *
 * @category Library
 * @package  App\Library
 * @author   Kaio Luiz Marques <kaiolmarques@gmail.com>
 * @license  https://opensource.org/license/MIT MIT
 * @link     https://github.com/kaiomarques/
 */
namespace App\Library;

/**
 * Trait para organizar funções gerais de paginação
 * 
 * @category Controller
 * @package  App\Controllers
 * @author   Kaio Luiz Marques <kaiolmarques@gmail.com>
 * @license  https://opensource.org/license/MIT MIT
 * @link     https://github.com/kaiomarques/
 */
trait PaginationTrait
{
    /**
     * Calcula o deslocamento (offset) para a paginação.
     * 
     * @param int $currentPage O número da página atual.
     * @param int $perPage     O número de itens por página.
     * 
     * @return int O deslocamento (offset) para a página atual.
     */    
    private function offset($currentPage, $perPage)
    {
        return ($currentPage - 1) * $perPage;
    }

    /**
     * Calcula o número total de páginas com base no total de registros e no número de itens por página.
     * 
     * @param int $totalRecords O número total de registros.
     * @param int $perPage      O número de itens por página.
     * 
     * @return int O número total de páginas.
     */    
    private function totalPages($totalRecords, $perPage)
    {
        return ceil($totalRecords / $perPage);
    }
}