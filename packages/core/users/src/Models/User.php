<?php

namespace Core\Users\Models;

use App\Observers\GlobalModelObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Users\Observers\UserObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;
use Core\Comments\Models\Comment;
use Core\MediaCenter\Helpers\MediaCenterHelper;
use Core\Notification\Models\BannerNotification;
use Core\Notification\Models\Notification;
use Core\Orders\Models\Order;
use Core\Orders\Models\OrderRepresentative;
use Core\Wallet\Models\WalletTransaction;
use Core\Workers\Models\Worker;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;

#[ObservedBy([GlobalModelObserver::class])]
#[ObservedBy([UserObserver::class])]
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $table             = 'users';
    protected $fillable          = ['image', 'fullname', 'email', 'password', 'email_verified_at', 'phone', 'phone_verified_at', 'is_active', 'is_allow_notify', 'date_of_birth', 'identity_number', 'wallet', 'points_balance', 'gender', 'rate_avg', 'referral_code', 'earned_referral_points', 'earned_referral_riyals', 'verified_code', 'last_login_at', 'operator_id', 'register_by_id', 'creator_id', 'updater_id', 'address', 'business_field', 'appear_at', 'contract_note', 'contract_expiration_date'];
    protected $guarded           = [];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
    ];


    //start Scopes
    function scopeSearch($query)
    {

        //filter text on  fullname
        if ((request()->has("filters.fullname")) and !empty(request("filters.fullname"))) {
            $query->where("fullname", "LIKE", "%" . request("filters.fullname") . "%");
        }

        //filter by email on  email
        if ((request()->has("filters.email")) and !empty(request("filters.email"))) {
            $query->where("email", "LIKE", "%" . request("filters.email") . "%");
        }

        //filter date on  email_verified_at
        if ((request()->has("filters.from_email_verified_at")) and !empty(request("filters.from_email_verified_at"))) {
            $query->whereDate("email_verified_at", ">=", Carbon::parse(request("filters.from_email_verified_at")));
        }

        if ((request()->has("filters.to_email_verified_at")) and !empty(request("filters.to_email_verified_at"))) {
            $query->whereDate("email_verified_at", "<=", Carbon::parse(request("filters.to_email_verified_at")));
        }

        //filter text on  phone
        if ((request()->has("filters.phone")) and !empty(request("filters.phone"))) {
            $query->where("phone", "LIKE", "%" . request("filters.phone") . "%");
        }

        //filter date on  phone_verified_at
        if ((request()->has("filters.from_phone_verified_at")) and !empty(request("filters.from_phone_verified_at"))) {
            $query->whereDate("phone_verified_at", ">=", Carbon::parse(request("filters.from_phone_verified_at")));
        }

        if ((request()->has("filters.to_phone_verified_at")) and !empty(request("filters.to_phone_verified_at"))) {
            $query->whereDate("phone_verified_at", "<=", Carbon::parse(request("filters.to_phone_verified_at")));
        }

        //filter select on  roles
        if ((request()->has("filters.roles")) and !empty(request("filters.roles"))) {
            $query->whereRelation("roles", "id", request("filters.roles"));
        }

        //filter date on  date_of_birth
        if ((request()->has("filters.from_date_of_birth")) and !empty(request("filters.from_date_of_birth"))) {
            $query->where("date_of_birth", ">=", Carbon::parse(request("filters.from_date_of_birth")));
        }

        if ((request()->has("filters.to_date_of_birth")) and !empty(request("filters.to_date_of_birth"))) {
            $query->where("date_of_birth", "<=", Carbon::parse(request("filters.to_date_of_birth")));
        }

        //filter text on  identity_number
        if ((request()->has("filters.identity_number")) and !empty(request("filters.identity_number"))) {
            $query->where("identity_number", "LIKE", "%" . request("filters.identity_number") . "%");
        }

        //filter by number on  wallet
        if ((request()->has("filters.wallet")) and !empty(request("filters.wallet"))) {
            $query->where("wallet", request("filters.wallet"));
        }

        //filter select on  gender
        if ((request()->has("filters.gender")) and !empty(request("filters.gender"))) {
            $query->where("gender", request("filters.gender"));
        }

        //filter by number on  rate_avg
        if ((request()->has("filters.rate_avg")) and !empty(request("filters.rate_avg"))) {
            $query->where("rate_avg", request("filters.rate_avg"));
        }

        //filter text on  referral_code
        if ((request()->has("filters.referral_code")) and !empty(request("filters.referral_code"))) {
            $query->where("referral_code", "LIKE", "%" . request("filters.referral_code") . "%");
        }

        //filter text on  verified_code
        if ((request()->has("filters.verified_code")) and !empty(request("filters.verified_code"))) {
            $query->where("verified_code", "LIKE", "%" . request("filters.verified_code") . "%");
        }

        //filter date on  last_login_at
        if ((request()->has("filters.from_last_login_at")) and !empty(request("filters.from_last_login_at"))) {
            $query->whereDate("last_login_at", ">=", Carbon::parse(request("filters.from_last_login_at")));
        }

        if ((request()->has("filters.to_last_login_at")) and !empty(request("filters.to_last_login_at"))) {
            $query->whereDate("last_login_at", "<=", Carbon::parse(request("filters.to_last_login_at")));
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
        // Filter by minimum number of orders
        if (request()->has('filters.orders_min') && !empty(request('filters.orders_min'))) {
            $query->whereHas('orders', function ($q) {
                $q->whereIn('status', ['delivered', 'finished']);
            }, '>=', request('filters.orders_min'));
        }

        // Filter by maximum number of orders
        if (request()->has('filters.orders_max') && !empty(request('filters.orders_max'))) {
            $query->whereHas('orders', function ($q) {
                $q->whereIn('status', ['delivered', 'finished']);
            }, '<=', request('filters.orders_max'));
        }

        // Filter by city_id through profile relation
        if (request()->has('filters.city_id') && !empty(request('filters.city_id'))) {
            $query->whereHas('profile', function ($q) {
                $q->where('city_id', request('filters.city_id'));
            });
        }

        // Filter by district_id through profile relation
        if (request()->has('filters.district_id') && !empty(request('filters.district_id'))) {
            $query->whereHas('profile', function ($q) {
                $q->where('district_id', request('filters.district_id'));
            });
        }

        if (request()->has("forCompany") and request("forCompany") == true) {
            $query->whereHas("roles", function ($roleQuery) {
                $roleQuery->where("name", "company");
            });
        }
        if (request()->has('trash') and request()->trash == 1) {
            $query->onlyTrashed();
        }
    }
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    public function scopeUnderMyControl($query)
    {
        if (request()->is('dashboard*') && Auth::check()) {
            if (Auth::user()->hasRole('operator')) {
                $query->where(function ($operatorQuery) {
                    $operatorQuery->whereHas('operator', function ($operatorsQuery) {
                        $operatorsQuery->where('id', Auth::id());
                    })
                        ->orWhere('id', Auth::id());
                });
            } else if (Auth::user()->hasRole(['driver', 'technical'])) {
                $query->where('id', Auth::id());
            }
        }
    }
    public function scopeDataTable($query): void
    {
        if (request()->has('start') and request()->has('length') and request()->input('length') != -1) {
            $query->skip(request()->input('start'))->take(request()->input('length'));
        }
        if (isset(request()->order) and request()->order[0]['column']) {
            $orderBy    = request()->columns[request()->order[0]['column']]['data'];
            $orderDir   = request()->order[0]['dir'];
            if (in_array($orderBy, ['city', 'district'])) {
                $query->join('profiles', 'users.id', '=', 'profiles.user_id')
                    ->orderBy("profiles.$orderBy" . "_id", $orderDir)
                    ->select('users.*');
            } elseif (isset($this->translatedAttributes) and !in_array($orderBy, $this->translatedAttributes)) {
                $query->orderByTranslation($orderBy, $orderDir);
            } else {
                $query->orderBy($orderBy, $orderDir);
            }
        }
    }
    //$request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to)
    public function scopeAnalysis($query, $from, $to, $cityId = null, $didNotOrderFrom = null, $didNotOrderTo = null, $didNotAppearFrom = null, $didNotAppearTo = null)
    {
        $query
            ->when($from, function ($q) use ($from) {
                $q->whereDate('users.created_at', '>=', $from);
            })
            ->when($to, function ($q) use ($to) {
                $q->whereDate('users.created_at', '<=', $to);
            })
            ->when($cityId, function ($q) use ($cityId) {
                $q->whereHas('profile', function ($q) use ($cityId) {
                    $q->where('city_id', $cityId);
                });
            })
            ->when($didNotOrderFrom, function ($q) use ($didNotOrderFrom) {
                $q->whereDoesntHave('orders', function ($q) use ($didNotOrderFrom) {
                    $q->whereDate('orders.created_at', '>=', $didNotOrderFrom);
                });
            })
            ->when($didNotOrderTo, function ($q) use ($didNotOrderTo) {
                $q->whereDoesntHave('orders', function ($q) use ($didNotOrderTo) {
                    $q->whereDate('orders.created_at', '<=', $didNotOrderTo);
                });
            })
            ->when($didNotAppearFrom, function ($q) use ($didNotAppearFrom) {
                $q->whereDate('users.appear_at', '>=', $didNotAppearFrom);
            })
            ->when($didNotAppearTo, function ($q) use ($didNotAppearTo) {
                $q->whereDate('users.appear_at', '<=', $didNotAppearTo);
            });
    }
    //end Scopes

    //start relations
    public function registerBy()
    {
        return $this->belongsTo(User::class, 'register_by_id', 'id');
    }
    public function myReferrals()
    {
        return $this->hasMany(User::class, 'register_by_id', 'id');
    }
    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id', 'id');
    }

    public function edit_profile()
    {
        return $this->hasMany(UserEditRequest::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class, 'client_id', 'id');
    }
    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class, 'user_id', 'id');
    }

    public function points()
    {
        return $this->hasMany(Point::class, 'user_id', 'id');
    }

    public function addresses()
    {
        return $this->hasMany(Address::class, 'user_id', 'id');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'comment_for');
    }

    public function devices()
    {
        return $this->hasMany(Device::class, 'user_id', 'id');
    }
    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id', 'id');
    }
    public function workers()
    {
        return $this->belongsTo(Worker::class, 'leader_id', 'id');
    }


    // Relationship for operators assigned to technicals
    public function operators()
    {
        return $this->belongsToMany(User::class, 'operators_technicals', 'technical_id', 'operator_id');
    }

    // Relationship for technicals assigned to operators
    public function technicals()
    {
        return $this->hasMany(User::class, 'operator_id');
    }
    public function representativeOrders()
    {
        return $this->belongsToMany(Order::class, 'order_representatives', 'representative_id', 'order_id');
    }
    public function operatorOrders()
    {
        return $this->hasMany(Order::class, 'operator_id', 'id');
    }
    public function notifications()
    {
        return $this->morphedByMany(Notification::class, 'notifications', 'users_notifications');
    }

    public function bannerNotifications()
    {
        return $this->morphedByMany(BannerNotification::class, 'notifications', 'users_notifications');
    }

    public function unreadNotifications()
    {
        return $this->notifications()->wherePivotNull('read_at'); // Filter unread notifications
    }
    public function contract()
    {
        return $this->hasOne(Contract::class, 'client_id', 'id');
    }
    //start Attributes


    // Mutator for password hashing
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }
    public function getAvatarUrlAttribute()
    {
        return isset($this->attributes['image'])
            ? MediaCenterHelper::getImagesUrl($this->attributes['image'])
            : MediaCenterHelper::getImagesUrl('storage/system/user.png');
    }

    public function getCountryNameAttribute()
    {
        return optional($this->profile?->country)?->name;
    }

    public function getCityNameAttribute()
    {
        return optional($this->profile?->city)?->name;
    }
    public function getDistrictNameAttribute()
    {
        return optional($this->profile?->district)?->name;
    }
    public function getOtherCityNameAttribute()
    {
        return optional($this->profile?->district)?->other_city_name;
    }

    public function getActionsAttribute()
    {
        return $this->getActions('users');
    }
    public function getShowActionsAttribute()
    {
        $slug = 'users';
        $actions = '<div class ="d-flex justify-content-center">';

        if (auth('web')->check() and auth('web')->user()->can('dashboard.' . $slug . '.show')) {
            $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1 " target="_blank" href="' . route('dashboard.' . $slug . '.show', ['id' => $this->id]) . '">
                <i class="fa fa-eye"></i> <span>' . trans('show') . '</span>
                </a>';
        }
        if (auth('web')->check() and auth('web')->user()->can('dashboard.' . $slug . '.edit')) {
            $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1 " target="_blank" href="' . route('dashboard.' . $slug . '.edit', ['id' => $this->id]) . '">
                <i class="fa fa-edit"></i> <span>' . trans('edit') . '</span>
                </a>';
        }


        $actions .= '</div>';
        return $actions;
    }
    public function getItemsActionsAttribute()
    {
        return $this->getItemsActions('users');
    }

    public function getItemDataAttribute()
    {
        return $this->getItemData('users');
    }
    public function getActions($slug)
    {

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
        
        if (auth('web')->check() and auth('web')->user()->can('dashboard.notifications.create')) {
            $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1 notify-btn" data-id="' . $this->id . '" href="' . route('dashboard.notifications.create', ['id' => $this->id]) . '">
                <i class="fa fa-bell"></i><span> ' . trans('notify') . ' </span>
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

    public function getItemsActions($slug)
    {

        $actions = '<div class ="d-flex justify-content-center">';
        if (auth('web')->check() and auth('web')->user()->can('dashboard.' . $slug . '.edit')) {
            $actions .= ' <button class="btn-operation edit-item mx-1" data-href="' . route('dashboard.' . $slug . '.edit', ['id' => $this->id]) . '"><i class="fas fa-pencil-alt"></i></button>';
        }
        if (auth('web')->check() and auth('web')->user()->can('dashboard.' . $slug . '.delete')) {
            $actions .= '<button class="btn-operation delete-item mx-1" data-href="' . route('dashboard.' . $slug . '.delete', ['id' => $this->id]) . '"> <i class="fas fa-trash"></i></button></td>';
        }

        $actions .= '</div>';
        return $actions;
    }
    public function getItemData($slug)
    {
        $data                    = $this->toArray();
        $data['deleteUrl']       = route('dashboard.' . $slug . '.delete', $this->id);
        $data['updateUrl']       = route('dashboard.' . $slug . '.edit', $this->id);
        return $data;
    }
    public function getSelectSwitchAttribute()
    {
        return
            '
                <div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" name="table_selected" value="' . $this->id . '" />
                </div>
            ';
    }

    //end Attributes

}
