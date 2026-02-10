<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\BarbecueSuggestionRequest;
use App\Models\BarbecueSuggestion;
use Illuminate\Http\Request;

class BarbecueSuggestionController extends Controller
{
    public function create()
    {
        return view('barbecue.suggest');
    }

    public function store(BarbecueSuggestionRequest $request)
    {
        $data = $request->validated();
        BarbecueSuggestion::create([
            'category_slug' => $data['category_slug'],
            'name' => $data['name'],
            'user_id' => optional($request->user())->id,
            'status' => BarbecueSuggestion::STATUS_PENDING,
        ]);

        return redirect()->route('barbecue.suggest')
            ->with('success', 'Sugestão enviada para análise!');
    }
}

