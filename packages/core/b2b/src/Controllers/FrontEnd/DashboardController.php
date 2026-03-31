<?php

namespace Core\B2B\Controllers\FrontEnd;

use Core\B2B\DataResources\B2BOrderResource;
use Core\B2B\Models\Company;
use Core\B2B\Models\CompanyBranch;
use Core\B2B\Requests\FrontEnd\UserProfileRequest;
use Core\Categories\Models\Category;
use App\Http\Controllers\Controller;
use Core\B2B\Requests\FrontEnd\OrderRequest;
use Core\B2B\Requests\FrontEnd\PasswordRequest;
use Core\B2B\Requests\FrontEnd\ProfileRequest;
use Core\B2B\Requests\FrontEnd\StoreCustomerPriceRequest;
use Core\B2B\Requests\FrontEnd\UpdateCustomerPriceRequest;
use Core\Categories\Models\CategoryDateTime;
use Core\Categories\Services\CategoryDateTimesService;
use Core\Info\Models\City;
use Core\Info\Models\District;
use Core\MediaCenter\Helpers\MediaCenterHelper;
use Core\Orders\Helpers\OrderHelper;
use Core\Orders\Models\OrderItem;
use Core\Users\Models\User;
use Core\B2B\Models\Contract;
use Core\B2B\Models\ContractsCustomerPrice;
use Core\Products\Models\Product;
use Core\Orders\Models\Order;
use Core\Orders\Services\OrdersService;
use Core\Settings\Traits\ApiResponse;
use Core\Users\Models\Address;
use Core\Users\Models\Point;
use Core\Users\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Core\B2B\Helpers\B2BHelper;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    use ApiResponse;
    public function __construct(protected OrdersService $ordersService)
    {
    }
    public function dashboard()
    {
        B2BHelper::checkPermission('dashboard-analytics');
        $user = Auth::user();

        

        $totalOrders = Order::query()->b2b('both')->count();

        $addresses = CompanyBranch::b2bUnderManagement('manage-orders')->active()->latest()->get();


        $branchCount = CompanyBranch::b2bUnderManagement('manage-orders')->count();
        $monthlyInvoiceTotal = Order::query()->b2b('both')
            ->validOrders()
            ->where('created_at', '>=', now()->startOfMonth())
            ->where('created_at', '<=', now()->endOfMonth())
            ->sum('total_price');
        $totalPendingOrders = Order::query()->b2b('both')
            ->whereNotIn('status', ['pending_payment','cancel_payment','cancelled', 'delivered', 'finished'])
            ->count();

        $title = trans('client.dashboard');
        $description = trans('client.dashboard_description');

        return view('b2b::web.pages.dashboard', compact(
            'user',
            'totalOrders',
            'totalPendingOrders',
            'addresses',
            'branchCount',
            'monthlyInvoiceTotal',
            'title',
            'description'
        ));
    }
   
    public function analytics()
    {
        B2BHelper::checkPermission('dashboard-analytics');
        
        $title = trans('client.analytics');
        $description = trans('client.analytics_description');

        // 1. Revenue Trend (Last 30 days)
        $revenueTrend = Order::query()
            ->b2b('both')
            ->validOrders()
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, SUM(total_price) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 2. Orders Distribution (by Type/Status)
        $distribution = Order::query()
            ->b2b('both')
            ->validOrders()
            ->selectRaw('b2b_type as type, COUNT(*) as count')
            ->groupBy('b2b_type')
            ->get();

        // 3. Top Items (using OrderItem to be more direct)
        $topItems = OrderItem::whereIn('order_id', Order::query()->b2b('both')->validOrders()->select('id'))
            ->selectRaw('product_id, SUM(quantity) as total_qty')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->with('product.translations')
            ->take(5)
            ->get();
            
        // Map top items to include names
        $topItemsData = $topItems->map(function($item) {
            return [
                'name' => $item->product?->name ?? '---',
                'qty' => $item->total_qty
            ];
        });

        return view('b2b::web.pages.analytics', compact(
            'title',
            'description',
            'revenueTrend',
            'distribution',
            'topItemsData'
        ));
    }
    public function clientsOrders()
    {
        B2BHelper::checkPermission('manage-orders');
        $title = trans('client.guest_orders');
        $description = trans('client.orders_description');

       
        return view('b2b::web.pages.guest-orders', compact(

            'title',
            'description',
           
        ));
    }

    public function clientsOrdersData(Request $request)
    {
        B2BHelper::checkPermission('manage-orders');
     
        $query = Order::with(['items.product.translations', 'orderRepresentatives.representative', 'client', 'orderRepresentatives', 'branch'])
            ->leftJoin('order_representatives as or_receiver', function ($join) {
                $join->on('orders.id', '=', 'or_receiver.order_id')
                    ->where('or_receiver.type', '=', 'receiver');
            })
            ->select('orders.*');

        $totalData = $query->count();

        $query = $query->b2b('client');

        if ($request->filled('from_date')) {
            $query->whereDate('orders.created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('orders.created_at', '<=', $request->to_date);
        }

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
            "data" => B2BOrderResource::collection($orders),
        ]);
    }


    public function orders()
    {
        B2BHelper::checkPermission('manage-orders');
        $title = trans('client.orders');
        $description = trans('client.orders_description');
        return view('b2b::web.pages.orders', compact('title', 'description'));
    }

    public function ordersData(Request $request)
    {
        B2BHelper::checkPermission('manage-orders');
        $query = Order::query()
            ->with(['items.product.translations', 'orderRepresentatives.representative', 'client', 'orderRepresentatives', 'branch'])
            ->leftJoin('order_representatives as or_receiver', function ($join) {
                $join->on('orders.id', '=', 'or_receiver.order_id')
                    ->where('or_receiver.type', '=', 'receiver');
            })
            ->leftJoin('order_representatives as or_delivery', function ($join) {
                $join->on('orders.id', '=', 'or_delivery.order_id')
                    ->where('or_delivery.type', '=', 'delivery');
            })
            ->select('orders.*');

        $totalData = $query->count();

        $query = $query->b2b('company');

        if ($request->filled('from_date')) {
            $query->whereDate('orders.created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('orders.created_at', '<=', $request->to_date);
        }

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
            "data" => B2BOrderResource::collection($orders),
        ]);
    }

    public function monthlyInvoices()
    {
        B2BHelper::checkPermission('invoices-payments');
        $monthlyInvoices = Order::query()
            ->b2b('both')
            ->selectRaw("
                YEAR(created_at) as year, 
                MONTH(created_at) as month, 
                SUM(total_price) as total_amount, 
                SUM(total_coupon) as total_coupon, 
                COUNT(id) as orders_count,
                SUM(CASE WHEN b2b_type = 'company' THEN total_price ELSE 0 END) as contract_cost,
                SUM(CASE WHEN b2b_type = 'client' THEN total_price - b2b_profit ELSE 0 END) as guest_cost,
                SUM(CASE WHEN b2b_type = 'client' THEN b2b_profit ELSE 0 END) as hotel_profit
            ")
            ->groupBy('year', 'month')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        $title = trans('client.monthly_invoice');
        $description = trans('client.monthly_invoices_description');

        return view('b2b::web.pages.invoices', compact('monthlyInvoices', 'title', 'description'));
    }

    public function monthlyInvoiceDetails($year, $month)
    {
        B2BHelper::checkPermission('invoices-payments');
        $companyId = B2BHelper::getB2BCompanyId();
        $company = Company::find($companyId);
        $contract = Contract::where('company_id', $companyId)->currentActive()->first();
        $settings = \Core\Settings\Models\Setting::pluck('value', 'key');

        $baseQuery = Order::query()
            ->b2b('both')
            ->validOrders()
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month);

        $totalAmount = $baseQuery->sum('total_price');
        $ordersCount = $baseQuery->count();

        $orders = $baseQuery->latest()->get();

        $title = trans('client.monthly_invoice_details');
        $description = trans('client.monthly_invoice_details_description');

        return view('b2b::web.pages.monthly-invoice-details', compact('orders', 'company', 'year', 'month', 'totalAmount', 'ordersCount', 'contract', 'settings', 'title', 'description'));
    }

    public function showOrder($id)
    {
        $order = Order::query()
            ->b2b('both')
            ->with(['items.product.translations', 'orderRepresentatives.representative', 'client', 'orderRepresentatives'])
            ->where(function($q) use ($id) {
                $q->where('id', $id)->orWhere('reference_id', $id);
            })
            ->firstOrFail();

        return view('b2b::web.partials.order-modal', compact('order'))->render();
    }

    public function invoice($id)
    {
        $order = Order::query()
            ->b2b('both')
            ->with(['items.product.translations', 'client', 'orderRepresentatives', 'city'])
            ->findOrFail($id);

        $title = trans('client.invoice');
        $description = trans('client.invoice_description');

        return view('b2b::web.pages.invoice', compact('order', 'title', 'description'));
    }

    public function orderStore(OrderRequest $request)
    {
        B2BHelper::checkPermission('manage-orders');
        try {
            $data           = $request->all();
            $receivingTime  = CategoryDateTime::find($request->receiving_time);
            $deliveryTime   = CategoryDateTime::find($request->delivery_time);
            $data['receiving_time']     = $receivingTime->from;
            $data['receiving_to_time']  = $receivingTime->to;
            $data['delivery_time']      = $deliveryTime->from;
            $data['delivery_to_time']   = $deliveryTime->to;
            $data['order_price']        = 0;
            $data['type']               = 'fastorder';
            $data['pay_type']           = 'contract';

            $b2bContext = B2BHelper::getCreationContext();
            $data['client_id']          = $b2bContext['client_id'] ?? null;
            $data['company_id']         = $b2bContext['company_id'];
            $data['branch_id']          = $request->branch_id;
            $data['b2b_type']           = $request->b2b_type;
            
            $orderUser = User::find($data['client_id']);
            if(!$orderUser){
                $orderUser = Auth::user();
            }
            $notes = $request->notes ?? '';
            if ($request->room_number) {
                $notes = 'Room: ' . $request->room_number . ' | ' . $notes;
            }
            $desc = $request->service_type;
            if ($request->b2b_type === 'client') {
                $user = User::withTrashed()->where('phone', $request->customer_phone)->first();
                if (isset($user) and $user->deleted_at) {
                    $user->restore();
                }
                if ($user) {
                    $user->update([
                        'fullname' => !empty($user->email) ? $user->email : $request->customer_name,
                        'company_id' => $b2bContext['company_id'],
                        'contract_note' => 'ملاحظة: ' . $notes
                    ]);
                } else {
                    $user = User::create([
                        'phone' => $request->customer_phone,
                        'fullname' => $request->customer_name,
                        'company_id' => $b2bContext['company_id'],
                        'contract_note' => 'ملاحظة: ' . $notes
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
                $desc .= " | " . $notes;
            }
            $data['desc'] = $desc;
            $order = $this->ordersService->createOrder($data, [], $orderUser);
            if ($request->b2b_type === 'client') {
                return $this->returnData(trans('order_created_success'), [
                    'url' => route('client.clientsOrders')
                ]);
            }
            return $this->returnData(trans('order_created_success'), [
                'url' => route('client.order.index')
            ]);
        } catch (\Exception $e) {
            report($e);
            return $this->returnErrorMessage(trans('client.order_creation_failed'), [], ['error' => $e->getMessage()], 500);
        }
    }

    public function getDatesTimes(Request $request)
    {
        $branch = null;
        if ($request->branch_id) {
            $branch = CompanyBranch::find($request->branch_id);
        }

        $dates = CategoryDateTimesService::getDateTimes('clothes', request('category_id'), $branch);
        $dates = CategoryDateTimesService::getDateTimesFormatted(null, $dates);
        return $this->returnData(trans('client.data_loaded'), ['status' => 'success', 'data' => $dates]);
    }

    public function points()
    {
        $user = Auth::user();
        $points  = [];
        $title = trans('client.points');
        $description = trans('client.points_description');

        return view('b2b::web.pages.points', compact('user', 'points', 'title', 'description'));
    }

    public function contracts()
    {
        B2BHelper::checkPermission('manage-orders');
        $user = Auth::user();
        $companyId = B2BHelper::getB2BCompanyId();
        $contracts = Contract::where('company_id', $companyId)
            ->with(['contractPrices.product'])
            ->first();
        $title = trans('client.contracts');
        $description = trans('client.contracts_description');
        return view('b2b::web.pages.contracts', compact('contracts', 'title', 'description'));
    }

    public function contract()
    {
        B2BHelper::checkPermission('manage-orders');
        $companyId = B2BHelper::getB2BCompanyId();
        $contract = Contract::where('company_id', $companyId)
            ->latest()
            ->with(['contractPrices.product', 'client'])
            ->firstOrFail();
        $title = trans('client.contract');
        $description = trans('client.contract_description');
        return view('b2b::web.pages.subscription', compact('contract', 'title', 'description'));
    }

    public function customerPrices()
    {
        B2BHelper::checkPermission('edit-guest-pricing');
        $companyId = B2BHelper::getB2BCompanyId();
        $contract = Contract::where('company_id', $companyId)
            ->currentActive()
            ->with(['contractCustomerPrices', 'contractPrices'])
            ->firstOrFail();

        // Get all active products of type 'clothes' with translations to avoid N+1
        $allProducts = Product::where('type', 'clothes')

            ->whereDoesntHave('category',function($query){
                $query->whereTranslationLike("name","%b2b%")->orWhere("id",43);
            })
            ->where('status', 'active')
            ->with(['translations', 'category.translations', 'subCategory.translations'])
            ->get();

        $contractPrices = $contract->contractPrices;
        $customerPrices = $contract->contractCustomerPrices;

        $mappedProducts = $allProducts->map(function ($product) use ($contractPrices, $customerPrices) {
            $cp = $contractPrices->where('product_id', $product->id)->first();
            $ccp = $customerPrices->where('product_id', $product->id)->first();
            
            return [
                'id' => $product->id,
                'name' => $product->name,
                'base_price' => $cp ? $cp->price : ($product->price ?? 0),
                'over_price' => $ccp ? $ccp->over_price : 0,
                'category_id' => $product->category_id,
                'category_name' => $product->category?->name ?? trans('client.other'),
                'sub_category_name' => $product->subCategory?->name ?? '',
            ];
        });

        $groupedProducts = $mappedProducts->groupBy('category_name');

        $title = trans('client.customer_overprices');
        $description = trans('client.customer_prices_description');

        return view('b2b::web.pages.overprices', compact('contract', 'groupedProducts', 'title', 'description'));
    }

    public function customerPricesBulkStore(Request $request)
    {
        B2BHelper::checkPermission('edit-guest-pricing');
        try {
            $companyId = B2BHelper::getB2BCompanyId();
            $contract = Contract::where('company_id', $companyId)
                ->currentActive()
                ->firstOrFail();

            $overprices = $request->input('overprices', []);

            foreach ($overprices as $productId => $overPrice) {
                if($overPrice > 0){
                    ContractsCustomerPrice::updateOrCreate(
                        ['contract_id' => $contract->id, 'product_id' => $productId],
                        ['over_price' => $overPrice ?? 0]
                    );
                }else{
                    ContractsCustomerPrice::where('contract_id', $contract->id)->where('product_id', $productId)->forceDelete();
                }
            }

            return $this->returnData(trans('client.customer_price_updated_success'), [
                'success' => true
            ]);
        } catch (\Exception $e) {
            report($e);
            return $this->returnErrorMessage(trans('client.customer_price_update_failed'), [], [], 500);
        }
    }

    public function searchProducts(Request $request)
    {
        B2BHelper::checkPermission('edit-guest-pricing');
        $companyId = B2BHelper::getB2BCompanyId();
        $contract = Contract::where('company_id', $companyId)
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
        B2BHelper::checkPermission('edit-guest-pricing');
        try {
            $companyId = B2BHelper::getB2BCompanyId();
            $contract = Contract::where('company_id', $companyId)
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
        B2BHelper::checkPermission('edit-guest-pricing');
        try {
            $customerPrice = ContractsCustomerPrice::with('contract')->findOrFail($priceId);

            // Verify the contract belongs to the authenticated user
            if ($customerPrice->contract->company_id !== B2BHelper::getB2BCompanyId()) {
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
        B2BHelper::checkPermission('edit-guest-pricing');
        try {
            $customerPrice = ContractsCustomerPrice::with('contract')->findOrFail($priceId);

            // Verify the contract belongs to the authenticated user
            if ($customerPrice->contract->company_id !== B2BHelper::getB2BCompanyId()) {
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
        $title = trans('client.profile');
        $description = trans('client.profile_description');
        return view('b2b::web.pages.user-settings', compact('user', 'title', 'description'));
    }

    public function updateProfileStore(UserProfileRequest $request)
    {
        try {
            $user = Auth::user();

            $data = [
                'fullname' => $request->fullname,
                'email' => $request->email,
                'phone' => $request->phone,
                'line_of_business' => $request->line_of_business,
            ];

            if ($request->hasFile('avatar')) {
                $media = MediaCenterHelper::saveMedia($request->avatar, 'avatar');
                if ($media && is_object($media)) {
                    $imageName = $media->file_name;
                    $data['image'] = $imageName;
                }
            }
            $user->update($data);

            return redirect()->route('client.profile.update-profile')->with('success', trans('client.profile_updated_success'));
        } catch (\Exception $e) {
            report($e);
            return back()->withErrors(['error' => trans('client.profile_update_failed')])->withInput();
        }
    }

    public function updatePassword(PasswordRequest $request)
    {
        try {
            $user = Auth::user();
            $user->update([
                'password' => bcrypt($request->password)
            ]);

            return redirect()->route('client.profile.update-profile')->with('success', trans('client.profile_updated_success'));
        } catch (\Exception $e) {
            report($e);
            return back()->withErrors(['error' => trans('client.profile_update_failed')]);
        }
    }
}
