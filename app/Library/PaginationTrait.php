<?php

namespace App\Library;

trait PaginationTrait
{
    private function offset($currentPage, $perPage) {
        return ($currentPage - 1) * $perPage;
    }

    private function totalPages($totalRecords, $perPage) {
        return ceil($totalRecords / $perPage);
    }
}