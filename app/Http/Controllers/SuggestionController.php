<?php

namespace App\Http\Controllers;

use App\Models\AiSuggestion;
use Illuminate\Http\Request;

class SuggestionController extends Controller
{
    public function dismiss(int $id, Request $request)
    {
        AiSuggestion::where('tenant_id', $request->user()->tenant_id)
            ->findOrFail($id)
            ->update(['is_dismissed' => true]);

        return back()->with('success', 'Suggestion dismissed.');
    }
}
