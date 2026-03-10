<?php

namespace Core\Users\Exports;

use Core\Orders\Helpers\OrderHelper;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Users\Models\User;


class UsersExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
{

    public function __construct(protected $headersOnly = false,protected $cols = [])
    {
    }

    public function collection()
    {
        if ($this->headersOnly) {
            // Return an empty collection if only headers are needed
            return collect([]);
        }
        // Fetch coupon data from the database
        return User::select([
            'users.id',
            'users.image',
            'users.fullname',
            'users.email',
            'users.phone',
            'users.is_active',
            'users.is_allow_notify',
            'users.date_of_birth',
            'users.gender',
            'users.created_at',
            \DB::raw('(SELECT MAX(created_at) FROM orders WHERE orders.client_id = users.id) as latest_order_at')
        ])
        ->with(['roles','profile'])
        ->withCount(['orders as orders_count' => function ($query) {
            $query->whereIn('status', ['finished', 'delivered']);
        }])->get();
    }

    public function headings(): array
    {
        // Define headers based on the provided attributes
        $headings = [];
        if(empty($this->cols) or in_array('id',$this->cols)){
            $headings[] = trans('id');
        }
        if(empty($this->cols) or in_array('image',$this->cols)){
            $headings[] = trans('avatar');
        }
        if(empty($this->cols) or in_array('fullname',$this->cols)){
            $headings[] = trans('full name');
        }
        if(empty($this->cols) or in_array('email',$this->cols)){
            $headings[] = trans('email');
        }
        if(empty($this->cols) or in_array('email_verified_at',$this->cols)){
            $headings[] = trans('email verified at');
        }
        if(empty($this->cols) or in_array('phone',$this->cols)){
            $headings[] = trans('phone');
        }
     
        if(empty($this->cols) or in_array('class',$this->cols)){
            $headings[] = trans('class');
        }
        if(empty($this->cols) or in_array('latest_order_at',$this->cols)){
            $headings[] = trans('latest_order_at');
        }
        if(empty($this->cols) or in_array('class',$this->cols)){
            $headings[] = trans('class');
        }
        if(empty($this->cols) or in_array('phone_verified_at',$this->cols)){
            $headings[] = trans('phone verified at');
        }
        if(empty($this->cols) or in_array('roles',$this->cols)){
            $headings[] = trans('roles');
        }
        if(empty($this->cols) or in_array('city',$this->cols)){
            $headings[] = trans('city');
        }
        if(empty($this->cols) or in_array('district',$this->cols)){
            $headings[] = trans('district');
        }
        if(empty($this->cols) or in_array('is_active',$this->cols)){
            $headings[] = trans('is active');
        }
        if(empty($this->cols) or in_array('is_allow_notify',$this->cols)){
            $headings[] = trans('is allow notify');
        }
        if(empty($this->cols) or in_array('date_of_birth',$this->cols)){
            $headings[] = trans('date of birth');
        }
        if(empty($this->cols) or in_array('identity_number',$this->cols)){
            $headings[] = trans('identity number');
        }
        if(empty($this->cols) or in_array('wallet',$this->cols)){
            $headings[] = trans('wallet');
        }
        if(empty($this->cols) or in_array('points_balance',$this->cols)){
            $headings[] = trans('points balance');
        }
        if(empty($this->cols) or in_array('gender',$this->cols)){
            $headings[] = trans('gender');
        }
        if(empty($this->cols) or in_array('rate_avg',$this->cols)){
            $headings[] = trans('rate avg');
        }
        if(empty($this->cols) or in_array('referral_code',$this->cols)){
            $headings[] = trans('referral code');
        }
        if(empty($this->cols) or in_array('verified_code',$this->cols)){
            $headings[] = trans('verified code');
        }
        if(empty($this->cols) or in_array('last_login_at',$this->cols)){
            $headings[] = trans('last login at');
        }             
        return $headings;
    }

    public function map($model): array
    {
        // Format the data before exporting
        $data = [];
        
        if(empty($this->cols) or in_array('id',$this->cols)){
            $data[] = $model->id;
        }
        if(empty($this->cols) or in_array('image',$this->cols)){
            $data[] = $model->image;
        }
        if(empty($this->cols) or in_array('fullname',$this->cols)){
            $data[] = $model->fullname;
        }
        if(empty($this->cols) or in_array('email',$this->cols)){
            $data[] = $model->email;
        }
        if(empty($this->cols) or in_array('email_verified_at',$this->cols)){
            $data[] = $model->email_verified_at;
        }
        if(empty($this->cols) or in_array('phone',$this->cols)){
            $data[] = $model->phone;
        }

        if(empty($this->cols) or in_array('orders_count',$this->cols)){
            $data[] = $model->orders_count;
        }     
     
        if(empty($this->cols) or in_array('latest_order_at',$this->cols)){
            $data[] = $model->latest_order_at;
        }       
        if(empty($this->cols) or in_array('class',$this->cols)){
            $customerTire         = OrderHelper::getCustomerTier($model->orders_count);
            $data[] = trans($customerTire['type']);
        }    
        if(empty($this->cols) or in_array('phone_verified_at',$this->cols)){
            $data[] = $model->phone_verified_at;
        }
        if(empty($this->cols) or in_array('roles',$this->cols)){
            $data[] = $model->roles->pluck('name')->toArray();
        }
        if(empty($this->cols) or in_array('city',$this->cols)){
            $data[] = $model?->profile?->city?->name;
        }
        if(empty($this->cols) or in_array('district',$this->cols)){
            $data[] = $model?->profile?->district?->name;
        }
        if(empty($this->cols) or in_array('is_active',$this->cols)){
            $data[] = $model->is_active;
        }
        if(empty($this->cols) or in_array('is_allow_notify',$this->cols)){
            $data[] = $model->is_allow_notify;
        }
        if(empty($this->cols) or in_array('date_of_birth',$this->cols)){
            $data[] = $model->date_of_birth;
        }
        if(empty($this->cols) or in_array('identity_number',$this->cols)){
            $data[] = $model->identity_number;
        }
        if(empty($this->cols) or in_array('wallet',$this->cols)){
            $data[] = $model->wallet;
        }
        if(empty($this->cols) or in_array('points_balance',$this->cols)){
            $data[] = $model->points_balance;
        }
        if(empty($this->cols) or in_array('gender',$this->cols)){
            $data[] = $model->gender;
        }
        if(empty($this->cols) or in_array('rate_avg',$this->cols)){
            $data[] = $model->rate_avg;
        }
        if(empty($this->cols) or in_array('referral_code',$this->cols)){
            $data[] = $model->referral_code;
        }
        if(empty($this->cols) or in_array('verified_code',$this->cols)){
            $data[] = $model->verified_code;
        }
        if(empty($this->cols) or in_array('last_login_at',$this->cols)){
            $data[] = $model->last_login_at;
        }
        return $data;
    }
    public function getCsvSettings(): array
    {
        // Optional: Customize CSV settings (e.g., delimiter)
        return [
            'delimiter' => ',',
            'enclosure' => '"',
            'line_ending' => "\n",
        ];
    }
}
