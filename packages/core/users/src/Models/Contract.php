<?php

namespace Core\Users\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Users\Observers\ContractObserver;

use Illuminate\Database\Eloquent\Model;
use App\Observers\GlobalModelObserver;
use Core\Settings\Models\CoreModel;
use Carbon\Carbon;


#[ObservedBy([ContractObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class Contract extends CoreModel
{
    protected $table = 'contracts';
    protected $fillable = ['title', 'months_count', 'month_fees', 'max_allowed_over_price', 'unlimited_days', 'number_of_days', 'contract', 'start_date', 'end_date', 'client_id', 'creator_id', 'updater_id', 'commercial_registration', 'tax_number'];
    protected $guarded = [];
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'unlimited_days' => 'boolean',
    ];

    //start Scopes
    function scopeSearch($query)
    {

        //filter text on  title
        if ((request()->has("filters.title")) and !empty(request("filters.title"))) {
            $query->where("title", "LIKE", "%" . request("filters.title") . "%");
        }

        //filter by number on  months_count
        if ((request()->has("filters.months_count")) and !empty(request("filters.months_count"))) {
            $query->where("months_count", request("filters.months_count"));
        }

        //filter by number on  month_fees
        if ((request()->has("filters.month_fees")) and !empty(request("filters.month_fees"))) {
            $query->where("month_fees", request("filters.month_fees"));
        }

        //filter by boolean on  unlimited_days
        if ((request()->has("filters.unlimited_days")) and !empty(request("filters.unlimited_days"))) {
            $query->where("unlimited_days", request("filters.unlimited_days"));
        }

        //filter by number on  number_of_days
        if ((request()->has("filters.number_of_days")) and !empty(request("filters.number_of_days"))) {
            $query->where("number_of_days", request("filters.number_of_days"));
        }

        //filter date on  start_date
        if ((request()->has("filters.from_start_date")) and !empty(request("filters.from_start_date"))) {
            $query->where("start_date", ">=", Carbon::parse(request("filters.from_start_date")));
        }

        if ((request()->has("filters.to_start_date")) and !empty(request("filters.to_start_date"))) {
            $query->where("start_date", "<=", Carbon::parse(request("filters.to_start_date")));
        }

        //filter date on  end_date
        if ((request()->has("filters.from_end_date")) and !empty(request("filters.from_end_date"))) {
            $query->where("end_date", ">=", Carbon::parse(request("filters.from_end_date")));
        }

        if ((request()->has("filters.to_end_date")) and !empty(request("filters.to_end_date"))) {
            $query->where("end_date", "<=", Carbon::parse(request("filters.to_end_date")));
        }

        //filter date on  created_at
        if ((request()->has("filters.from_created_at")) and !empty(request("filters.from_created_at"))) {
            $query->whereDate("created_at", ">=", Carbon::parse(request("filters.from_created_at")));
        }

        if ((request()->has("filters.to_created_at")) and !empty(request("filters.to_created_at"))) {
            $query->whereDate("created_at", "<=", Carbon::parse(request("filters.to_created_at")));
        }

        //filter date on  updated_at
        if ((request()->has("filters.from_updated_at")) and !empty(request("filters.from_updated_at"))) {
            $query->whereDate("updated_at", ">=", Carbon::parse(request("filters.from_updated_at")));
        }

        if ((request()->has("filters.to_updated_at")) and !empty(request("filters.to_updated_at"))) {
            $query->whereDate("updated_at", "<=", Carbon::parse(request("filters.to_updated_at")));
        }

        if (request()->has('trash') and request()->trash == 1) {
            $query->onlyTrashed();
        }
    }
    function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }
    function scopeCurrentActive($query)
    {
        return $query->where(function ($query) {
            $query->where('start_date', '<=', Carbon::now())->orWhere('start_date', null);
        })->where(function ($query) {
            $query->where('end_date', '>=', Carbon::now())->orWhere('end_date', null);
        });
    }
    //end Scopes

    //start relations

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id', 'id');
    }

    public function contractPrices()
    {
        return $this->hasMany(ContractsPrice::class, 'contract_id', 'id');
    }

    public function contractCustomerPrices()
    {
        return $this->hasMany(ContractsCustomerPrice::class, 'contract_id', 'id');
    }

    //end relations

    //start Attributes

    public function getActionsAttribute()
    {
        $slug = 'contracts';
        $actions = '<div class ="d-flex justify-content-center">';
        if (request()->has('trash') and request()->trash == 1) {
            if (auth('web')->check() and auth('web')->user()->can('dashboard.' . $slug . '.restore')) {
                $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1 btn-restore" href="' . route('dashboard.' . $slug . '.restore', ['id' => $this->id]) . '">
                    <i class="fa fa-trash-restore"></i> <span>' . trans('restore') . '</span>
                    </a>';
            }
            $actions .= '</div>';
            return $actions;
        }
        if (auth('web')->check() and auth('web')->user()->can('dashboard.contracts.qr-codes.form')) {
            $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1" href="' . route('dashboard.' . $slug . '.qr-codes.form', ['contract_id' => $this->id]) . '"> 
                <i class="fas fa-qrcode"></i><span> ' . trans('Create QR Codes') . ' </span>
                </a>';
        }
        if (auth('web')->check() and auth('web')->user()->can('dashboard.' . $slug . '.show')) {
            $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1 " href="' . route('dashboard.' . $slug . '.show', ['id' => $this->id]) . '"> 
                <i class="fa fa-eye"></i> <span>' . trans('show') . '</span>
                </a>';
        }
        if (auth('web')->check() and auth('web')->user()->can('dashboard.' . $slug . '.edit')) {
            $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1 " href="' . route('dashboard.' . $slug . '.edit', ['id' => $this->id]) . '"> 
                <i class="fa fa-edit"></i> <span>' . trans('edit') . '</span>
                </a>';
        }
        if (auth('web')->check() and auth('web')->user()->can('dashboard.' . $slug . '.delete')) {
            $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1 delete-btn" href="' . route('dashboard.' . $slug . '.delete', ['id' => $this->id]) . '"> 
                <i class="fa fa-trash"></i><span> ' . trans('delete') . ' </span>
                </a>';
        }


        $actions .= '</div>';
        return $actions;

    }

    public function getItemsActionsAttribute()
    {
        return $this->getItemsActions('contracts');
    }
    public function getShowActionsAttribute()
    {
        return $this->getShowActions('contracts');
    }

    public function getItemDataAttribute()
    {
        return $this->getItemData('contracts');
    }
    //end Attributes

}
