<?php

namespace Core\Notification\Helpers;

use Core\Notification\Models\Notification;

class NotificationChannelResolver
{
    public const CHANNEL_APP_FCM = 'app_fcm';
    public const CHANNEL_WHATSAPP = 'whatsapp';
    public const CHANNEL_SMS = 'sms';
    public const CHANNEL_EMAIL = 'email';
    public const CHANNEL_IN_APP = 'in_app';
    public const CHANNEL_LEGACY_UNKNOWN = 'legacy_unknown';

    public const PURPOSE_AUTHENTICATION = 'authentication';
    public const PURPOSE_TRANSACTIONAL = 'transactional';
    public const PURPOSE_MARKETING = 'marketing';
    public const PURPOSE_SYSTEM = 'system';
    public const PURPOSE_LEGACY_UNKNOWN = 'legacy_unknown';

    /**
     * Resolve all communication channels for a given notification.
     *
     * @param mixed $notification
     * @return array<string>
     */
    public static function resolveChannels($notification): array
    {
        $rawTypes = [];

        if ($notification instanceof Notification) {
            $rawTypes = NotificationDataNormalizer::toStringList($notification->types);
            $chanVal = $notification->getRawOriginal('channel') ?? ($notification->getAttributes()['channel'] ?? null);
            if (empty($rawTypes) && !empty($chanVal)) {
                $rawTypes = [$chanVal];
            }
        } elseif (is_array($notification)) {
            $rawTypes = NotificationDataNormalizer::toStringList($notification['types'] ?? $notification['channel'] ?? []);
        } elseif (is_string($notification)) {
            $rawTypes = NotificationDataNormalizer::toStringList($notification);
        }

        if (empty($rawTypes)) {
            return [self::CHANNEL_LEGACY_UNKNOWN];
        }

        $resolved = [];
        foreach ($rawTypes as $type) {
            $t = strtolower(trim((string)$type));
            if (in_array($t, ['apps', 'app push', 'fcm', 'app_fcm', 'push'], true)) {
                $resolved[] = self::CHANNEL_APP_FCM;
            } elseif (in_array($t, ['whats_app', 'whatsapp'], true)) {
                $resolved[] = self::CHANNEL_WHATSAPP;
            } elseif ($t === 'sms') {
                $resolved[] = self::CHANNEL_SMS;
            } elseif (in_array($t, ['email', 'mail'], true)) {
                $resolved[] = self::CHANNEL_EMAIL;
            } elseif (in_array($t, ['in_app', 'inapp'], true)) {
                $resolved[] = self::CHANNEL_IN_APP;
            }
        }

        $resolved = array_values(array_unique($resolved));

        return !empty($resolved) ? $resolved : [self::CHANNEL_LEGACY_UNKNOWN];
    }

    /**
     * Resolve the primary channel as a single identifier.
     *
     * @param mixed $notification
     * @return string
     */
    public static function resolveChannel($notification): string
    {
        if ($notification instanceof Notification) {
            $val = $notification->getRawOriginal('channel') ?? ($notification->getAttributes()['channel'] ?? null);
            if (!empty($val)) {
                $val = strtolower(trim((string)$val));
                if (in_array($val, [self::CHANNEL_APP_FCM, self::CHANNEL_WHATSAPP, self::CHANNEL_SMS, self::CHANNEL_EMAIL, self::CHANNEL_IN_APP], true)) {
                    return $val;
                }
            }
        }

        $channels = self::resolveChannels($notification);

        if (in_array(self::CHANNEL_APP_FCM, $channels, true)) {
            return self::CHANNEL_APP_FCM;
        }

        return $channels[0] ?? self::CHANNEL_LEGACY_UNKNOWN;
    }

