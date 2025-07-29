<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Redis;

class StatsController extends Controller
{
    public function index(): JsonResponse
    {
        
        $totalParticipants = Redis::get('participants_total') ?? 0;

        return response()->json([
            'total_participants' => (int) $totalParticipants,
        ]);
    }
}
