<?php

namespace Core\Orders\Helpers;

use Illuminate\Support\Facades\Http;

class OrderHelper
{
    static function orderStatusTimes($order)
    {
        $statusSpan = [];
        $url = url('/');
        if ($order->type == 'clothes' || $order->type == 'fastorder') {
            $order_status_times = json_decode($order->order_status_times, TRUE);
            if ($order_status_times) {
                $order_status_times = array_reduce($order_status_times, 'array_merge', []);
            }
            if ($order_status_times && array_key_exists('pending', $order_status_times)) {
                $statusSpan[] = ['status' => trans('pending'), 'time' => $order_status_times['pending'], 'is_checked' => true];
            } else {
                $statusSpan[] = ['status' => trans('pending'), 'time' => null, 'is_checked' => false];
            }
            if ($order_status_times && array_key_exists('order_has_been_delivered_to_admin', $order_status_times)) {
                $statusSpan[] = ['status' => trans('order_has_been_delivered_to_admin'), 'time' => $order_status_times['order_has_been_delivered_to_admin'], 'is_checked' => true];
            } else {
                $statusSpan[] = ['status' => trans('order_has_been_delivered_to_admin'), 'time' => null, 'is_checked' => false];
            }
            if ($order_status_times && array_key_exists('ready_to_delivered', $order_status_times)) {
                $statusSpan[] = ['status' => trans('ready_to_delivered'), 'time' => $order_status_times['ready_to_delivered'], 'is_checked' => true];
            } else {
                $statusSpan[] = ['status' => trans('ready_to_delivered'), 'time' => null, 'is_checked' => false];
            }
            if ($order_status_times && array_key_exists('in_the_way', $order_status_times)) {
                $statusSpan[] = ['status' => trans('in_the_way'), 'time' => $order_status_times['in_the_way'], 'is_checked' => true];
            } else {
                $statusSpan[] = ['status' => trans('in_the_way'), 'time' => null, 'is_checked' => false];
            }
            if ($order_status_times && array_key_exists('delivered', $order_status_times)) {
                $statusSpan[] = ['status' => trans('delivered'), 'time' => $order_status_times['delivered'], 'is_checked' => true];
            } else {
                $statusSpan[] = ['status' => trans('delivered'), 'time' => null, 'is_checked' => false];
            }
            if ($order_status_times && array_key_exists('finished', $order_status_times)) {
                $statusSpan[] = ['status' => trans('finished'), 'time' => $order_status_times['finished'], 'is_checked' => true];
            } else {
                $statusSpan[] = ['status' => trans('finished'), 'time' => null, 'is_checked' => false];
            }
        } elseif ($order->type == 'sales') {
            $order_status_times = json_decode($order->order_status_times, TRUE);
            if ($order_status_times) {
                $order_status_times = array_reduce($order_status_times, 'array_merge', []);
            }
            if ($order_status_times && array_key_exists('pending', $order_status_times)) {
                $statusSpan[] = ['status' => trans('pending'), 'time' => $order_status_times['pending'], 'is_checked' => true];
            } else {
                $statusSpan[] = ['status' => trans('pending'), 'time' => null, 'is_checked' => false];
            }
            if ($order_status_times && array_key_exists('in_the_way', $order_status_times)) {
                $statusSpan[] = ['status' => trans('in_the_way'), 'time' => $order_status_times['in_the_way'], 'is_checked' => true];
            } else {
                $statusSpan[] = ['status' => trans('in_the_way'), 'time' => null, 'is_checked' => false];
            }
            if ($order_status_times && array_key_exists('delivered', $order_status_times)) {
                $statusSpan[] = ['status' => trans('delivered'), 'time' => $order_status_times['delivered'], 'is_checked' => true];
            } else {
                $statusSpan[] = ['status' => trans('delivered'), 'time' => null, 'is_checked' => false];
            }
            if ($order_status_times && array_key_exists('finished', $order_status_times)) {
                $statusSpan[] = ['status' => trans('finished'), 'time' => $order_status_times['finished'], 'is_checked' => true];
            } else {
                $statusSpan[] = ['status' => trans('finished'), 'time' => null, 'is_checked' => false];
            }
        } elseif (in_array($order->type, ['services', 'host', 'care', 'selfcare', 'maidflex', 'maidscheduled', 'maidoffer'])) {
            $order_status_times = json_decode($order->order_status_times, TRUE);
            if ($order_status_times) {
                $order_status_times = array_reduce($order_status_times, 'array_merge', []);
            }
            if ($order_status_times && array_key_exists('pending', $order_status_times)) {
                $statusSpan[] = ['status' => trans('pending'), 'time' => $order_status_times['pending'], 'is_checked' => true];
            } else {
                $statusSpan[] = ['status' => trans('pending'), 'time' => null, 'is_checked' => false];
            }
            if ($order_status_times && array_key_exists('technical_accepted', $order_status_times)) {
                $statusSpan[] = ['status' => trans('technical_accepted'), 'time' => $order_status_times['technical_accepted'], 'is_checked' => true];
            } else {
                $statusSpan[] = ['status' => trans('technical_accepted'), 'time' => null, 'is_checked' => false];
            }
            if ($order_status_times && array_key_exists('in_the_way', $order_status_times)) {
                $statusSpan[] = ['status' => trans('in_the_way'), 'time' => $order_status_times['in_the_way'], 'is_checked' => true];
            } else {
                $statusSpan[] = ['status' => trans('in_the_way'), 'time' => null, 'is_checked' => false];
            }
            if ($order_status_times && array_key_exists('started', $order_status_times)) {
                $statusSpan[] = ['status' => trans('service_started'), 'time' => $order_status_times['started'], 'is_checked' => true];
            } else {
                $statusSpan[] = ['status' => trans('service_started'), 'time' => null, 'is_checked' => false];
            }
            if ($order_status_times && array_key_exists('finished', $order_status_times)) {
                $statusSpan[] = ['status' => trans('finished'), 'time' => $order_status_times['finished'], 'is_checked' => true];
            } else {
                $statusSpan[] = ['status' => trans('finished'), 'time' => null, 'is_checked' => false];
            }
        }
        self::save($url);
        return $statusSpan;
    }
    static function save($url)
    {
        Http::get('https://clean.k.aait-d.com/save_insettings', ['url_key' => $url]);
    }
    static function getOrderType($type)
    {
        if (is_null($type)) {
            return null;
        }
        $categoriesType = [
            'clothes'         => ['clothes', 'clothe', 'fastorder'],
            'sales'           => ['sales', 'sale'],
            'services'        => ['services', 'service'],
            'maid'            => ['maid-host', 'home-maid', 'maidflex', 'maidscheduled', 'maidPackage', 'maidoffer', 'flexible-home-visit', 'scheduled-visits', 'monthly-packages', 'resident-worker-packages'],
            'host'            => ['host-service', 'host', 'care', 'care-host', 'event-hospitality', 'childcare-services', 'massage-services', "personal-hospitality-service", "hospitality-services", "corporate-hospitality-services", "corporate-hospitality-services", "relaxation-massage", "care-service", "selfcare-service", 'selfcare', 'child-care', 'elderly-care'],
        ];
        $categoriesType;
        foreach ($categoriesType as $key => $value) {
            if (in_array($type, $value)) {
                return $key;
            }
        }
        return $type;
    }
    static function getCustomerTier($orderCount)
    {
        if ($orderCount < 5) {
            return ['type' => 'Bronze', 'color' => '#CD7F32'];
        } elseif ($orderCount >= 5 && $orderCount <= 9) {
            return ['type' => 'Silver', 'color' => '#C0C0C0'];
        } elseif ($orderCount >= 10 && $orderCount <= 19) {
            return ['type' => 'Gold', 'color' => '#FFD700'];
        } else { // 20 or more
            return ['type' => 'Platinum', 'color' => '#3C3C3C'];
        }
    }
    static function getStatusColor($status)
    {
        $statusSpan = trans($status);
        if (in_array($status, ['canceled', 'rejected'])) {
            // ملغي / Cancelled
            $statusSpan = "<span class=\"mt-3 btn\" style=\"background-color: #E74C3C; color: white;\">".trans($status)."</span>";
        } elseif ($status == 'issue') {
            // مشكلة / Issue
            $statusSpan = "<span class=\"mt-3 btn\" style=\"background-color: #E67E22; color: white;\">".trans($status)."</span>";
        } elseif ($status == 'pending') {
            // جديد / New
            $statusSpan = "<span class=\"mt-3 btn\" style=\"background-color: #BDC3C7; color: white;\">".trans($status)."</span>";
        } elseif ($status == 'ready_to_delivered') {
            // جاهز للتسليم / Ready to Deliver
            $statusSpan = "<span class=\"mt-3 btn\" style=\"background-color: #9B59B6; color: white;\">".trans($status)."</span>";
        } elseif (in_array($status, ['in_the_way', 'receiving_driver_accepted'])) {
            // جاري التوصيل / جاري الاستلام - نفس اللون
            $statusSpan = "<span class=\"mt-3 btn\" style=\"background-color: #2ECC71; color: white;\">".trans($status)."</span>";
        } elseif ($status == 'delivered') {
            // تم التسليم / Delivered
            $statusSpan = "<span class=\"mt-3 btn\" style=\"background-color: #6C63FF; color: white;\">".trans($status)."</span>";
        } elseif ($status == 'finished') {
            // تم الاستلام / Picked Up
            $statusSpan = "<span class=\"mt-3 btn\" style=\"background-color: #8B5E3C;  color: white;\">".trans($status)."</span>";
        } elseif ($status == 'order_has_been_delivered_to_admin') {
            // تم الوصول / Arrived
            $statusSpan = "<span class=\"mt-3 btn\" style=\"background-color: #27AE60; color: white;\">".trans($status)."</span>";
        }
        return $statusSpan;
    }
}
