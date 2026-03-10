<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\OrderRequest;
use App\Http\Requests\Client\AddressRequest;
use App\Http\Requests\Client\ProfileRequest;
use Core\Info\Models\District;
use Core\Users\Models\User;
use Core\Orders\Models\Order;
use Core\Orders\Models\OrderItem;
use Core\Users\Models\Point;
use Core\Wallet\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AddressController extends Controller
{
  

    public function address()
    {
        $user = Auth::user();
        $addresses = $user->addresses()->latest()->get();
        $coveredAreas = District::with('mapPoints')->get()->toJson();
        
        return view('client.address', compact('addresses', 'coveredAreas'));
    }
    public function store(AddressRequest $request)
    {
        try {
            $user = Auth::user();
            
            $user->addresses()->create([
                'name' => $request->name,
                'location' => $request->location,
                'lat' => $request->lat,
                'lng' => $request->lng,
                'city_id' => $request->city_id,
                'district_id' => $request->district_id,
                'is_default' =>  false,
                'status' => 'not-active',

            ]);

          
            return redirect()->route('client.address.index')->with('success', trans('client.address_created_success'));
            
        } catch (\Exception $e) {
            report($e);
            return back()->withErrors(['error' => trans('client.address_creation_failed')])->withInput();
        }
    }

    public function update(AddressRequest $request, $id)
    {
        try {
            $user = Auth::user();
            $address = $user->addresses()->findOrFail($id);
            
            $address->update([
                'name' => $request->title,
                'location' => $request->address,
                'lat' => $request->lat,
                'lng' => $request->lng,
                'city_id' => $request->city_id,
                'district_id' => $request->district_id,
                'is_default' => $request->is_default ?? false,
                'status' => 'not-active',
            ]);

            // If this is set as default, unset others
            if ($request->is_default) {
                $user->addresses()
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }

            return redirect()->route('client.address.index')->with('success', trans('client.address_updated_success'));
            
        } catch (\Exception $e) {
            report($e);
            return back()->withErrors(['error' => trans('client.address_update_failed')])->withInput();
        }
    }

    public function delete($id)
    {
        try {
            $user = Auth::user();
            $address = $user->addresses()->findOrFail($id);
            $address->delete();

            return redirect()->route('client.address.index')->with('success', trans('client.address_deleted_success'));
            
        } catch (\Exception $e) {
            report($e);
            return back()->withErrors(['error' => trans('client.address_deletion_failed')]);
        }
    }


} 