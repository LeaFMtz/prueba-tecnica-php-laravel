<?php

namespace App\Repositories;

use App\Interfaces\ParticipantRepositoryInterface;
use App\Models\Participant;
use Illuminate\Support\Facades\Cache;

class ParticipantRepository implements ParticipantRepositoryInterface
{
    public function create(array $participantDetails): Participant
    {
        return Participant::create($participantDetails);
    }
    public function findById(int $participantId): ?Participant
    {
        
        $cacheKey = 'participant_' . $participantId;
        
        return Cache::remember($cacheKey, 3600, function () use ($participantId) {
            return Participant::find($participantId);
        });
    }
}