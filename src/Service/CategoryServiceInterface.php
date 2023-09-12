<?php

namespace App\Service;

use App\Entity\Category;

interface CategoryServiceInterface
{
    /**
     * Save entity.
     *
     * @param Category $category Category entity
     */
    public function save(Category $category): void;
    public function delete(Category $category): void;

    public function getPaginatedList(int $getInt);


}