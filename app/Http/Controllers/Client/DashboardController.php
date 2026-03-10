<?php

namespace App\Http\Controllers\Client;

use App\DataResources\B2BOrderResource;
use Core\Categories\Models\Category;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\OrderRequest;
use App\Http\Requests\Client\AddressRequest;
use App\Http\Requests\Client\PasswordRequest;
use App\Http\Requests\Client\ProfileRequest;
use App\Http\Requests\Client\StoreCustomerPriceRequest;
use App\Http\Requests\Client\UpdateCustomerPriceRequest;
use Core\Categories\Models\CategoryDateTime;
use Core\Categories\Services\CategoryDateTimesService;
use Core\Info\Models\City;
use Core\Info\Models\District;
use Core\MediaCenter\Helpers\MediaCenterHelper;
use Core\Orders\Helpers\OrderHelper;
use Core\Users\Models\User;
use Core\Users\Models\Contract;
use Core\Users\Models\ContractsPrice;
use Core\Users\Models\ContractsCustomerPrice;
use Core\Products\Models\Product;
use Core\Orders\Models\Order;
use Core\Orders\Models\OrderItem;
use Core\Orders\Services\OrdersService;
use Core\Settings\Traits\ApiResponse;
use Core\Users\Models\Address;
use Core\Users\Models\Point;
use Core\Users\Models\Profile;
use Core\Wallet\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    use ApiResponse;
    public function __construct(protected OrdersService $ordersService)
    {
    }
    public function dashboard()
    {
        $user = Auth::user();
        $orders = Order::where('orders.client_id', $user->id)
            ->with(['items.product.translations', 'orderRepresentatives.representative'])
            ->leftJoin('order_representatives as or_receiver', function ($join) {
                $join->on('orders.id', '=', 'or_receiver.order_id')
                    ->where('or_receiver.type', '=', 'receiver');
            })
            ->select('orders.*')
            ->orderByRaw('or_receiver.date IS NOT NULL DESC')
            ->orderByDesc('or_receiver.date')
            ->orderByDesc('orders.id')
            ->take(5)
            ->get();
        $totalOrders = Order::where('client_id', $user->id)->count();

        $addresses = Address::where('user_id', $user->id)
            ->where('status', 'active')->get();


        $branchCount = Address::where('user_id', $user->id)->count();
        $monthlyInvoiceTotal = Order::where('client_id', $user->id)
            ->whereIn('status', ['delivered', 'finished'])
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('total_price');

        return view('client.dashboard', compact(
            'user',
            'totalOrders',
            'addresses',
            'orders',
            'branchCount',
            'monthlyInvoiceTotal'
        ));
    }
    public function clientsOrders()
    {
        $user = Auth::user();
        $totalOrdersCount = Order::where('operator_id', $user->id)->count();
        $ordersTotalPrice = Order::where('operator_id', $user->id)->sum('total_price');
        $ordersTotalOriginalPrice = Order::where('operator_id', $user->id)->sum('original_products_total');
        $ordersTotalDeliveryPrice = Order::where('operator_id', $user->id)->sum('delivery_price');
        $totalProfit = $ordersTotalPrice - $ordersTotalOriginalPrice - $ordersTotalDeliveryPrice;


        return view('client.clients-orders', compact(
            'user',
            'totalOrdersCount',
            'ordersTotalPrice',
            'ordersTotalOriginalPrice',
            'ordersTotalDeliveryPrice',
            'totalProfit'
        ));
    }

    public function clientsOrdersData(Request $request)
    {
        $user = Auth::user();
        $query = Order::where('orders.operator_id', $user->id)
            ->with(['items.product.translations', 'orderRepresentatives.representative', 'client', 'orderRepresentatives'])
            ->leftJoin('order_representatives as or_receiver', function ($join) {
                $join->on('orders.id', '=', 'or_receiver.order_id')
                    ->where('or_receiver.type', '=', 'receiver');
            })
            ->select('orders.*');

        $totalData = $query->count();

        $query = $query->b2b('client');

        $limit = request()->input('length');
        $start = request()->input('start');
        $recordsFiltered = $query->count();

        $orders = $query->offset($start)
            ->limit($limit)
            ->get();


        return $this->returnData(trans('data loaded'), [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($recordsFiltered),
            "data" => B2BOrderResource::collection($orders)
        ]);
    }


    public function orders()
    {
        return view('client.orders');
    }

    public function ordersData(Request $request)
    {
        $user = Auth::user();
        $query = Order::where('orders.client_id', $user->id)
            ->with(['items.product.translations', 'orderRepresentatives.representative', 'client', 'orderRepresentatives'])
            ->leftJoin('order_representatives as or_receiver', function ($join) {
                $join->on('orders.id', '=', 'or_receiver.order_id')
                    ->where('or_receiver.type', '=', 'receiver');
            })
            ->select('orders.*');

        $totalData = $query->count();

        $query = $query->b2b('client');

        $limit = request()->input('length');
        $start = request()->input('start');
        $recordsFiltered = $query->count();

        $orders = $query->offset($start)
            ->limit($limit)
            ->get();


        return $this->returnData(trans('data loaded'), [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($recordsFiltered),
            "data" => B2BOrderResource::collection($orders)
        ]);
    }

    public function monthlyInvoices()
    {
        $user = Auth::user();
        $monthlyInvoices = Order::where(function ($query) use ($user) {
            $query->where('client_id', $user->id)
                ->orWhere('operator_id', $user->id);
        })
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total_price) as total_amount, COUNT(id) as orders_count')
            ->groupBy('year', 'month')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        return view('client.monthly-invoices', compact('monthlyInvoices'));
    }

    public function monthlyInvoiceDetails($year, $month)
    {
        $user = Auth::user();
        $contract = Contract::where('client_id', $user->id)->currentActive()->first();
        $settings = \Core\Settings\Models\Setting::pluck('value', 'key');

        $baseQuery = Order::where(function ($query) use ($user) {
            $query->where('client_id', $user->id)
                ->orWhere('operator_id', $user->id);
        })
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month);

        $totalAmount = $baseQuery->sum('total_price');
        $ordersCount = $baseQuery->count();

        $orders = $baseQuery->latest()->get();

        return view('client.monthly-invoice-details', compact('orders', 'year', 'month', 'totalAmount', 'ordersCount', 'contract', 'settings'));
    }

    public function showOrder($id)
    {
        $user = Auth::user();
        $order = Order::where(function ($query) use ($user) {
            $query->where('orders.client_id', $user->id)
                ->orWhere('orders.operator_id', $user->id);
        })
            ->with(['items.product.translations', 'orderRepresentatives.representative', 'client', 'orderRepresentatives'])
            ->findOrFail($id);

        return view('client.partials._order_modal_content', compact('order'))->render();
    }

    public function invoice($id)
    {
        $user = Auth::user();
        $order = Order::where(function ($query) use ($user) {
            $query->where('orders.client_id', $user->id)
                ->orWhere('orders.operator_id', $user->id);
        })
            ->with(['items.product.translations', 'client', 'orderRepresentatives', 'city'])
            ->findOrFail($id);

        return view('client.invoice', compact('order'));
    }

    public function orderStore(OrderRequest $request)
    {
        try {
            $data = $request->all();
            $receivingTime = CategoryDateTime::find($request->receiving_time);
            $deliveryTime = CategoryDateTime::find($request->delivery_time);
            $data['receiving_time'] = $receivingTime->from;
            $data['receiving_to_time'] = $receivingTime->to;
            $data['delivery_time'] = $deliveryTime->from;
            $data['delivery_to_time'] = $deliveryTime->to;
            $data['order_price'] = 0;
            $data['type'] = 'fastorder';
            $data['pay_type'] = 'contract';
            $orderUser = Auth::user();
            $desc = $request->service_type;
            if ($request->order_type === 'customer') {
                $user = User::withTrashed()->where('phone', $request->customer_phone)->first();
                if ($user->deleted_at) {
                    $user->restore();
                }
                if ($user) {
                    $user->update([
                        'fullname' => !empty($user->email) ? $user->email : $request->customer_name,
                        'operator_id' => $orderUser->id,
                        'contract_note' => 'ملاحظة' . $request->notes
                    ]);
                } else {
                    $user = User::create([
                        'phone' => $request->customer_phone,
                        'fullname' => $request->customer_name,
                        'operator_id' => $orderUser->id,
                        'contract_note' => 'ملاحظة' . $request->notes
                    ]);
                }
                if (isset($request->customer_email)) {
                    $emailUser = User::where('email', $request->customer_email)->doesntExist();
                    if ($emailUser) {
                        $user->update([
                            'email' => $request->customer_email
                        ]);
                    }
                }
                $orderUser = $user;
            } else {
                $desc .= " " . $request->notes;
                ;
            }
            $data['desc'] = $desc;
            $this->ordersService->createOrder($data, [], $orderUser);
            if ($request->order_type === 'customer') {
                return redirect()->route('client.clientsOrders')->with('success', trans('client.order_created_success'));
            }
            return redirect()->route('client.dashboard')->with('success', trans('client.order_created_success'));
        } catch (\Exception $e) {
            report($e);
            dd($e);
            return back()->withErrors(['error' => trans('client.order_creation_failed')])->withInput();
        }
    }

    public function getDatesTimes(Request $request)
    {
        $address = Address::find($request->delivery_address_id);
        $dates = CategoryDateTimesService::getDateTimes(OrderHelper::getOrderType($request->type), request('category_id'), $address);
        $dates = CategoryDateTimesService::getDateTimesFormatted(null, $dates);
        return $this->returnData(trans('order was created'), ['status' => 'success', 'data' => $dates]);
    }

    public function points()
    {
        $user = Auth::user();
        $points = Point::where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('client.points', compact('user', 'points'));
    }

    public function contracts()
    {
        $user = Auth::user();
        $contracts = Contract::where('client_id', $user->id)
            ->with(['contractPrices.product'])
            ->paginate(10);
        return view('client.contracts', compact('contracts'));
    }

    public function contract()
    {
        $user = Auth::user();
        $contract = Contract::where('client_id', $user->id)
            ->currentActive()
            ->with(['contractPrices.product', 'client'])
            ->firstOrFail();
        return view('client.contract-print', compact('contract'));
    }

    public function customerPrices()
    {
        $user = Auth::user();
        $contract = Contract::where('client_id', $user->id)
            ->currentActive()
            ->with(['contractCustomerPrices.product', 'contractPrices.product'])
            ->firstOrFail();

        $categoryIds = $contract->contractPrices->pluck('product.category_id')->unique();
        $subCategoryIds = $contract->contractPrices->pluck('product.sub_category_id')->unique();

        $categories = Category::doesntHave('parent')->where('type', 'clothes')->get();
        $subCategories = Category::has('parent')->where('type', 'clothes')->get();

        return view('client.customer-prices', compact('contract', 'categories', 'subCategories'));
    }

    public function searchProducts(Request $request)
    {
        $user = Auth::user();
        $contract = Contract::where('client_id', $user->id)
            ->currentActive()
            ->with('contractCustomerPrices')
            ->firstOrFail();

        $search = $request->get('q');
        $categoryId = $request->get('category_id');
        $subCategoryId = $request->get('sub_category_id');

        $query = Product::with(['category', 'subCategory']);
        if ($search) {
            $query->whereTranslationLike('name', "%$search%");
        }
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        if ($subCategoryId) {
            $query->where('sub_category_id', $subCategoryId);
        }

        $prices = $query->get()
            ->map(function ($product) use ($contract) {
                $customerPrice = $contract->contractCustomerPrices->where('contract_id', $contract->id)
                    ->where('product_id', $product->id)
                    ->first();

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product?->category?->name ?? '',
                    'sub_category' => $product?->subCategory?->name ?? '',
                    'clean_station_price' => $product->price,
                    'over_price' => $customerPrice ? $customerPrice->over_price : 0,
                    'customer_price_id' => $customerPrice ? $customerPrice->id : null,
                ];
            });

        return response()->json($prices);
    }

    public function customerPricesStore(StoreCustomerPriceRequest $request)
    {
        try {
            $contract = Contract::where('client_id', Auth::id())
                ->currentActive()
                ->firstOrFail();

            // Check if the product already has a customer price
            $existing = ContractsCustomerPrice::where('contract_id', $contract->id)
                ->where('product_id', $request->product_id)
                ->first();

            if ($existing) {
                return $this->returnErrorMessage(trans('client.product_already_exists'), [], [], 400);
            }

            $customerPrice = ContractsCustomerPrice::create([
                'contract_id' => $contract->id,
                'product_id' => $request->product_id,
                'over_price' => $request->over_price,
            ]);

            return $this->returnData(trans('client.customer_price_added_success'), [
                'data' => [
                    'id' => $customerPrice->id
                ]
            ]);
        } catch (\Exception $e) {
            report($e);
            return $this->returnErrorMessage(trans('client.customer_price_add_failed'), [], [], 500);
        }
    }

    public function customerPricesUpdate(UpdateCustomerPriceRequest $request, $priceId)
    {
        try {
            $customerPrice = ContractsCustomerPrice::with('contract')->findOrFail($priceId);

            // Verify the contract belongs to the authenticated user
            if ($customerPrice->contract->client_id !== Auth::id()) {
                return $this->returnErrorMessage(trans('client.unauthorized_access'), [], [], 403);
            }

            $customerPrice->update([
                'over_price' => $request->over_price,
            ]);

            return $this->returnData(trans('client.customer_price_updated_success'), [
                'data' => [
                    'over_price' => $customerPrice->over_price,
                ]
            ]);
        } catch (\Exception $e) {
            report($e);
            return $this->returnErrorMessage(trans('client.customer_price_update_failed'), [], [], 500);
        }
    }

    public function customerPricesDelete($priceId)
    {
        try {
            $customerPrice = ContractsCustomerPrice::with('contract')->findOrFail($priceId);

            // Verify the contract belongs to the authenticated user
            if ($customerPrice->contract->client_id !== Auth::id()) {
                return $this->returnErrorMessage(trans('client.unauthorized_access'), [], [], 403);
            }

            $customerPrice->delete();

            return $this->returnSuccessMessage(trans('client.customer_price_deleted_success'));
        } catch (\Exception $e) {
            report($e);
            return $this->returnErrorMessage(trans('client.customer_price_delete_failed'), [], [], 500);
        }
    }



    public function updateProfile()
    {
        $user = Auth::user();
        $cities = City::all();
        $districts = District::all();
        return view('client.profile', compact('user', 'cities', 'districts'));
    }

    public function updateProfileStore(ProfileRequest $request)
    {
        try {
            $user = Auth::user();

            $data = [
                'fullname' => $request->fullname,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
            ];

            if ($request->hasFile('logo')) {
                $media = MediaCenterHelper::saveMedia($request->logo, 'avatar');
                if ($media && is_object($media)) {
                    $imageName = $media->file_name;
                    $data['image'] = $imageName;
                }
            }

            $user->update($data);
            Profile::updateOrCreate([
                'user_id' => $user->id,
            ], [
                'city_id' => $request->city_id,
                'district_id' => $request->district_id,
            ]);

            return redirect()->route('client.profile.update-profile')->with('success', trans('client.profile_updated_success'));
        } catch (\Exception $e) {
            report($e);
            return back()->withErrors(['error' => trans('client.profile_update_failed')])->withInput();
        }
    }

    public function updatePassword(PasswordRequest $request)
    {
        $user = Auth::user();
        if (Hash::check($request->current_password, $user->password)) {
            $user->update(['password' => Hash::make($request->password)]);
            return redirect()->route('client.profile.update-profile')->with('success', trans('client.password_updated_success'));
        } else {
            return back()->withErrors(['error' => trans('client.password_incorrect')])->withInput();
        }
        return redirect()->route('client.profile.update-profile')->with('success', trans('client.password_updated_success'));
    }
}
