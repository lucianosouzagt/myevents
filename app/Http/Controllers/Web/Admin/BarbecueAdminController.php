<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\BarbecueCategory;
use App\Models\BarbecueItemType;
use App\Models\BarbecueSuggestion;
use Illuminate\Http\Request;

class BarbecueAdminController extends Controller
{
    public function index()
    {
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            abort(403);
        }
        $pending = BarbecueSuggestion::where('status', BarbecueSuggestion::STATUS_PENDING)
            ->orderByDesc('created_at')
            ->get();

        return view('barbecue.admin.suggestions', compact('pending'));
    }

    public function moderate(Request $request, int $id)
    {
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            abort(403);
        }
        $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $suggestion = BarbecueSuggestion::findOrFail($id);
        $suggestion->admin_notes = $request->input('notes');

        if ($request->input('action') === 'approve') {
            $suggestion->status = BarbecueSuggestion::STATUS_APPROVED;
            // criar item padrão na categoria correspondente
            $category = BarbecueCategory::where('slug', $suggestion->category_slug)->firstOrFail();
            $defaults = $this->defaultForCategory($suggestion->category_slug);

            BarbecueItemType::firstOrCreate(
                [
                    'barbecue_category_id' => $category->id,
                    'name' => $suggestion->name,
                ],
                [
                    'unit' => $defaults['unit'],
                    'default_per_adult' => $defaults['adult'],
                    'default_per_child' => $defaults['child'],
                    'active' => true,
                ]
            );
        } else {
            $suggestion->status = BarbecueSuggestion::STATUS_REJECTED;
        }

        $suggestion->save();

        return redirect()->route('barbecue.admin.suggestions')
            ->with('success', 'Sugestão processada com sucesso.');
    }

    private function defaultForCategory(string $slug): array
    {
        return match ($slug) {
            'meat' => ['unit' => 'kg', 'adult' => 0.20, 'child' => 0.10],
            'side' => ['unit' => 'kg', 'adult' => 0.10, 'child' => 0.05],
            default => ['unit' => 'un', 'adult' => 1, 'child' => 1],
        };
    }
}
