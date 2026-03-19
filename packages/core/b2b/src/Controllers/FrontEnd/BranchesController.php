<?php

namespace Core\B2B\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Core\B2B\Helpers\B2BHelper;
use Core\B2B\Models\CompanyBranch;
use Core\Info\Models\City;
use Core\Info\Models\District;
use Core\B2B\Models\Company;
use Core\B2B\Requests\FrontEnd\BrancheRequest;
use Illuminate\Support\Facades\Auth;


class BranchesController extends Controller
{


    public function branches()
    {
        B2BHelper::checkPermission('manage-branches');
        $title = trans('client.branches');
        $description = trans('client.branches_description');

        $context = B2BHelper::getCreationContext();
        $companyId = $context['company_id'];

        $branches = CompanyBranch::b2bUnderManagement('manage-branches')->latest()->get();
        $cities = City::where('status', 'active')->with('districts')->get();
        $coveredAreas = District::with('mapPoints')->get()->toJson();

        return view('b2b::web.pages.branches', compact('branches', 'cities', 'coveredAreas', 'title', 'description'));
    }

    public function store(BrancheRequest $request)
    {
        B2BHelper::checkPermission('manage-branches');
        try {
            $context = B2BHelper::getCreationContext();
            $companyId = $context['company_id'];

            CompanyBranch::create([
                'company_id' => $companyId,
                'name' => $request->name,
                'location' => $request->location,
                'lat' => $request->lat,
                'lng' => $request->lng,
                'city_id' => $request->city_id,
                'district_id' => $request->district_id,
                'is_default' => false,
                'creator_id' => Auth::id(),
            ]);

            return redirect()->route('client.branches.index')->with('success', trans('client.branch_created_success'));
        } catch (\Exception $e) {
            report($e);
            return back()->withErrors(['error' => trans('client.branch_creation_failed')])->withInput();
        }
    }

    public function update(BrancheRequest $request, $id)
    {
        B2BHelper::checkPermission('manage-branches');
        try {
            $branch = CompanyBranch::b2bUnderManagement('manage-branches')->findOrFail($id);

            $branch->update([
                'name' => $request->name,
                'location' => $request->location,
                'lat' => $request->lat,
                'lng' => $request->lng,
                'city_id' => $request->city_id,
                'district_id' => $request->district_id,
                'is_default' => $request->is_default ?? false,
                'updater_id' => Auth::id(),
            ]);

            if ($request->is_default) {
                CompanyBranch::b2bUnderManagement('manage-branches')
                    ->where('id', '!=', $branch->id)
                    ->update(['is_default' => false]);
            }

            return redirect()->route('client.branches.index')->with('success', trans('client.branch_updated_success'));
        } catch (\Exception $e) {
            report($e);
            return back()->withErrors(['error' => trans('client.branch_update_failed')])->withInput();
        }
    }

    public function delete($id)
    {
        B2BHelper::checkPermission('manage-branches');
        try {
            $context = B2BHelper::getCreationContext();
            $companyId = $context['company_id'];

            $branch = CompanyBranch::where('company_id', $companyId)->findOrFail($id);
            $branch->delete();

            return redirect()->route('client.branches.index')->with('success', trans('client.branch_deleted_success'));
        } catch (\Exception $e) {
            report($e);
            return back()->withErrors(['error' => trans('client.branch_deletion_failed')]);
        }
    }


}