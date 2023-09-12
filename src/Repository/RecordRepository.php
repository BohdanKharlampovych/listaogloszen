<?php
/**
 * Record repository.
 */

namespace App\Repository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Record;

/**
 * Class RecordRepository.
 */
class RecordRepository extends ServiceEntityRepository
{
    const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Query all records.
     *
     * @return QueryBuilder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->select(
                'partial record.{id, createdAt, title, text}',
                'partial category.{id, title}'
            )
            ->join('record.category', 'category')
            ->orderBy('record.id', 'ASC');
    }
    /**
     * Get or create query builder.
     *
     * @param QueryBuilder|null $queryBuilder
     *
     * @return QueryBuilder
     */
    private function getOrCreateQueryBuilder(QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('record');
    }

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Record::class);
    }

    /**
     * Save entity.
     *
     * @param Record $record Category entity
     */
    public function save(Record $record): void
    {
        $this->_em->persist($record);
        $this->_em->flush();
    }
    /**
     * Delete entity.
     *
     * @param Record $record Category entity
     */
    public function delete(Record $record): void
    {
        $this->_em->remove($record);
        $this->_em->flush();
    }
}
