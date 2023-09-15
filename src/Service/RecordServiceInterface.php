<?php

namespace App\Service;

use App\Entity\Record;

interface RecordServiceInterface
{
    /**
     * Save entity.
     *
     * @param Record $record Record entity
     */
    public function save(Record $record): void;

    public function delete(Record $record): void;
}
