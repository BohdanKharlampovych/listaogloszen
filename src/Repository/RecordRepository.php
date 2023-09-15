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
    public const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Query all records.
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

    public function findByTitle($value): ?Record
    {
        return $this->createQueryBuilder('record')
            ->andWhere('record.title = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByCategory($value): ?Record
    {
        return $this->createQueryBuilder('record')
            ->andWhere('record.category = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
