<?php

namespace Core\Admin\Services;

use Core\Orders\Models\Order;
use Core\Orders\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Core\Settings\Services\SettingsService;

class OrderReportService
{
    /**
     * Get order quantities report data
     */
    public function getOrderQuantitiesReport($filters)
    {
        $query = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('product_translations', function($join) {
                $join->on('products.id', '=', 'product_translations.product_id')
                    ->where('product_translations.locale', '=', app()->getLocale());
            })
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('category_translations as cat_trans', function($join) {
                $join->on('categories.id', '=', 'cat_trans.category_id')
                    ->where('cat_trans.locale', '=', app()->getLocale());
            })
            ->leftJoin('categories as sub_categories', 'products.sub_category_id', '=', 'sub_categories.id')
            ->leftJoin('category_translations as sub_cat_trans', function($join) {
                $join->on('sub_categories.id', '=', 'sub_cat_trans.category_id')
                    ->where('sub_cat_trans.locale', '=', app()->getLocale());
            });

        // Apply filters
        if (!empty($filters['from_date'])) {
            $query->whereDate('orders.created_at', '>=', Carbon::parse($filters['from_date']));
        }
        if (!empty($filters['to_date'])) {
            $query->whereDate('orders.created_at', '<=', Carbon::parse($filters['to_date']));
        }

        if (!empty($filters['type'])) {
            if ($filters['type'] === 'company') {
                $query->whereNotNull('orders.company_id');
            } elseif ($filters['type'] === 'client') {
                $query->whereNull('orders.company_id');
            }
        }

        if (!empty($filters['company_id'])) {
            $query->where('orders.company_id', $filters['company_id']);
        }

        if (!empty($filters['client_id'])) {
            $query->where('orders.client_id', $filters['client_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('orders.status', $filters['status']);
        }
        
        if (!empty($filters['wash_type'])) {
            $query->where('order_items.wash_type', $filters['wash_type']);
        }

        // Test accounts - usually we exclude them in reports
        $testAccounts = SettingsService::getDataBaseSetting('testing_accounts') ?? [];
        if (!empty($testAccounts)) {
            $query->whereNotIn('orders.client_id', $testAccounts);
        }

        $query->select(
            'order_items.product_id',
            'product_translations.name as product_name',
            'cat_trans.name as category_name',
            'sub_cat_trans.name as subcategory_name',
            'order_items.wash_type',
            DB::raw('SUM(order_items.quantity) as total_quantity')
        )
            ->groupBy('order_items.product_id', 'product_translations.name', 'cat_trans.name', 'sub_cat_trans.name', 'order_items.wash_type')
            ->orderBy('total_quantity', 'desc');

        return $query->get();
    }

    /**
     * Get summarized costs from orders table
     */
    public function getOrderCostsSummary($filters)
    {
        $query = DB::table('orders');

        // Apply filters
        if (!empty($filters['from_date'])) {
            $query->whereDate('orders.created_at', '>=', Carbon::parse($filters['from_date']));
        }
        if (!empty($filters['to_date'])) {
            $query->whereDate('orders.created_at', '<=', Carbon::parse($filters['to_date']));
        }

        if (!empty($filters['type'])) {
            if ($filters['type'] === 'company') {
                $query->whereNotNull('orders.company_id');
            } elseif ($filters['type'] === 'client') {
                $query->whereNull('orders.company_id');
            }
        }

        if (!empty($filters['company_id'])) {
            $query->where('orders.company_id', $filters['company_id']);
        }

        if (!empty($filters['client_id'])) {
            $query->where('orders.client_id', $filters['client_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('orders.status', $filters['status']);
        }

        // Test accounts
        $testAccounts = SettingsService::getDataBaseSetting('testing_accounts') ?? [];
        if (!empty($testAccounts)) {
            $query->whereNotIn('orders.client_id', $testAccounts);
        }

        return $query->select(
            DB::raw('SUM(lab_cost) as total_lab_cost'),
            DB::raw('SUM(washer_cost) as total_washer_cost')
        )->first();
    }
}
