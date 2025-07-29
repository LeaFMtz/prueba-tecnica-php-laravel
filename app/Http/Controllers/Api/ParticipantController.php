<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreParticipantRequest;
use App\Services\ParticipantService;
use Illuminate\Http\JsonResponse;

class ParticipantController extends Controller
{
    protected ParticipantService $participantService;

    public function __construct(ParticipantService $participantService)
    {
        $this->participantService = $participantService;
    }

    public function store(StoreParticipantRequest $request): JsonResponse
    {
        $participant = $this->participantService->registerParticipant(
            $request->validated()
        );

        return response()->json([
            'message' => 'Participante registrado exitosamente.',
            'data' => $participant
        ], 201);
    }
    public function show(int $id): JsonResponse
    {
        $participant = $this->participantService->getParticipantById($id);

        if (!$participant) {
            return response()->json(['message' => 'Participante no encontrado.'], 404);
        }

        return response()->json($participant);
    }
}
