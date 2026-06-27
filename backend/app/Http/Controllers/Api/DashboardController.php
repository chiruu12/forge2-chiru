<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function metrics(): JsonResponse
    {
        $user = request()->user();

        // Only agents and admins can view metrics
        if ($user->role === 'customer') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $openCount = Ticket::where('status', 'open')->count();
        $pendingCount = Ticket::where('status', 'pending')->count();
        $resolvedCount = Ticket::where('status', 'resolved')->count();
        $closedCount = Ticket::where('status', 'closed')->count();
        $urgentOpenCount = Ticket::where('status', 'open')->where('priority', 'urgent')->count();
        $totalTickets = Ticket::count();

        return response()->json([
            'data' => [
                'open_count' => $openCount,
                'pending_count' => $pendingCount,
                'resolved_count' => $resolvedCount,
                'closed_count' => $closedCount,
                'urgent_open_count' => $urgentOpenCount,
                'total_tickets' => $totalTickets,
                'avg_first_response_minutes' => null, // TODO: calculate from comments
            ],
        ]);
    }
}
