<?php

namespace App\Tests\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Service\CategoryService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryServiceTest extends WebTestCase
{
    /**
     * Category service.
     */
    private ?CategoryService $categoryService;

    /**
     * @return void void
     */
    public function setUp(): void
    {
        $container = static::getContainer();
        $this->categoryService = $container->get(CategoryService::class);
    }

    /**
     * Test GetPaginatedList.
     *
     * @return void void
     */
    public function testGetPaginatedList(): void
    {
        // given
        $dataSetSize = 3;
        $expectedResultSize = 3;
        $saved = 0;
        $categoryRepository =
            static::getContainer()->get(CategoryRepository::class);
        for ($i=0; $i<$dataSetSize; $i++,$saved++)
        {
            $category = new Category();
            $category->setTitle('Categoryx'.$i);
            $category->setCreatedAt(new \DateTimeImmutable('now'));
            $category->setUpdatedAt(new \DateTimeImmutable('now'));
            $categoryRepository->save($category);
        }

        // then
        $this->assertEquals($expectedResultSize, $saved);
    }
}
