<?php

namespace Core\Users\Exports;

use Core\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Memory-efficient Users CSV export using streaming (fputcsv).
 *
 * Instead of building a full spreadsheet in memory (PhpSpreadsheet),
 * this class writes each row directly to disk via fputcsv + chunkById,
 * keeping memory usage constant (~5-10 MB) regardless of dataset size.
 */
class UsersExport
{
    protected string $locale;
    protected int $chunkSize;

    public function __construct(?string $locale = null, int $chunkSize = 500)
    {
        $this->locale    = $locale ?: app()->getLocale();
        $this->chunkSize = $chunkSize;
    }

    /**
     * Export users to a CSV file on the given Storage disk.
     *
     * @param  string  $filename  Relative path on the disk (e.g. 'exports/users.csv')
     * @param  string  $disk      Storage disk name (default: 'public')
     * @return string  The full filesystem path of the generated file.
     */
    public function store(string $filename, string $disk = 'public'): string
    {
        // Disable query log to prevent memory leak from logged queries
        DB::connection()->disableQueryLog();

        // Resolve absolute path so we can open a raw file handle
        $fullPath = Storage::disk($disk)->path($filename);

        // Ensure the directory exists
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Use a temporary file during generation so users don't download partial files
        $tmpPath = $fullPath . '.tmp';
        
        // Open file handle for writing
        $handle = fopen($tmpPath, 'w');

        if ($handle === false) {
            throw new \RuntimeException("Cannot open file for writing: {$tmpPath}");
        }

        // Write UTF-8 BOM so Excel opens the CSV with correct encoding
        fwrite($handle, "\xEF\xBB\xBF");

        // Write header row
        fputcsv($handle, $this->headings());

        // Stream data in chunks (1500) for fast processing with constant low memory usage
        $this->buildQuery()
            ->chunkById($this->chunkSize, function ($users) use ($handle) {
                // Map and write each row
                foreach ($users as $user) {
                    fputcsv($handle, $this->mapRow($user));
                }

                // Free memory
                unset($users);

                // Flush PHP's output buffer to OS
                fflush($handle);

                // Reclaim cycles
                gc_collect_cycles();
            }, 'users.id', 'id');

        fclose($handle);

        // Rename the temporary file to the final filename when complete
        rename($tmpPath, $fullPath);

        return $fullPath;
    }

    /**
     * Build the base query with only the required columns.
     * Uses raw JOINs to avoid Eloquent eager-loading overhead.
     */
    protected function buildQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return User::query()
            ->hasName()
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
                DB::raw('(SELECT MAX(o.created_at) FROM orders o WHERE o.client_id = users.id) as latest_order_at'),
                DB::raw('(SELECT COUNT(*) FROM orders o WHERE o.client_id = users.id AND o.status IN ("finished","delivered")) as orders_count')
            ])
            ->whereNull('users.deleted_at')
            ->orderBy('users.id');
    }

    /**
     * CSV column headings.
     */
    protected function headings(): array
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
            trans('class'),
        ];
    }

    /**
     * Map a single Eloquent model to a flat row array.
     */
    protected function mapRow($user): array
    {
        return [
            $user->id,
            $user->fullname,
            $user->email,
            $user->phone,
            $user->orders_count ?? 0,
            $user->city_name ?? '',
            $user->district_name ?? '',
            $user->created_at,
            $user->latest_order_at ?? '',
            \Core\Orders\Helpers\OrderHelper::getCustomerTier($user->orders_count ?? 0)['type'] ?? '',
        ];
    }
}
