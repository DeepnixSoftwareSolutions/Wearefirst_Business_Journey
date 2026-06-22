<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MyNetworkController extends Controller
{
    public function index(Request $request)
    {
        // STRICT FILTER: Only grab users this exact agent registered
        $query = User::where('registered_by_agent_id', $request->user()->id)
                    ->with(['sponsor', 'intendedParentNode.user']);

        // Allow searching within their downline
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nic', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
                if (is_numeric($search)) {
                    $q->orWhere('member_id', (int) $search);
                }
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(50)->withQueryString();

        return Inertia::render('Agent/MyNetwork', [
            'users' => $users,
            'filters' => $request->only(['search'])
        ]);
    }
}