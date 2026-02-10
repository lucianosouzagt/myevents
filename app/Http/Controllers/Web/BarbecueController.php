<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\BarbecuePlanRequest;
use App\Models\BarbecueCategory;
use App\Models\BarbecueItemType;
use App\Services\BarbecuePlannerService;
use Illuminate\Http\Request;

class BarbecueController extends Controller
{
    public function __construct(
        protected BarbecuePlannerService $plannerService
    ) {
    }

    public function index(Request $request)
    {
        $categories = BarbecueCategory::with(['itemTypes' => function ($q) {
            $q->where('active', true)->orderBy('name');
        }])->orderBy('name')->get();

        return view('barbecue.planner', compact('categories'));
    }

    public function calculate(BarbecuePlanRequest $request)
    {
        $data = $request->validated();
        $result = $this->plannerService->plan(
            $data['men'],
            $data['women'],
            $data['children'],
            $data['types'] ?? null
        );

        // Recarrega categorias e tipos para reexibir o formulário
        $categories = BarbecueCategory::with(['itemTypes' => function ($q) {
            $q->where('active', true)->orderBy('name');
        }])->orderBy('name')->get();

        return view('barbecue.planner', [
            'categories' => $categories,
            'result' => $result,
        ]);
    }
}
