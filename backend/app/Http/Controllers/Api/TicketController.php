<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;

class TicketController extends Controller
{
    public function show(Ticket $ticket): JsonResponse
    {
        return response()->json([
            'data' => $ticket->load(['requester', 'assignee']),
        ]);
    }
}
