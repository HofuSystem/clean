<?php

namespace Core\Users\Exports;

use Core\Orders\Helpers\OrderHelper;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Illuminate\Support\Facades\DB;
use Core\Users\Models\User;

class UsersExport implements FromQuery, WithHeadings, WithMapping, WithCustomCsvSettings
{
    public function __construct(
        protected $headersOnly = false,
        protected $cols        = [],
        protected int $offset  = 0,
        protected int $limit   = 1000
    ) {}

    public function query()
    {
        if ($this->headersOnly) {
            return User::whereRaw('0 = 1');
        }

        $locale = app()->getLocale();

        return User::query()
            ->select([
                'users.id',
                'users.fullname',
                'users.email',
                'users.phone',
                'users.created_at',
                // City name via translation table
                DB::raw("(
                    SELECT ct.name
                    FROM city_translations ct
                    INNER JOIN profiles p ON p.city_id = ct.city_id AND p.user_id = users.id
                    WHERE ct.locale = '{$locale}'
                    LIMIT 1
                ) as city_name"),
                // District name via translation table
                DB::raw("(
                    SELECT dt.name
                    FROM district_translations dt
                    INNER JOIN profiles p ON p.district_id = dt.district_id AND p.user_id = users.id
                    WHERE dt.locale = '{$locale}'
                    LIMIT 1
                ) as district_name"),
                // Last order date
                DB::raw("(
                    SELECT MAX(o.created_at)
                    FROM orders o
                    WHERE o.client_id = users.id
                ) as latest_order_at"),
                // Completed orders count
                DB::raw("(
                    SELECT COUNT(*)
                    FROM orders o
                    WHERE o.client_id = users.id
                    AND o.status IN ('finished','delivered')
                ) as orders_count"),
            ])
            ->whereNull('users.deleted_at')
            ->orderBy('users.id')
            ->offset($this->offset)
            ->limit($this->limit);
    }

    public function headings(): array
    {
        return [
            trans('full name'),
            trans('email'),
            trans('phone'),
            trans('orders count'),
            trans('city'),
            trans('district'),
            trans('register date'),
            trans('last order date'),
            trans('class'),
        ];
    }

    public function map($model): array
    {
        // $tier = OrderHelper::getCustomerTier($model->orders_count ?? 0);

        return [
            $model->fullname,
            $model->email,
            $model->phone,
            $model->orders_count,
            $model->city_name,
            $model->district_name,
            $model->created_at,
            $model->latest_order_at,
            // trans($tier['type']),
        ];
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter'   => ',',
            'enclosure'   => '"',
            'line_ending' => "\n",
        ];
    }
}
