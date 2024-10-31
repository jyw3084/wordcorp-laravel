<?php

namespace App\Admin\Actions\Grid;

use Dcat\Admin\Grid\RowAction;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Models;
use Mail;

class SendInvoice extends RowAction
{
    public $email;
    /**
     * Title
     *
     * @return string
     */
    public function title()
    {
        return '<i class="fa fa-envelope-o"></i> Send Invoice';
    }

    /**
     * Set the confirmation popup message, if it returns null, no popup will be shown
     *
     * Allows return of string or array types
     *
     * @return array|string|void
     */
    public function confirm()
    {
        return [
            "Are you sure want to send this mail?",
        ];
    }

    /**
     * Processing Requests
     *
     * @param Request $request
     *
     * @return \Dcat\Admin\Actions\Response
     */
    public function handle(Request $request)
    {
        // Get the current row ID
        $id = $this->getKey();

        // Get the current row Email
        $order = Models\Order::find($id);

        $this->email = $order->email;
        $this->invoice = $order->invoice_number;

        if($order->invoice_status == 1)
        {
            // Please add here the procedure for sending invoices by email
            Mail::send([], [], function($message) {
                $message->to($this->email, 'Invoce')
                    ->subject('Admin send an Invoice');
                $message->from(config('mail.mailers.smtp.username'),'Word Corp Admin');
                $message->setBody('This is the wordcorp invoice number: '.$this->invoice);
            });

            return $this->response()->success("Invoice sent successfully: " . $this->email)->refresh();
        }

        $response = $this->get_invoice($order);

        if($response->Status == 'SUCCESS')
        {
            $invoice = json_decode($response->Result);
            $this->invoice = $invoice->InvoiceNumber;

            $order->invoice_status = 1;
            $order->invoice_number = $this->invoice;
            $order->save();

    
            // Returns the response and refreshes the page
            return $this->response()->success("Invoice sent successfully: " . $this->email)->refresh();
        }
        else
            return $this->response()->warning($response->Message);

    }

    private function get_invoice($order)
    {
        $merchant_id = env('EZPAY_MERCHANT_ID', false);
        $key = env('EZPAY_HASH_KEY', false);
        $iv = env('EZPAY_HASH_IV', false);


        $url = 'https://inv.ezpay.com.tw/Api/invoice_issue';
        if(env('EZPAY_DEBUG') == true)
            $url = 'https://cinv.ezpay.com.tw/Api/invoice_issue';

        $piece = count(json_decode($order->associated_docs));
        $data = [];
        $data['RespondType'] = 'JSON';
        $data['Version'] = '1.5';
        $data['TimeStamp'] = time();
        $data['MerchantOrderNo'] = $order->order_number;
        $data['Status'] = '1';
        $data['Category'] = $order->invoice_type == 1 ? 'B2B':'B2C';
        $data['BuyerName'] = $order->title;
        $data['BuyerEmail'] = $order->email;
        $data['CarrierType'] = $order->carrier == 2 ? 2 : '';
        $data['CarrierNum'] = $order->carrier == 2 ? $order->barcode : '';
        $data['BuyerUBN'] = $order->serial_no;
        $data['PrintFlag'] = 'Y';
        $data['TaxType'] = $order->invoice_type == 1 ? 0: 1;
        $data['TaxRate'] = 5;
        $data['Amt'] = (int)($order->total_price - $order->tax_amount);
        $data['TaxAmt'] = (int)$order->tax_amount;
        $data['TotalAmt'] = (int)$order->total_price;
        $data['ItemName'] = '翻譯服務';
        $data['ItemCount'] = 1;
        $data['ItemUnit'] = $piece.'件';
        $data['ItemPrice'] = (int)$order->total_price;
        $data['ItemAmt'] = (int)$order->total_price;

        $post_data_str = http_build_query($data);
        $post_data = trim(bin2hex(openssl_encrypt($this->addpadding($post_data_str),'AES-256-CBC', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv)));

        $invoice = [];
        $invoice['MerchantID_'] = $merchant_id;
        $invoice['PostData_'] = $post_data;
        $transaction_data_str = http_build_query($invoice);
        $result = $this->curl_work($url, $transaction_data_str);
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
