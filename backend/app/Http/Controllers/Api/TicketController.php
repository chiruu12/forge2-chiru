<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(): JsonResponse
    {
        $tickets = Ticket::with(['requester', 'assignee'])->get();

        return response()->json([
            'data' => $tickets,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:open,pending,resolved,closed'],
            'priority' => ['required', 'string', 'in:low,medium,high,urgent'],
        ]);

        $ticket = Ticket::create(array_merge($validated, [
            'requester_id' => $request->user()->id,
        ]));

        return response()->json([
            'data' => $ticket->load(['requester', 'assignee']),
        ], 201);
    }

    public function show(Ticket $ticket): JsonResponse
    {
        return response()->json([
            'data' => $ticket->load(['requester', 'assignee']),
        ]);
    }

    public function update(Request $request, Ticket $ticket): JsonResponse
    {
        $validated = $request->validate([
            'subject' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', 'string', 'in:open,pending,resolved,closed'],
            'priority' => ['sometimes', 'string', 'in:low,medium,high,urgent'],
        ]);

        $ticket->update($validated);

        return response()->json([
            'data' => $ticket->load(['requester', 'assignee']),
        ]);
    }

    public function destroy(Ticket $ticket): JsonResponse
    {
        $ticket->delete();

        return response()->json(null, 204);
    }
}