    /**
     * Resolve the purpose of the notification.
     * Never defaults blindly to 'marketing'.
     * Uses explicit value if valid, or safely infers from title, body, order_id, or channel.
     *
     * @param mixed $notification
     * @return string
     */
    public static function resolvePurpose($notification): string
    {
        $explicitPurpose = null;
        $title = '';
        $body = '';
        $orderId = null;

        if ($notification instanceof Notification) {
            $explicitPurpose = $notification->getRawOriginal('purpose') ?? ($notification->getAttributes()['purpose'] ?? null) ?? $notification->purpose;
            $title = (string)($notification->title ?? '');
            $body = (string)($notification->body ?? '');
            $orderId = $notification->order_id ?? null;
        } elseif (is_array($notification)) {
            $explicitPurpose = $notification['purpose'] ?? null;
            $title = (string)($notification['title'] ?? '');
            $body = (string)($notification['body'] ?? '');
            $orderId = $notification['order_id'] ?? null;
        }

        // 1. If explicitly set to a recognized purpose, preserve it strictly
        if (!empty($explicitPurpose)) {
            $p = strtolower(trim((string)$explicitPurpose));
            if (in_array($p, [self::PURPOSE_AUTHENTICATION, self::PURPOSE_TRANSACTIONAL, self::PURPOSE_MARKETING, self::PURPOSE_SYSTEM], true)) {
                return $p;
            }
        }

        // 2. Safe derivation from order_id (unambiguous transactional)
        if (!empty($orderId)) {
            return self::PURPOSE_TRANSACTIONAL;
        }

        // 3. Safe derivation from title and body text
        $text = mb_strtolower(trim($title . ' ' . $body));

        if ($text !== '') {
            // A. Authentication keywords
            $authKeywords = [
                'verify message', 'verified_code', 'رمز التحقق', 'كود التحقق', 'رمز التأكيد',
                'otp', 'verification', 'verify', 'activation', 'تفعيل الحساب', 'تفعيل',
                'تسجيل الدخول', 'login code', 'password reset', 'إعادة تعيين كلمة المرور',
                'كود الدخول'
            ];
            foreach ($authKeywords as $kw) {
                if (mb_strpos($text, $kw) !== false) {
                    return self::PURPOSE_AUTHENTICATION;
                }
            }

            // B. Transactional keywords (orders, drivers, technicals, delivery, payments)
            $transKeywords = [
                'طلب جديد', 'طلبك', 'المندوب', 'مندوب', 'سائق', 'driver', 'فني', 'technical',
                'توصيل', 'delivery', 'تم الاستلام', 'طلبك جاهز', 'وصل لموقعك', 'تم توصيل',
                'ادفع من التطبيق', 'المبلغ المتبقي', 'فاتورة', 'invoice', 'محفظة', 'wallet',
                'نقاط الولاء', 'loyalty points', 'order collected', 'ready for delivery',
                'driver on the way', 'driver has arrived', 'order delivered', 'قيد التنفيذ',
                'تم الإلغاء', 'الغاء الطلب', 'order'
            ];
            foreach ($transKeywords as $kw) {
                if (mb_strpos($text, $kw) !== false) {
                    return self::PURPOSE_TRANSACTIONAL;
                }
            }

            // C. Marketing keywords (discounts, coupons, promotions, abandoned cart)
            $mktKeywords = [
                'عرض', 'عروض', 'خصم', 'خصومات', 'كوبون', 'كوبونات', 'offer', 'discount',
                'coupon', 'promo', 'تخفيض', 'مجاني', 'هدية', 'اشتقنا لخدمتك', 'نسيت غسيلك',
                'خزانتك تستحق', 'الجمعة للراحة', 'أناقتك', 'انتعاش يليق', 'نظافة تفرق',
                'اليوم التوصيل علينا', 'عيد ميلاد', 'سلة متروكة', 'abandoned cart'
            ];
            foreach ($mktKeywords as $kw) {
                if (mb_strpos($text, $kw) !== false) {
                    return self::PURPOSE_MARKETING;
                }
            }

            // D. System / Administrative keywords
            $sysKeywords = [
                'تحديث النظام', 'صيانة', 'شروط الاستخدام', 'سياسة الخصوصية', 'إشعار إداري',
                'system maintenance', 'terms of service', 'privacy policy', 'admin alert',
                'تنبيه أمني', 'security alert'
            ];
            foreach ($sysKeywords as $kw) {
                if (mb_strpos($text, $kw) !== false) {
                    return self::PURPOSE_SYSTEM;
                }
            }
        }

        // 4. Fallback: Legacy / Unknown (Never assume marketing)
        return self::PURPOSE_LEGACY_UNKNOWN;
    }

    /**
     * Resolve the transport mechanism for display.
     *
     * @param string $channel
     * @param string|null $fcmDeliveryChannel
     * @return string
     */
    public static function resolveTransportType(string $channel, ?string $fcmDeliveryChannel = null): string
    {
        switch ($channel) {
            case self::CHANNEL_APP_FCM:
                if ($fcmDeliveryChannel === 'direct_fcm') {
                    return 'Direct FCM (Push)';
                }
                return 'Legacy Topic Subscription';

            case self::CHANNEL_WHATSAPP:
                return 'WhatsApp Gateway API';

            case self::CHANNEL_SMS:
                return 'SMS Gateway API';

            case self::CHANNEL_EMAIL:
                return 'SMTP / Mail Server';

            case self::CHANNEL_IN_APP:
                return 'In-App Inbox';

            default:
                return 'Legacy / Unknown';
        }
    }

