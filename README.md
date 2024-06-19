# Abdal Zarinpal PG
<p align="center"><img src="resources/img/zlogo.png?raw=true"></p>

 ## 💎 هدف اصلی
این پکیج برای یکپارچه‌سازی درگاه پرداخت زرین پال با لاراول طراحی شده است.

## ⚓ پیش نیاز برای برنامه نویسان
> PHP 7.2 یا بالاتر  
> Laravel 7.0 یا بالاتر

## ✨ قابلیت‌ها

- درخواست پرداخت
- تایید پرداخت
- مدیریت خطاها
- استفاده آسان با متدهای زنجیره‌ای
- انتخاب واحد پول در زمان درخواست هر تراکنش
- پشتیبانی از آخرین نسخه  و جدیدترین نسخه های لاراول

## 📝 چگونه کار می‌کند؟

### نصب
برای نصب پکیج از کامپوزر استفاده کنید:
```bash
composer require abdal/abdal-zarinpal-pg
```

### تنظیمات

سپس سرویس‌پراوایدر را در فایل config/app.php اضافه کنید: (برای لاراول 11 نیاز به انجام این مورد ندارید) 
```bash
'providers' => [
// ...
Abdal\AbdalZarinpalPg\ZarinpalServiceProvider::class,
];
```
و فاساد را ثبت کنید: (برای لاراول 11 نیاز به انجام این مورد ندارید) 
```bash
'aliases' => [
// ...
'Zarinpal' => Abdal\AbdalZarinpalPg\Facades\Zarinpal::class,
];
```
استفاده

برای درخواست پرداخت یا ارسال مشتری به درگاه:
```bash
use Abdal\AbdalZarinpalPg\Zarinpal;

 
$response = Zarinpal::merchantId('00000000-0000-0000-0000-000000000000')
    ->amount(13660000)
    ->currency('IRT')
    ->callbackUrl(route('payment.verify'))
    ->description('خرید تست')
    ->email('info@ebrasha.com')
    ->mobile('09022223301')
    ->request();

if (!$response->success()) {
    return response()->json(['error' => $response->message()], 400);
}

$authority = $response->getAuthority(); // Save Authority in Database
return $response->redirect();

```

برای تایید پرداخت مشتری:
```bash

use Abdal\AbdalZarinpalPg\Zarinpal;


$response = Zarinpal::merchantId('00000000-0000-0000-0000-000000000000')
    ->amount(13660000)
    ->currency('IRT')
    ->authority($request->query('Authority'))
    ->verify();

if (!$response->success()) {
    return response()->json(['error' => $response->message()], 400);
}

return $response->referenceId();
```
## ❤️ کمک به پروژه

https://alphajet.ir/abdal-donation

## 🤵 برنامه نویس
دست ساز با عشق توسط ابراهیم شفیعی (ابراشا)

E-Mail = Prof.Shafiei@Gmail.com

Telegram: https://t.me/ProfShafiei

## ☠️ گزارش خطا

اگر با مشکلی در پیکربندی مواجه هستید یا چیزی آنطور که انتظار دارید کار نمی‌کند، لطفا از Prof.Shafiei@Gmail.com استفاده کنید.طرح مشکلات بر روی  GitLab یا Github نیز پذیرفته می‌شوند.

