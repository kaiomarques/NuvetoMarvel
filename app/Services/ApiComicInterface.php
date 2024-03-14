<?php
namespace App\Services;

interface ApiComicInterface
{
    public function getAllComics();
    public function getComicById(int $id);
}