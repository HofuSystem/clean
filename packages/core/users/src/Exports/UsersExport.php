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
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Events\BeforeSheet;
use Laravel\Telescope\Telescope;

class UsersExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading, WithEvents, ShouldQueue
{
    use Exportable;

    public function __construct(
        protected $locale = null
    ) {
        $this->locale = $locale ?: app()->getLocale();
        
        // Ensure memory limit and Telescope are handled whenever this class is instantiated (including in jobs)
        ini_set('memory_limit', '512M');
        if (class_exists(Telescope::class)) {
            Telescope::stopRecording();
        }
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
            // Aggregate orders in a single join to avoid row-by-row subqueries
            ->leftJoin(DB::raw("(
                SELECT client_id, MAX(created_at) as latest_order_at, COUNT(*) as total_orders_count
                FROM orders
                WHERE status IN ('finished','delivered')
                GROUP BY client_id
            ) as order_stats"), 'order_stats.client_id', '=', 'users.id')
            ->select([
                'users.id',
                'users.fullname',
                'users.email',
                'users.phone',
                'users.created_at',
                'city_translations.name as city_name',
                'district_translations.name as district_name',
                'order_stats.latest_order_at',
                'order_stats.total_orders_count as orders_count',
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

    private $rowCounter = 0;
    public function map($model): array
    {
        $this->rowCounter++;
        if ($this->rowCounter % 100 === 0) {
            gc_collect_cycles();
        }

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
        return 500;
    }

    public function registerEvents(): array
    {
        return [
            BeforeExport::class => function (BeforeExport $event) {
                ini_set('memory_limit', '512M');
                
                // Disable DB query log to save memory
                DB::connection()->disableQueryLog();
                
                if (class_exists(Telescope::class)) {
                    Telescope::stopRecording();
                }
            },
            BeforeWriting::class => function (BeforeWriting $event) {
                ini_set('memory_limit', '512M');
                gc_collect_cycles();
            },
            BeforeSheet::class => function (BeforeSheet $event) {
                ini_set('memory_limit', '512M');
                gc_collect_cycles();
            },
        ];
    }
}
