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

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Laravel\Telescope\Telescope;

class UsersExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading, WithEvents, ShouldQueue
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
            ->leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
            ->leftJoin('city_translations', function ($join) {
                $join->on('city_translations.city_id', '=', 'profiles.city_id')
                    ->where('city_translations.locale', '=', $this->locale);
            })
            ->leftJoin('district_translations', function ($join) {
                $join->on('district_translations.district_id', '=', 'profiles.district_id')
                    ->where('district_translations.locale', '=', $this->locale);
            })
            ->select([
                'users.id',
                'users.fullname',
                'users.email',
                'users.phone',
                'users.created_at',
                'city_translations.name as city_name',
                'district_translations.name as district_name',
                // Last order date - keeping subquery for these aggregates as they are harder to join without grouping issues
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

    public function registerEvents(): array
    {
        return [
            BeforeExport::class => function (BeforeExport $event) {
                // Increase memory limit for large exports
                ini_set('memory_limit', '512M');
                
                if (class_exists(Telescope::class)) {
                    Telescope::stopRecording();
                }
            },
        ];
    }
}
