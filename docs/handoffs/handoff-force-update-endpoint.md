# Endpoint التحديث الإجباري (Force Update)

تاريخ: 2026-11-19  
الجهة: مبرمج الموبايل  
الغرض: معرفة الـ endpoint المستخدم لتفعيل/قراءة إعدادات التحديث الإجباري

---

## الـ Endpoint المستخدم حالياً في التطبيق

| البند | القيمة |
| --- | --- |
| Method | `GET` |
| Path | `/api/app_config_new` |
| Full URL (إنتاج) | `https://cleanstation.app/api/app_config_new` |
| Auth | غير مطلوب |
| مكان الاستدعاء في التطبيق | `lib/screens/mutual/splash/bloc/app_config_bloc.dart` → `_repo()` |

```dart
return await dioHelper.getFromServer(
  url: 'app_config_new',
);
```

---

## Endpoint قديم (لا تستخدمه)

| البند | القيمة |
| --- | --- |
| Path | `/api/app_config` |
| ملاحظة | قديم وموجود للتوافق فقط. التطبيق الحالي يعتمد `app_config_new` |

---

## شكل الاستجابة

```json
{
  "status": "success",
  "message": "A new version is available. Please update your app.",
  "maintenance_mode": false,
  "force_update": true,
  "clear_local_data": false,
  "min_clear_local_data_version": "0.0.0",
  "force_clean_local_data": false,
  "force_clean_local_data_id": "",
  "android": {
    "latest_version": "3.0.4",
    "min_supported_version": "3.0.2"
  },
  "ios": {
    "latest_version": "3.0.4",
    "min_supported_version": "3.0.4"
  }
}
```

الحقول المهمة للتحديث:

- `force_update` (bool)
- `android.latest_version` (string مثل `3.0.5`)
- `android.min_supported_version`
- `ios.latest_version`
- `ios.min_supported_version`

---

## كيف التطبيق يقرر؟

من `ForceUpdateService.hasNewVersion()`:

1. إذا `force_update == true` وإصدار الجهاز `< latest_version`  
   → حوار تحديث **إجباري** (ما في خيار لاحقاً)

2. إذا `force_update == false` وإصدار الجهاز `< min_supported_version`  
   → حوار تحديث **اختياري** (فيه Update Later)

3. غير ذلك  
   → يكمل بدون حوار تحديث

مقارنة الإصدارات تتم بـ semantic version (`pub_semver`).

---

## أين زر التحديث يودّي؟

من `ForceUpdateDialog.launchStore()`:

- Android → Google Play  
  `https://play.google.com/store/apps/details?id=com.googansolutions.cleanstation`
- iOS → App Store  
  `https://apps.apple.com/app/id1670950492`

**مهم:** الزر يفتح متاجر الإنتاج، مش TestFlight.

---

## متى نفعّل الفورس أبديت؟

1. بعد نشر النسخة المصلحة على App Store و Google Play وظهورها فعلياً للمستخدمين.
2. بعدين يعدّل السيرفر قيم `app_config_new`:
   - `latest_version` = رقم النسخة الجديدة (iOS و Android)
   - `force_update` = `true`
3. لا تفعّله والنسخة لسا على TestFlight فقط.

مثال بعد نشر نسخة `3.0.5`:

```json
{
  "force_update": true,
  "android": {
    "latest_version": "3.0.5",
    "min_supported_version": "3.0.5"
  },
  "ios": {
    "latest_version": "3.0.5",
    "min_supported_version": "3.0.5"
  }
}
```

---

## ملاحظة للموبايل / الباك اند

القيم تُقرأ من إعدادات قاعدة البيانات عبر:

`packages/core/settings/src/Controllers/Api/SettingsController.php`  
الدالة: `appConfigNew()`

التحكم من لوحة التحكم:

الإعدادات العامة → تاب **تحديث التطبيق** / **App Update**

بعد حفظ الإعدادات، `GET /api/app_config_new` يرجع القيم الجديدة مباشرة (بدون تعديل كود).

---

## ملخص سطر واحد

> استخدم `GET /api/app_config_new` فقط. فعّل `force_update` من تاب تحديث التطبيق في الإعدادات العامة بعد نشر نسخة المتاجر، مع تحديث `latest_version` لرقم النسخة الجديدة.