    /**
     * Human-readable label for the channel.
     *
     * @param string $channel
     * @return string
     */
    public static function getChannelLabel(string $channel): string
    {
        switch ($channel) {
            case self::CHANNEL_APP_FCM:
                return 'App FCM';
            case self::CHANNEL_WHATSAPP:
                return 'WhatsApp';
            case self::CHANNEL_SMS:
                return 'SMS';
            case self::CHANNEL_EMAIL:
                return 'Email';
            case self::CHANNEL_IN_APP:
                return 'In-App';
            default:
                return 'Legacy / Unknown';
        }
    }

    /**
     * Human-readable label for the purpose.
     *
     * @param string $purpose
     * @return string
     */
    public static function getPurposeLabel(string $purpose): string
    {
        switch ($purpose) {
            case self::PURPOSE_AUTHENTICATION:
                return trans('توثيق (Authentication)');
            case self::PURPOSE_TRANSACTIONAL:
                return trans('تشغيلي (Transactional)');
            case self::PURPOSE_MARKETING:
                return trans('تسويقي (Marketing)');
            case self::PURPOSE_SYSTEM:
                return trans('نظام (System)');
            default:
                return trans('غير مصنف (Unclassified / Unknown)');
        }
    }

    /**
     * Format a combined summary label e.g. "WhatsApp / Authentication" or "App FCM / Transactional".
     *
     * @param mixed $notification
     * @return string
     */
    public static function formatCombinedLabel($notification): string
    {
        $channel = self::resolveChannel($notification);
        $purpose = self::resolvePurpose($notification);

        $channelName = self::getChannelLabel($channel);
        $purposeName = ucfirst($purpose === self::PURPOSE_LEGACY_UNKNOWN ? 'Unknown' : $purpose);

        return "{$channelName} / {$purposeName}";
    }

    /**
     * Render an HTML badge for the channel.
     *
     * @param string $channel
     * @return string
     */
    public static function renderChannelBadge(string $channel): string
    {
        switch ($channel) {
            case self::CHANNEL_APP_FCM:
                return '<span class="badge bg-label-primary badge-light-primary"><i class="fas fa-mobile-alt me-1"></i>App FCM</span>';
            case self::CHANNEL_WHATSAPP:
                return '<span class="badge bg-label-success badge-light-success"><i class="fab fa-whatsapp me-1"></i>WhatsApp</span>';
            case self::CHANNEL_SMS:
                return '<span class="badge bg-label-info badge-light-info"><i class="fas fa-sms me-1"></i>SMS</span>';
            case self::CHANNEL_EMAIL:
                return '<span class="badge bg-label-warning badge-light-warning"><i class="fas fa-envelope me-1"></i>Email</span>';
            case self::CHANNEL_IN_APP:
                return '<span class="badge bg-label-dark badge-light-dark"><i class="fas fa-bell me-1"></i>In-App</span>';
            default:
                return '<span class="badge bg-label-secondary badge-light-secondary"><i class="fas fa-question-circle me-1"></i>Legacy</span>';
        }
    }

    /**
     * Render an HTML badge for the purpose.
     *
     * @param string $purpose
     * @return string
     */
    public static function renderPurposeBadge(string $purpose): string
    {
        switch ($purpose) {
            case self::PURPOSE_AUTHENTICATION:
                return '<span class="badge bg-label-warning badge-light-warning"><i class="fas fa-shield-alt me-1"></i>توثيق</span>';
            case self::PURPOSE_TRANSACTIONAL:
                return '<span class="badge bg-label-info badge-light-info"><i class="fas fa-receipt me-1"></i>تشغيلي</span>';
            case self::PURPOSE_MARKETING:
                return '<span class="badge bg-label-marketing badge-light-marketing"><i class="fas fa-bullhorn me-1"></i>تسويقي</span>';
            case self::PURPOSE_SYSTEM:
                return '<span class="badge bg-label-secondary badge-light-secondary"><i class="fas fa-cog me-1"></i>نظام</span>';
            default:
                return '<span class="badge bg-label-secondary badge-light-secondary"><i class="fas fa-question-circle me-1"></i>غير مصنف</span>';
        }
    }

    /**
     * Returns true only if the notification utilizes App FCM.
     *
     * @param mixed $notification
     * @return bool
     */
    public static function isFcm($notification): bool
    {
        $channels = self::resolveChannels($notification);
        return in_array(self::CHANNEL_APP_FCM, $channels, true);
    }
}