<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureGroupIsSelected
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $activeGroupId = session('active_group_id');

        if (!$activeGroupId) {
            $userGroups = auth()->user()->groups;

            if ($userGroups->count() === 1) {
                session(['active_group_id' => $userGroups->first()->id]);
                return $next($request);
            }

            if ($userGroups->isEmpty()) {
                return redirect()->route('groups.create')->with('info', 'Please create a group to get started.');
            }

            return redirect()->route('groups.index')->with('info', 'Please select a group to continue.');
        }

        // Verify user still belongs to this group
        if (!auth()->user()->groups()->where('groups.id', $activeGroupId)->exists()) {
            session()->forget('active_group_id');
            return redirect()->route('groups.index');
        }

        return $next($request);
    }
}
