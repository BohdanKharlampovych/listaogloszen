<?php

namespace App\Service;

use App\Entity\Record;
use App\Repository\RecordRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class RecordService implements RecordServiceInterface
{
    /**
     * Constructor.
     *
     * @param RecordRepository $taskRepository Category repository
     */
    private RecordRepository $recordRepository;
    private PaginatorInterface $paginator;

    public function __construct(RecordRepository $recordRepository, PaginatorInterface $paginator)
    {
        $this->recordRepository = $recordRepository;
        $this->paginator = $paginator;
    }

    /**
     * Create paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface Paginated list
     */
    public function createPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->recordRepository->queryAll(),
            $page,
            recordRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param Record $record Category entity
     */
    public function save(Record $record): void
    {
        if (null == $record->getId()) {
            $record->setCreatedAt(new \DateTimeImmutable());
        }
        $record->setUpdatedAt(new \DateTimeImmutable());

        $this->recordRepository->save($record);
    }

    public function delete(Record $record): void
    {
        $this->recordRepository->delete($record);
    }
}
