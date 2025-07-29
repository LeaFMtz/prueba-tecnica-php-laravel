<?php

namespace App\Interfaces;

use App\Models\Participant;

interface ParticipantRepositoryInterface
{
    public function create(array $participantDetails): Participant;
    public function findById(int $participantId): ?Participant;
}