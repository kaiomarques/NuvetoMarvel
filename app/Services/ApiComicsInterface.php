<?php
namespace App\Services;

interface ApiComicsInterface
{
    public function getAllComics();
    public function getComicById(int $id);
}