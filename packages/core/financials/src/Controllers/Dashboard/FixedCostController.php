<?php

namespace Core\Financials\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Financials\Models\FixedCost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FixedCostController extends Controller
{
    public function index()
    {
        $title = trans('Fixed Costs Management'); 
        $screen = 'fixed-costs-index';
        $fixedCosts = FixedCost::orderBy('created_at', 'desc')->paginate(15);
        return view('financials::pages.fixed-costs.index', compact('title', 'screen', 'fixedCosts'));
    }

    public function create()
    {
        $title = trans('Add New Fixed Cost');
        $screen = 'fixed-costs-create';
        return view('financials::pages.fixed-costs.create', compact('title', 'screen'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'frequency' => 'required|in:monthly,quarterly,yearly',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        FixedCost::create($request->all());

        return redirect()->route('dashboard.fixed-costs.index')
            ->with('success', trans('Fixed cost created successfully'));
    }

    public function show(FixedCost $fixedCost)
    {
        $title = trans('Fixed Cost Details');
        $screen = 'fixed-costs-show';
        return view('financials::pages.fixed-costs.show', compact('title', 'screen', 'fixedCost'));
    }

    public function edit(FixedCost $fixedCost)
    {
        $title = trans('Edit Fixed Cost');
        $screen = 'fixed-costs-edit';
        return view('financials::pages.fixed-costs.edit', compact('title', 'screen', 'fixedCost'));
    }

    public function update(Request $request, FixedCost $fixedCost)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'frequency' => 'required|in:monthly,quarterly,yearly',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $fixedCost->update($request->all());

        return redirect()->route('dashboard.fixed-costs.index')
            ->with('success', trans('Fixed cost updated successfully'));
    }

    public function destroy(FixedCost $fixedCost)
    {
        $fixedCost->delete();

        return redirect()->route('dashboard.fixed-costs.index')
            ->with('success', trans('Fixed cost deleted successfully'));
    }


}
