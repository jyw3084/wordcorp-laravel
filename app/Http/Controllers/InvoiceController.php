<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Mail;

class InvoiceController extends Controller
{
    public function handleInvoice($order)
    {
        if ($order->invoice_status == 1) {
            return 'Invoice already sent';
        }

        $response = $this->sendInvoice($order);

        if ($response->Status == 'SUCCESS') {
            $result = json_decode($response->Result);
            $order->invoice_status = 1;
            $order->invoice_number = $result->InvoiceNumber;
            return 'SUCCESS';
        } else {
            return $response->Message;
        }
    }

    private function sendInvoice($order)
    {
        $post_data_array = array(
            //post_data 欄位資料
            'RespondType' => 'JSON',
            'Version' => '1.5',
            'TimeStamp' => time(), //請以 time() 格式 
            'MerchantOrderNo' => $order->order_number,
            'BuyerName' => $order->title,
            'BuyerUBN' => $order->serial_no,
            'BuyerEmail' => $order->email,
            'Category' => $order->invoice_type == 1 ? 'B2B':'B2C', 
            'TaxType' => 1,
            'TaxRate' => 5,
            'Amt' => (int)$order->order_price,
            'TaxAmt' => (int)$order->tax_amount,
            'TotalAmt' => (int)$order->total_price,
            'CarrierType' => $order->carrier == 2 ? 0 : '',
            'CarrierNum' => $order->carrier == 2 ? $order->barcode : '',
            'PrintFlag' => 'Y',
            'ItemName' => '翻譯服務', //多項商品時，以「|」分開 
            'ItemCount' => 1, //多項商品時，以「|」分開 
            'ItemUnit' => ' 件', //多項商品時，以「|」分開 
            'ItemPrice' => (int)$order->total_price, //多項商品時，以「|」分開 
            'ItemAmt' => (int)$order->total_price, //多項商品時，以「|」分開 
            'Status' => '1' //1=立即開立，0=待開立，3=延遲開立 
        );
        
        $post_data_str = http_build_query($post_data_array); //轉成字串排列
        $key = env('EZPAY_HASH_KEY', false); //商店專屬串接金鑰HashKey 值
        $iv = env('EZPAY_HASH_IV', false); //商店專屬串接金鑰 HashIV 值
        $post_data = trim(bin2hex(openssl_encrypt($this->addpadding($post_data_str), 'AES-256-CBC', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv))); //php 7 以上版本加密

        $url = 'https://inv.ezpay.com.tw/Api/invoice_issue';
        if(env('EZPAY_DEBUG') == true)
                $url = 'https://cinv.ezpay.com.tw/Api/invoice_issue';
        
        $MerchantID = env('EZPAY_MERCHANT_ID', false); //商店代號
        $transaction_data_array = array(//送出欄位
                'MerchantID_' => $MerchantID,
                'PostData_' => $post_data 
        );
        $transaction_data_str = http_build_query($transaction_data_array);
        $result = $this->curl_work($url, $transaction_data_str); //背景送出
        $response = json_decode($result['web_info']);
        
        return $response;
    }

    private function addpadding($string, $blocksize = 32) 
    {
        $len = strlen($string);
        $pad = $blocksize - ($len % $blocksize); 
        $string .= str_repeat(chr($pad), $pad); 
        return $string;
    }

    private function curl_work($url = '', $parameter = '') 
    {
        $curl_options = array(
            CURLOPT_URL => $url, 
            CURLOPT_HEADER => false, 
            CURLOPT_RETURNTRANSFER => true, 
            CURLOPT_USERAGENT => 'ezPay', 
            CURLOPT_FOLLOWLOCATION => true, 
            CURLOPT_SSL_VERIFYPEER => false, 
            CURLOPT_SSL_VERIFYHOST => false, 
            CURLOPT_POST => '1', 
            CURLOPT_POSTFIELDS => $parameter
        );
        $ch = curl_init(); 
        curl_setopt_array($ch, $curl_options);
        $result = curl_exec($ch);
        $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_errno($ch);
        curl_close($ch); 
        $return_info = array(
            'url' => $url,
            'sent_parameter' => $parameter, 
            'http_status' => $retcode, 
            'curl_error_no' => $curl_error, 
            'web_info' => $result
        );
        return $return_info;
    }
}
