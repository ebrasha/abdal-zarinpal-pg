<?php

/*
 **********************************************************************
 * -------------------------------------------------------------------
 * Project Name : Abdal Zarinpal PG
 * File Name    : Zarinpal.php
 * Author       : Ebrahim Shafiei (EbraSha)
 * Email        : Prof.Shafiei@Gmail.com
 * Created On   : 2024-06-21
 * Description  : Abdal Zarinpal PG Main Class
 * -------------------------------------------------------------------
 *
 * "Coding is an engaging and beloved hobby for me. I passionately and insatiably pursue knowledge in cybersecurity and programming."
 * – Ebrahim Shafiei
 *
 **********************************************************************
 */

namespace Abdal\AbdalZarinpalPg;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class Zarinpal
{
    protected $merchantId;
    protected $amount;
    protected $callbackUrl;
    protected $description;
    protected $email;
    protected $mobile;
    protected $currency;
    protected $authority;
    protected $code;
    protected $result;

    public function __construct()
    {
        $this->merchantId = (Cache::has("ZARINPAL_MERCHANT_ID")) ? Cache::get("ZARINPAL_MERCHANT_ID") : env('ZARINPAL_MERCHANT_ID', 'your-merchant-id');
        $this->currency = (Cache::has("ZARINPAL_CURRENCY")) ? Cache::get("ZARINPAL_CURRENCY") : env('ZARINPAL_CURRENCY', 'IRT');
    }

    public static function merchantId($merchantId)
    {
        $instance = new self();
        return $instance->setMerchantId($merchantId);
    }


    public function setMerchantId($merchantId)
    {
        $this->merchantId = $merchantId;
        Log::info('setMerchantId executed', ['merchantId' => $this->merchantId]);
        return $this;
    }

    public function currency($currency)
    {
        $this->currency = $currency;
        return $this;
    }


    public function amount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    public function callbackUrl($callbackUrl)
    {
        $this->callbackUrl = $callbackUrl;
        return $this;
    }

    public function description($description)
    {
        $this->description = $description;
        return $this;
    }

    public function email($email)
    {
        $this->email = $email;
        return $this;
    }

    public function mobile($mobile)
    {
        $this->mobile = $mobile;
        return $this;
    }

    public function authority($authority)
    {
        $this->authority = $authority;
        return $this;
    }

    public function request()
    {
        $data = [
            "merchant_id" => $this->merchantId,
            "amount" => $this->amount,
            "callback_url" => $this->callbackUrl,
            "description" => $this->description,
            "currency" => $this->currency,
            "metadata" => [
                "email" => $this->email,
                "mobile" => $this->mobile,
            ]
        ];

        $url = 'https://api.zarinpal.com/pg/v4/payment/request.json';

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'User-Agent' => 'ZarinPal Rest Api v1'
        ])
        ->timeout(30)              // ⏱ Set max timeout to 30 seconds
        ->retry(3, 100)            // 🔁 Retry 3 times, wait 100ms between retries
        ->post($url, $data);


        $this->result = $response->json();
        $this->code = $this->result['errors']['code'] ?? $this->result['data']['code'] ?? null;
        $this->authority = $this->result['data']['authority'] ?? null;

        Log::info('request executed', [
            'result' => $this->result,
            'code' => $this->code,
            'authority' => $this->authority,
        ]);

        return $this;
    }

    public function verify()
    {
        $data = [
            "merchant_id" => $this->merchantId,
            "authority" => $this->authority,
            "amount" => $this->amount
        ];

        $url = 'https://api.zarinpal.com/pg/v4/payment/verify.json';

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'User-Agent' => 'ZarinPal Rest Api v4'
        ])
        ->timeout(30)
        ->retry(3, 100)
        ->post($url, $data);

        $this->result = $response->json();
        $this->code = $this->result['errors']['code'] ?? $this->result['data']['code'] ?? null;

        Log::info('verify executed', [
            'result' => $this->result,
            'code' => $this->code,
        ]);

        return $this;
    }

    public function success()
    {
        return isset($this->result['data']['code']) && $this->result['data']['code'] == 100;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function getAuthority()
    {
        return $this->authority;
    }

    public function redirect()
    {
        if ($this->success()) {
            $redirectUrl = 'https://www.zarinpal.com/pg/StartPay/' . $this->authority;
            return redirect($redirectUrl);
        }

        return response()->json(['error' => $this->message()], 400);
    }

    public function referenceId()
    {
        if ($this->success()) {
            return response()->json([
                'message' => 'Transaction successful',
                'ref_id' => $this->result['data']['ref_id']
            ], 200);
        }

        return response()->json(['error' => $this->message()], 400);
    }

    public function message(): string
    {
        switch ($this->code) {
            case -9 :
                return 'خطای اعتبار سنجی: 1- مرچنت کد داخل تنظیمات وارد نشده باشد، 2- آدرس بازگشت وارد نشده باشد، 3- توضیحات بیشتر از حد مجاز، 4- مبلغ پرداختی نامعتبر';
            case -10 :
                return 'آی‌پی و یا مرچنت كد پذیرنده صحیح نیست.';
            case -11 :
                return 'مرچنت کد فعال نیست؛ لطفا با تیم پشتیبانی ما تماس بگیرید.';
            case -12 :
                return 'تلاش بیش از حد در یک بازه زمانی کوتاه';
            case -15 :
                return 'ترمینال شما به حالت تعلیق در آمده است؛ با تیم پشتیبانی تماس بگیرید.';
            case -16 :
                return 'سطح تایید پذیرنده پایین‌تر از سطح نقره‌ای است.';
            case -30 :
                return 'اجازه دسترسی به تسویه اشتراکی شناور ندارید.';
            case -31 :
                return 'حساب بانکی تسویه را به پنل اضافه کنید. مقادیر وارد شده برای تسهیم درست نیست. پذیرنده جهت استفاده از خدمات سرویس تسویه اشتراکی شناور، باید حساب بانکی معتبری به پنل کاربری خود اضافه نماید.';
            case -32 :
                return 'مقادیر وارد شده برای تسهیم درست نیست و از مقدار حداکثر بیشتر است.';
            case -33 :
                return 'درصدهای وارد شده درست نیست.';
            case -34 :
                return 'مبلغ از کل تراکنش بیشتر است.';
            case -35 :
                return 'تعداد افراد دریافت کننده تسهیم بیش از حد مجاز است.';
            case -40 :
                return 'مقادیر extra درست نیست؛ expire_in معتبر نیست.';
            case -50 :
                return 'مبلغ پرداخت شده با مقدار مبلغ در وریفای متفاوت است.';
            case -51 :
                return 'پرداخت ناموفق';
            case -52 :
                return 'خطای غیر منتظره؛ با پشتیبانی تماس بگیرید.';
            case -53 :
                return 'اتوریتی برای این مرچنت کد نیست.';
            case -54 :
                return 'اتوریتی نامعتبر است.';
            case -101 :
                return 'تراکنش قبلا وریفای شده است.';
            default:
                return 'خطای پیش بینی نشده‌ای رخ داده است.';
        }
    }
}
