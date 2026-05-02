<?php

namespace Core\Users\Exports;

use Core\Users\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading, ShouldQueue
{
    use Exportable;

    public function __construct(
        protected $locale = null
    ) {
        $this->locale = $locale ?: app()->getLocale();
    }

    public function query()
    {
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
                    WHERE ct.locale = '{$this->locale}'
                    LIMIT 1
                ) as city_name"),
                // District name via translation table
                DB::raw("(
                    SELECT dt.name
                    FROM district_translations dt
                    INNER JOIN profiles p ON p.district_id = dt.district_id AND p.user_id = users.id
                    WHERE dt.locale = '{$this->locale}'
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
            ->orderBy('users.id');
    }

    public function headings(): array
    {
        return [
            trans('id'),
            trans('full name'),
            trans('email'),
            trans('phone'),
            trans('orders count'),
            trans('city'),
            trans('district'),
            trans('register date'),
            trans('last order date'),
        ];
    }

    public function map($model): array
    {
        return [
            $model->id,
            $model->fullname,
            $model->email,
            $model->phone,
            $model->orders_count,
            $model->city_name,
            $model->district_name,
            $model->created_at,
            $model->latest_order_at,
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
