<?php

namespace App\Services;

use App\Events\ParticipantRegistered;
use App\Interfaces\ParticipantRepositoryInterface;
use App\Models\Participant;

class ParticipantService
{
    protected ParticipantRepositoryInterface $participantRepository;

    public function __construct(ParticipantRepositoryInterface $participantRepository)
    {
        $this->participantRepository = $participantRepository;
    }

    public function registerParticipant(array $participantDetails): Participant
    {
        $participant = $this->participantRepository->create($participantDetails);

        ParticipantRegistered::dispatch($participant);

        return $participant;
    }

    public function getParticipantById(int $id): ?Participant
    {
        return $this->participantRepository->findById($id);
    }
}
