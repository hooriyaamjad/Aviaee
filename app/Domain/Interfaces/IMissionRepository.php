<?php

namespace App\Domain\Interfaces;

use App\Domain\Entities\MissionEntity;

interface IMissionRepository
{
    /**
     * Get missions by creator email
     *
     * @param string $email
     * @return MissionEntity[]
     */
    public function getMissions(string $email): array;

    /**
     * Creates a mission in the database and returns the new entity
     */
    public function create(MissionEntity $mission): MissionEntity;
}
