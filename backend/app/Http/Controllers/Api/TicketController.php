<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Ticket::with(['requester', 'assignee']);

        if ($user->role === 'customer') {
            $query->where('requester_id', $user->id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        // Filter by priority
        if ($request->has('priority')) {
            $query->where('priority', $request->get('priority'));
        }

        // Filter by assignee_id
        if ($request->has('assignee_id')) {
            $assigneeId = $request->get('assignee_id');
            if ($assigneeId === 'null' || $assigneeId === null) {
                $query->whereNull('assignee_id');
            } else {
                $query->where('assignee_id', $assigneeId);
            }
        }

        // Search by subject or description
        if ($request->has('q')) {
            $search = $request->get('q');
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return response()->json([
            'data' => $query->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:open,in_progress,pending,resolved,closed'],
            'priority' => ['required', 'string', 'in:low,medium,high,urgent'],
            'assignee_id' => ['nullable', 'integer'],
            'requester_id' => ['nullable', 'integer'],
        ]);

        // Prevent cross-org requester_id / assignee_id manipulation
        if (! empty($validated['requester_id'])) {
            $requester = User::find($validated['requester_id']);
            if (! $requester || $requester->organization_id !== $user->organization_id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }

        if (! empty($validated['assignee_id'])) {
            $assignee = User::find($validated['assignee_id']);
            if (! $assignee || $assignee->organization_id !== $user->organization_id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }

        // Customers can only create tickets for themselves
        if ($user->role === 'customer') {
            if (! empty($validated['requester_id']) && $validated['requester_id'] !== $user->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }

        $ticket = Ticket::create(array_merge($validated, [
            'requester_id' => $validated['requester_id'] ?? $user->id,
        ]));

        return response()->json([
            'data' => $ticket->load(['requester', 'assignee']),
        ], 201);
    }

    public function show(Ticket $ticket): JsonResponse
    {
        $user = request()->user();

        // Customers can only see their own tickets
        if ($user->role === 'customer' && $ticket->requester_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'data' => $ticket->load(['requester', 'assignee', 'comments.author']),
        ]);
    }

    public function update(Request $request, Ticket $ticket): JsonResponse
    {
        $user = $request->user();

        // Customers can only update their own tickets
        if ($user->role === 'customer' && $ticket->requester_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'subject' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', 'string', 'in:open,in_progress,pending,resolved,closed'],
            'priority' => ['sometimes', 'string', 'in:low,medium,high,urgent'],
            'assignee_id' => ['sometimes', 'nullable', 'integer'],
            'requester_id' => ['sometimes', 'nullable', 'integer'],
        ]);

        // Prevent cross-org requester_id / assignee_id manipulation
        if (array_key_exists('requester_id', $validated) && $validated['requester_id'] !== null) {
            $requester = User::find($validated['requester_id']);
            if (! $requester || $requester->organization_id !== $user->organization_id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }

        if (array_key_exists('assignee_id', $validated) && $validated['assignee_id'] !== null) {
            $assignee = User::find($validated['assignee_id']);
            if (! $assignee || $assignee->organization_id !== $user->organization_id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }

        // Customers cannot change requester_id or assignee_id
        if ($user->role === 'customer') {
            unset($validated['requester_id'], $validated['assignee_id']);
        }

        $ticket->update($validated);

        return response()->json([
            'data' => $ticket->load(['requester', 'assignee', 'comments.author']),
        ]);
    }

    public function destroy(Ticket $ticket): JsonResponse
    {
        $user = request()->user();

        // Customers can only delete their own tickets
        if ($user->role === 'customer' && $ticket->requester_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $ticket->delete();

        return response()->json(null, 204);
    }
}
