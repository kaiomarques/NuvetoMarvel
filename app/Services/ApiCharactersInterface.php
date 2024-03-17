<?php
namespace App\Services;

interface ApiCharactersInterface
{
    public function getAllCharacters();
    public function getCharacterById(int $id);
}