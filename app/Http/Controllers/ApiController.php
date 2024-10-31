<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models;
use Mail;
use Session;
use App\Models\EmailManagement;
use App\Mail\NewOrder;
use App\Models\Order;
use App\Models\User;
use App\Mail\OrderComplete;
use App\Http\Controllers\InvoiceController;
use Ycs77\NewebPay\Facades\NewebPay;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use GoogleDriveAdapter;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_Permission;

class ApiController extends Controller
{
	public function trans_deliver(Request $request)
	{
		$order = Models\Order::find($request->id);
		$docs = json_decode($order->associated_docs);
		$data = [];
		$status = 4;
		$needs = count($docs);
		$finished = 0;
		foreach($docs as $k => $v)
		{
			if($v->id == $request->doc) {
				$v->translator_deliver_date = date('Y-m-d H:i:s');
				
				$payment = new Models\Payment;
				$payment->order_id = $order->id;
				$payment->uid = $v->translator;
				$payment->total = $v->translator_pay;
				$payment->lang_combo = $v->language_combination;
				$payment->trans = $v->word_count;
				$payment->filename = $v->filename;
				$payment->type = 1;
				$payment->order_deadline = $order->deadline;
				$payment->save();
			}
			
			if($v->translator_deliver_date != null && ($v->editor == null || $v->editor_deliver_date != null))
			$finished++;
			
			$data[] = $v;
		}
		if($finished == $needs)
		$status = 5;
		
		$order->associated_docs = json_encode($data);
		$order->order_status = $status;
		$order->save();
		
		$result = array(
			'code' => 200
		);
		
		return response()->json($result);
		
	}
	
	public function editor_deliver(Request $request)
	{
		$order = Models\Order::find($request->id);
		$docs = json_decode($order->associated_docs);
		$data = [];
		$status = 4;
		$needs = count($docs);
		$finished = 0;
		foreach($docs as $k => $v)
		{
			if($v->id == $request->doc) {
				$v->editor_deliver_date = date('Y-m-d H:i:s');
				
				$payment = new Models\Payment;
				$payment->order_id = $order->id;
				$payment->uid = $v->editor;
				$payment->total = $v->editor_pay;
				$payment->lang_combo = $v->language_combination;
				$payment->edit = $v->word_count;
				$payment->filename = $v->filename;
				$payment->type = 2;
				$payment->order_deadline = $order->deadline;
				$payment->save();
			}
			
			if($v->translator_deliver_date != null && ($v->editor == null || $v->editor_deliver_date != null))
			$finished++;
			
			$data[] = $v;
		}
		if($finished == $needs)
		$status = 5;
		$order->associated_docs = json_encode($data);
		$order->order_status = $status;
		$order->save();
		
		$result = array(
			'code' => 200
		);
		
		return response()->json($result);
		
	}
	
	function translatorAssignToMe()
	{
		$str = file_get_contents("php://input");
		if (!$str) {
			$param = array_merge($_POST, $_GET);
			$str = json_encode($param, 320);
		} else {
			$param = json_decode($str, true);
			$param ? $param : parse_str($str, $param);
		}
		
		$order = Order::find($param['orderID']);
		if($order['associated_docs']){
			$associated_doc = json_decode($order['associated_docs'], 420);
		}
		$associated_doc_result = [];
		foreach($associated_doc as $data){
			if($data['file'] == $param['file']){
				if(array_key_exists('translator', $data)){
					$data['translator'] = $param['translator_id'];
				}
				else{
					$data = array_merge($data,  array('translator'=> $param['translator_id']));
				}
				$user = User::find($param['translator_id']);
				
				$client = new Google_Client();
				$client->setClientId(env('GOOGLE_CLIENT_ID'));
				$client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
				$client->refreshToken(env('GOOGLE_REFRESH_TOKEN'));
				$service = new Google_Service_Drive($client);
				$service->permissions->delete($data['file_id'], 'anyoneWithLink', array('supportsTeamDrives'=>true));
				
				$permission = new Google_Service_Drive_Permission();
				$permission->setRole('writer');
				$permission->setType('user');
				$permission->setEmailAddress($user->email);
				
				$permissions_id = $service->permissions->create($data['file_id'], $permission);
				
				$data = array_merge($data,  array('translator_permissions_id'=> $permissions_id->id));
			}
			array_push($associated_doc_result, $data);
		}
		// dd(json_encode($associated_doc_result));
		
		
		$order->associated_docs = json_encode($associated_doc_result);
		if($order->save()){
			$order->save();
			return true;
		}
		else{
			return false;
		}
	}
	
	function sendToEditor()
	{
		$str = file_get_contents("php://input");
		if (!$str) {
			$param = array_merge($_POST, $_GET);
			$str = json_encode($param, 320);
		} else {
			$param = json_decode($str, true);
			$param ? $param : parse_str($str, $param);
		}
		
		$order = Order::find($param['orderID']);
		$order->order_status = 4;
		if($order['associated_docs']){
			$associated_doc = json_decode($order['associated_docs'], 420);
		}
		
		$user = User::where([['email', $order->email], ['user_type', 2]])->first();
		$comb = isset($user->language_combination) ? json_decode($user->language_combination) : null;
		$lang = [];
		if($comb)
		{
			foreach($comb as $k => $v)
			{
				if(isset($v->editor))
				$lang[$v->language] = $v->editor;
			}
		}
		
		$array = '';
		$document_for_editors = '';
		$fileName = '';
		$filePath = '';
		$associated_doc_result = [];
		foreach($associated_doc as $data) {
			if($data['file'] == $param['file']) {
				$v = (object)$data;
				
				$payment = new Models\Payment;
				$payment->order_id = $order->id;
				$payment->uid = $v->translator;
				$payment->total = $v->translator_pay;
				$payment->lang_combo = $v->language_combination;
				$payment->trans = $v->word_count;
				$payment->filename = $v->filename;
				$payment->type = 1;
				$payment->order_deadline = $order->deadline;
				$payment->save();
				
				if(array_key_exists('translator', $data)){
					$data['translator_finish'] = true;
				}
				else{
					$data = array_merge($data,  array('translator_finish'=> true));
				}
				$translator = User::find($data['translator']);
				
				$idRef = strpos($v->file, 'id');
				$idRef2 = strpos($v->file, '&');
				$filePath = substr($v->file, $idRef+3, $idRef2 - ($idRef+3));
				$fileName = $v->filename;
				$wordcount = $v->word_count;
				$fee = $v->editor_pay;
				
				$client = new Google_Client();
				$client->setClientId(env('GOOGLE_CLIENT_ID'));
				$client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
				$client->refreshToken(env('GOOGLE_REFRESH_TOKEN'));
				$service = new Google_Service_Drive($client);
				$permission = new Google_Service_Drive_Permission();
				$permission->setRole('reader');
				$permission->setType('anyone');
				$service->permissions->create($data['file_id'], $permission);
				
				$document_for_editors = '<ul style="list-style: none;">
				<li>Service: '.$v->language_combination.'</li>
				<li><a href="https://docs.google.com/document/d/'.$filePath.'/edit">'.$fileName.'</a></li>
				<li>Word Count: '.$v->word_count.' words</li>
				<li>Due Date/Time: '.$v->doc_deadline.'</li>
				<li>Fee: '.$fee.' NTD</li>
				<li>Note: '.$v->notes.'</li>
				</ul>
				<br />';
			}
			$order_currency = $order->overseas == 1 ? 'USD' : 'NTD';
			
			$array = array(
				'{ document_for_editors }' => $document_for_editors,
				'{ dashboard }'=> url('/translator/editor-bin'),
				'{ url }'=> url('new-order/order/'.$order->order_number),
				'{ order_date }'=> $order->order_date,
				'{ order_number }'=> $order->order_number,
				'{ price }'=> $order->order_price,
				'{ currency }'=> $order_currency,
				'{ delivery_date }'=> $order->delivery_date,
				'{ deadline }'=> $order->deadline,
				'{ email }'=> $order->email,
			);
			array_push($associated_doc_result, $data);
		}
		
		$editors = User::where([['user_type', 3], ['roles', 2], ['active', 1]])->orWhere([['user_type', 3], ['roles', 3], ['active', 1]])->get();
		foreach ($editors as $k => $v) {
			$email = $v->email;
			$mail_template = EmailManagement::where('template_name', 'editor_notice')->first();
			$min = 0;
			$content = strtr($mail_template->{'mail_body_'.$order->locale}, $array);
			
			$email_data = array(
				'content' => $content,
				'name' => $mail_template->sender_name,
				'subject' => $mail_template->{'mail_subject_'.$order->locale},
			);
			Mail::to($email)->later(now()->addMinutes($min), new OrderComplete($email_data));
		}
		
		$order->associated_docs = json_encode($associated_doc_result);
		
		if($order->save()){
			//send a mail to client to notify that his order was completed
			$mail_template = EmailManagement::where('template_name', 'submit_confirmation')->first();
			
			$email = $translator->email;
			$array = array(
				'{ date }' => date('m/d/Y H:i'),
				'{ doc }' => $fileName,
				'{ wordcount }' => $wordcount,
				'{ fee }' => $fee,
				'{ doc_link }' => 'https://docs.google.com/document/d/'.$filePath.'/edit',
				'{ translator }'=> $translator->name,
				'{ designation_link }'=> url('designation/translator/'.$data['language_combination'].'/'.$order->client->id.'/'.$translator->id),
			);
			$content = strtr($mail_template->{'mail_body_'.$order->locale}, $array);
			
			$data = array(
				'content' => $content,
				'name' => $mail_template->sender_name,
				'subject' => $mail_template->{'mail_subject_'.$order->locale},
			);
			
			Mail::to($email)->later(now()->addMinutes(0), new OrderComplete($data));
			return true;
		}
		else{
			return false;
		}
	}
	
	function editorAssignToMe(Request $request)
	{
		$str = file_get_contents("php://input");
		if (!$str) {
			$param = array_merge($_POST, $_GET);
			$str = json_encode($param, 320);
		} else {
			$param = json_decode($str, true);
			$param ? $param : parse_str($str, $param);
		}
		
		$order = Order::find($param['orderID']);
		if($order['associated_docs']){
			$associated_doc = json_decode($order['associated_docs'], 420);
		}
		$associated_doc_result = [];
		foreach($associated_doc as $data){
			if($data['file'] == $param['file']){
				if(array_key_exists('editor', $data)){
					$data['editor'] = $param['editor_id'];
				}
				else{
					$data = array_merge($data,  array('editor'=> $param['editor_id']));
				}
				$editor = User::find($param['editor_id']);
				
				$client = new Google_Client();
				$client->setClientId(env('GOOGLE_CLIENT_ID'));
				$client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
				$client->refreshToken(env('GOOGLE_REFRESH_TOKEN'));
				$service = new Google_Service_Drive($client);
				
				$permission = new Google_Service_Drive_Permission();
				$permission->setRole('writer');
				$permission->setType('user');
				$permission->setEmailAddress($editor->email);
				$service->permissions->create($data['file_id'], $permission);
			}
			array_push($associated_doc_result, $data);
		}
		
		$order->associated_docs = json_encode($associated_doc_result);
		if($order->save())
		{
			$order->save();
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function sendToClient()
	{
		$str = file_get_contents("php://input");
		if (!$str) {
			$param = array_merge($_POST, $_GET);
			$str = json_encode($param, 320);
		} else {
			$param = json_decode($str, true);
			$param ? $param : parse_str($str, $param);
		}
		
		$order = Order::find($param['orderID']);
		if ($order['associated_docs']) {
			$associated_doc = json_decode($order['associated_docs'], 420);
		}
		$documents = '';
		$single_doc = '';
		$fileName = '';
		$filePath = '';
		$associated_doc_result = [];
		$i = 0;

		foreach ($associated_doc as $data) {
			$v = (object)$data;
			$idRef = strpos($v->file, 'id');
			$idRef2 = strpos($v->file, '&');
			$filePath = substr($v->file, $idRef+3, $idRef2 - ($idRef+3));
			$wordcount = $v->word_count;
			$fileName = $v->filename;
			
			if ($data['file'] == $param['file']) {
				if($v->service_type == 1) {
					$payment = new Models\Payment;
					$payment->order_id = $order->id;
					$payment->uid = $v->editor;
					$payment->total = $v->editor_pay;
					$payment->lang_combo = $v->language_combination;
					$payment->edit = $v->word_count;
					$payment->filename = $v->filename;
					$payment->type = 2;
					$payment->order_deadline = $order->deadline;
					$payment->save();
					$data['editor_finish'] = true;
				} else {
					$payment = new Models\Payment;
					$payment->order_id = $order->id;
					$payment->uid = $v->translator;
					$payment->total = $v->translator_pay;
					$payment->lang_combo = $v->language_combination;
					$payment->edit = $v->word_count;
					$payment->filename = $v->filename;
					$payment->type = 1;
					$payment->order_deadline = $order->deadline;
					$payment->save();
					$data['translator_finish'] = true;
				}
				
				$translator = USER::find($data['translator']);
				if ($v->service_type == 1) {
					$editor = USER::find($data['editor']);
					$user_id = $editor->id;
					$type = 'editor';
				} else {
					$user_id = $translator->id;
					$type = 'translator';
				}
			
				$translator_name = $translator->name;
				$designation_link = url('designation/translator/'.$data['language_combination'].'/'.$order->client->id.'/'.$translator->id);

				// set google doc permission to anyone can view
				$client = new Google_Client();
				$client->setClientId(env('GOOGLE_CLIENT_ID'));
				$client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
				$client->refreshToken(env('GOOGLE_REFRESH_TOKEN'));
				$service = new Google_Service_Drive($client);
				$permission = new Google_Service_Drive_Permission();
				$permission->setRole('reader');
				$permission->setType('anyone');
				$service->permissions->create($data['file_id'], $permission);

				if ($order->locale === 'en') {
					$single_doc = '<li>
						<p>Document: 【'.$fileName.'】</p>
						<p>Download link: <a href="https://docs.google.com/document/d/'.$filePath.'/export?format=docx">'.$fileName.'</a></p>
						<p>Translator: '.$translator_name.'</p>
						<p><a href="'.$designation_link.'">Designate as preferred translator</a></p>
					</li>';
				} else {
					$single_doc = '<li>
						<p>文件：【'.$fileName.'】</p>
						<p>下載連結：<a href="https://docs.google.com/document/d/'.$filePath.'/export?format=docx">'.$fileName.'</a></p>
						<p>翻譯師：'.$translator_name.'</p>
						<p><a href="'.$designation_link.'">指定為最愛譯者</a></p>
					</li>';
				}

				if($v->service_type == 1)
				{
					$mail_template = EmailManagement::where('template_name', 'review_notification_for_translator')->first();
				}
				else
				{
					$mail_template = EmailManagement::where('template_name', 'submit_confirmation')->first();
				}
				
				$email = $translator->email;
				$array = array(
					'{ date }' => date('m/d/Y H:i'),
					'{ single_doc }' => $single_doc,
					'{ doc }' => $fileName,
					'{ wordcount }' => $wordcount,
					'{ doc_link }' => 'https://docs.google.com/document/d/'.$filePath.'/export?format=docx',
					'{ translator }'=> $translator->name,
				);
				$content = strtr($mail_template->{'mail_body_'.$order->locale}, $array);
				
				$email_data = array(
					'content' => $content,
					'name' => $mail_template->sender_name,
					'subject' => $mail_template->{'mail_subject_'.$order->locale},
				);
				Mail::to($email)->later(now()->addMinutes(0), new OrderComplete($email_data));
				$i++;
			}
			elseif ($v->service_type == 1 && isset($data['editor_finish']) && $data['editor_finish'] == true) {
				$i++;
			}
			elseif ($v->service_type == 0 && isset($data['translator_finish']) && $data['translator_finish'] == true) {
				$i++;
			}
			array_push($associated_doc_result, $data);
			if ($order->locale === 'en') {
				$documents .= '<li>
					<p>Document: 【'.$fileName.'】</p>
					<p>Download link: <a href="https://docs.google.com/document/d/'.$filePath.'/export?format=docx">'.$fileName.'</a></p><br/>
				</li>';
			} else {
				$documents .= '<li>
					<p>文件：【'.$fileName.'】</p>
					<p>下載連結：<a href="https://docs.google.com/document/d/'.$filePath.'/export?format=docx">'.$fileName.'</a></p><br/>
				</li>';
			}
		}
		
		$order->associated_docs = json_encode($associated_doc_result);
		$order->order_status = 4;
		if (count($associated_doc) == $i) {
			$order->order_status = 5;
			$order->delivery_date = date('Y-m-d H:i:s');
			if ($order->autosend_invoice == 1) {
				$order->invoice_response = (new InvoiceController)->handleInvoice($order);
			}
		}

		if ($order->save()) {
			$mail_template = EmailManagement::where('template_name', 'order_deliver')->first();
			$email = $order->notice == 1 ? $order->email : env('ADMIN_EMAIL');
			$total_price = $order->total_price;
			$array = array(
				'email' => $email,
				'{ documents }' => $documents,
				'{ single_doc }' => $single_doc,
				'{ doc }' => $fileName,
				'{ doc_link }' => 'https://docs.google.com/document/d/'.$filePath.'/export?format=docx',
				'{ translator }'=> $translator->name,
				'{ total_price }'=> $total_price,
			);
			$content = strtr($mail_template->{'mail_body_'.$order->locale}, $array);
			$email_data = array(
				'content' => $content,
				'name' => $mail_template->sender_name,
				'subject' => $mail_template->{'mail_subject_'.$order->locale},
			);
			Mail::to($email)->later(now()->addMinutes(0), new OrderComplete($email_data));

			if ($order->order_status == 5) {
				$mail_template = EmailManagement::where('template_name', 'order_finish')->first();
				$content = strtr($mail_template->{'mail_body_'.$order->locale}, $array);
				$email_data = array(
					'content' => $content,
					'name' => $mail_template->sender_name,
					'subject' => $mail_template->{'mail_subject_'.$order->locale},
				);
				Mail::to($email)->later(now()->addMinutes(0), new OrderComplete($email_data));
			}

			return true;
		} else {
			return false;
		}
	}
	
	function newebPay(Request $request)
	{
		
		$order = Order::find($request->id);
		
		// print_r($param);die;
		$payment = NewebPay::payment(
			$order->order_number, // 訂單編號
			round($order->total_price), // 交易金額
			'翻譯服務', // 交易描述
			$order->email // 付款人信箱
			)
			->setReturnURL(URL::to('api/newebpay/return'))
			->setClientBackURL(URL::to('new-order/order', $order->order_number))
			->submit();
			
		return $payment;
	}
		
	public function return(Request $request)
	{
		$return = (object) NewebPay::decode($request->input('TradeInfo'));
		if($return->Status == 'SUCCESS')
		{
			$order = Order::where('order_number', $return->Result['MerchantOrderNo'])->first();
			$order->payment_status = 1;
			$order->order_status = 3;
			if($order->save())
			{
				$user = User::where([['email', $order->email], ['user_type', 2]])->first();
				$comb = isset($user->language_combination) ? json_decode($user->language_combination) : null;
				$lang = [];
				if($comb)
				{
					foreach($comb as $k => $v)
					{
						if(isset($v->translator))
						$lang[$v->language] = $v->translator;
					}
				}
				$docs = json_decode($order->associated_docs);
				$documents = '';
				$document_for_translators = '';
				$fileName = '';
				$filePath = '';
				foreach($docs as $doc)
				{
					$idRef = strpos($doc->file, 'id');
					$idRef2 = strpos($doc->file, '&');
					$filePath = substr($doc->file, $idRef+3, $idRef2 - ($idRef+3));
					$fileName = $doc->filename;
					if ($order->locale === 'en') {
						$documents .= '<ul style="list-style: none;">
						<li>Service: '.$doc->language_combination.'</li>
						<li>Name: '.$fileName.'</li>
						<li>Word Count: '.$doc->word_count.' words</li>
						<li>Fee: '.round($doc->doc_price).' NTD</li>
						<li>Note: '.$doc->notes.'</li>
						</ul>
						<br />';
					} else {
						$documents .= '<ul style="list-style: none;">
						<li>服務: '.$doc->language_combination.'</li>
						<li>名稱: '.$fileName.'</li>
						<li>字數: '.$doc->word_count.' words</li>
						<li>費用: '.round($doc->doc_price).' NTD</li>
						<li>備註: '.$doc->notes.'</li>
						</ul>
						<br />';
					}
					$document_for_translators = '<ul style="list-style: none;">
					<li>Service: '.$doc->language_combination.'</li>
					<li><a href="https://docs.google.com/document/d/'.$filePath.'/edit">'.$fileName.'</a></li>
					<li>Word Count: '.$doc->word_count.' words</li>
					<li>Due Date/Time: '.$doc->translation_deadline.'</li>
					<li>Fee: '.$doc->translator_pay.' NTD</li>
					<li>Note: '.$doc->notes.'</li>
					</ul>
					<br />';
					
					$array = array(
						'{ document_for_translators }' => $document_for_translators,
						'{ dashboard }'=> url('translator/translator-bin'),
					);
					
					$translators = User::where([['user_type', 3], ['roles', 1], ['active', 1], ['language_combination', 'like', '%'.$doc->language_combination.'%']])->orWhere([['user_type', 3], ['roles', 3], ['active', 1], ['language_combination', 'like', '%'.$doc->language_combination.'%']])->get();
					unset($preferred_mail);
					unset($normal_mail);

					foreach($translators as $k => $v)
					{
						$email = $v->email;
						$language_combination = $doc->language_combination;
						if(!empty($lang[$language_combination]) && $v->id == $lang[$language_combination])
						{
							$preferred_mail[] = $email;
						}
						else
						{
							$normal_mail[] = $email;
						}
					}

					if(isset($preferred_mail))
					{
						$mail_template = EmailManagement::where('template_name', 'preferred_translator')->first();
						$content = strtr($mail_template->{'mail_body_'.$order->locale}, $array);
						$data = array(
							'content' => $content,
							'name' => $mail_template->sender_name,
							'subject' => $mail_template->{'mail_subject_'.$order->locale},
						);
						Mail::to(env('ADMIN_EMAIL'))->bcc($preferred_mail)->later(now()->addMinutes(0), new OrderComplete($data));
						$min = env('DELAY_MIN');
					}
					else
					$min = 0;
					
					if(isset($normal_mail))
					{
						$mail_template = EmailManagement::where('template_name', 'translator_notice')->first();
						$content = strtr($mail_template->{'mail_body_'.$order->locale}, $array);
						$data = array(
							'content' => $content,
							'name' => $mail_template->sender_name,
							'subject' => $mail_template->{'mail_subject_'.$order->locale},
						);
						Mail::to(env('ADMIN_EMAIL'))->bcc($normal_mail)->later(now()->addMinutes($min), new OrderComplete($data));
					}
				}
				
				$array = array(
					'{ documents }' => $documents,
					'{ quote_url }'=> url('new-order/quote/'.$order->order_number),
					'{ url }'=> url('new-order/order/'.$order->order_number),
					'{ order_date }'=> $order->order_date,
					'{ order_number }'=> $order->order_number,
					'{ order_price }'=> round($order->order_price),
					'{ total_price }'=> round($order->total_price),
					'{ currency }'=> 'NTD',
					'{ delivery_date }'=> $order->delivery_date,
					'{ deadline }'=> $order->deadline,
					'{ email }'=> $order->email,
				);
				
				$mail_template = EmailManagement::where('template_name', 'order_confirmation')->first();
				$content = strtr($mail_template->{'mail_body_'.$order->locale}, $array);
				
				$data = array(
					'content' => $content,
					'name' => $mail_template->sender_name,
					'subject' => $mail_template->{'mail_subject_'.$order->locale}.$order->order_number,
				);
				$min = 0;
				if (!empty($order->delivery_emails)) {
					$cc = array_map('trim', explode(',', $order->delivery_emails));
					Mail::to($order->email)->cc($cc)->later(now()->addMinutes($min), new OrderComplete($data));
				} else {
					Mail::to($order->email)->later(now()->addMinutes($min), new OrderComplete($data));
				}
				return redirect('new-order/order/'.$order->order_number);
			}
		}
		return redirect('new-order/failed');
	}
		
	public function receive(Request $request)
	{
		$return = (object) NewebPay::decode($request->input('TradeInfo'));
		if($return->Status == 'SUCCESS')
		{
			$order = Order::where('order_number', $return->Result['MerchantOrderNo'])->first();
			$order->payment_status = 1;
			$order->order_status = 3;
			if($order->save())
			{
				$user = User::where([['email', $order->email], ['user_type', 2]])->first();
				$comb = isset($user->language_combination) ? json_decode($user->language_combination) : null;
				$lang = [];
				if($comb)
				{
					foreach($comb as $k => $v)
					{
						if(isset($v->translator))
						$lang[$v->language] = $v->translator;
					}
				}
				
				$docs = json_decode($order->associated_docs);
				$documents = '';
				$document_for_translators = '';
				$fileName = '';
				$filePath = '';
				foreach($docs as $doc)
				{
					$idRef = strpos($doc->file, 'id');
					$idRef2 = strpos($doc->file, '&');
					$filePath = substr($doc->file, $idRef+3, $idRef2 - ($idRef+3));
					$fileName = $doc->filename;
					if ($order->locale === 'en') {
						$documents .= '<ul style="list-style: none;">
						<li>Service: '.$doc->language_combination.'</li>
						<li>Name: '.$fileName.'</li>
						<li>Word Count: '.$doc->word_count.' words</li>
						<li>Fee: '.round($doc->doc_price).' NTD</li>
						<li>Note: '.$doc->notes.'</li>
						</ul>
						<br />';
					} else {
						$documents .= '<ul style="list-style: none;">
						<li>服務: '.$doc->language_combination.'</li>
						<li>名稱: '.$fileName.'</li>
						<li>字數: '.$doc->word_count.' words</li>
						<li>費用: '.round($doc->doc_price).' NTD</li>
						<li>備註: '.$doc->notes.'</li>
						</ul>
						<br />';
					}
					$document_for_translators = '<ul style="list-style: none;">
					<li>Service: '.$doc->language_combination.'</li>
					<li><a href="https://docs.google.com/document/d/'.$filePath.'/edit">'.$fileName.'</a></li>
					<li>Word Count: '.$doc->word_count.' words</li>
					<li>Due Date/Time: '.$doc->translation_deadline.'</li>
					<li>Fee: '.$doc->translator_pay.' NTD</li>
					<li>Note: '.$doc->notes.'</li>
					</ul>
					<br />';
					
					$array = array(
						'{ document_for_translators }' => $document_for_translators,
						'{ dashboard }'=> url('translator/translator-bin'),
					);
					
					$translators = User::where([['user_type', 3], ['roles', 1], ['active', 1], ['language_combination', 'like', '%'.$doc->language_combination.'%']])->orWhere([['user_type', 3], ['roles', 3], ['active', 1], ['language_combination', 'like', '%'.$doc->language_combination.'%']])->get();
					unset($preferred_mail);
					unset($normal_mail);

					foreach($translators as $k => $v)
					{
						$email = $v->email;
						$language_combination = $doc->language_combination;
						if(!empty($lang[$language_combination]) && $v->id == $lang[$language_combination])
						{
							$preferred_mail[] = $email;
						}
						else
						{
							$normal_mail[] = $email;
						}
					}

					if(isset($preferred_mail))
					{
						$mail_template = EmailManagement::where('template_name', 'preferred_translator')->first();
						$content = strtr($mail_template->{'mail_body_'.$order->locale}, $array);
						$data = array(
							'content' => $content,
							'name' => $mail_template->sender_name,
							'subject' => $mail_template->{'mail_subject_'.$order->locale},
						);
						Mail::to(env('ADMIN_EMAIL'))->bcc($preferred_mail)->later(now()->addMinutes(0), new OrderComplete($data));
						$min = env('DELAY_MIN');
					}
					else
					$min = 0;
					
					if(isset($normal_mail))
					{
						$mail_template = EmailManagement::where('template_name', 'translator_notice')->first();
						$content = strtr($mail_template->{'mail_body_'.$order->locale}, $array);
						$data = array(
							'content' => $content,
							'name' => $mail_template->sender_name,
							'subject' => $mail_template->{'mail_subject_'.$order->locale},
						);
						Mail::to(env('ADMIN_EMAIL'))->bcc($normal_mail)->later(now()->addMinutes($min), new OrderComplete($data));
					}
				}
				
				$array = array(
					'{ documents }' => $documents,
					'{ quote_url }'=> url('new-order/quote/'.$order->order_number),
					'{ url }'=> url('new-order/order/'.$order->order_number),
					'{ order_date }'=> $order->order_date,
					'{ order_number }'=> $order->order_number,
					'{ order_price }'=> round($order->order_price),
					'{ total_price }'=> round($order->total_price),
					'{ currency }'=> 'NTD',
					'{ delivery_date }'=> $order->delivery_date,
					'{ deadline }'=> $order->deadline,
					'{ email }'=> $order->email,
				);
				
				$mail_template = EmailManagement::where('template_name', 'order_confirmation')->first();
				$content = strtr($mail_template->{'mail_body_'.$order->locale}, $array);
				
				$data = array(
					'content' => $content,
					'name' => $mail_template->sender_name,
					'subject' => $mail_template->{'mail_subject_'.$order->locale}.$order->order_number,
				);
				$min = 0;
				if (!empty($order->delivery_emails)) {
					$cc = array_map('trim', explode(',', $order->delivery_emails));
					Mail::to($order->email)->cc($cc)->later(now()->addMinutes($min), new OrderComplete($data));
				} else {
					Mail::to($order->email)->later(now()->addMinutes($min), new OrderComplete($data));
				}
				return 'SUCCESS';
			}
		}
		return 'Fail';
	}
		
	public function order_status(Request $request)
	{
		
		$order = Order::find($request->id);
		
		$data = [
			'status' => $order->payment_status
		];
		return response()->json($data);
	}
		
	function createOrder()
	{
		$str = file_get_contents("php://input");
		
		if (!$str) {
			$param = array_merge($_POST, $_GET);
			$str = json_encode($param, 320);
		} else {
			$param = json_decode($str, true);
			$param ? $param : parse_str($str, $param);
		}
		
		$user = User::where([['email', $param['email']], ['user_type', 2]])->first();
		if(!$user)
		{
			$user = new User;
			$user->name = $param['title'];
			$user->email = $param['email'];
			$user->user_type = 2;
			$user->local = 'en';
			$user->phone_number = $param['phone_no'];
			$user->save();
		} else {
			$user->local = isset($param['locale']) && $param['locale'] != '' ? $param['locale']: 'en';
			$user->phone_number = isset($param['phone_no']) && $param['phone_no'] != '' ? $param['phone_no']: null;
			$user->save();
		}
		
		$order_id = Order::insertGetId(array(
			'order_number'      => $param['order_number'],
			'email'      => $param['email'],
			'associated_docs'      => $param['associated_doc'],
			'order_price'      => $param['order_price'],
			'discount'      => $param['discount'],
			'total_price'      => $param['total_price'],
			'hours'      => $param['hours'],
			'deadline'      => date('Y-m-d H:i:s', strtotime($param['deadline'])),
			'payment_status'      => $param['payment_status'],
			'order_status'      => $param['order_status'],
			'urgent'      => $param['urgent'],
			'note'      => $param['note'],
			'delivery_date'      => date('Y-m-d H:i:s', strtotime($param['delivery_date'])), 
			'depreciation'      => $param['depreciation'],
			'overseas'      => $param['overseas'],
			'invoice_type'      => $param['invoice_type'],
			'title'      => $param['title'] ?? null,
			'carrier'      => isset($param['carrier']) && $param['carrier'] != '' ? $param['carrier']: null,
			'barcode'      => isset($param['barcode']) && $param['barcode'] != '' ? $param['barcode']: null,
			'serial_no'      => isset($param['serial_no']) && $param['serial_no'] != '' ? $param['serial_no']: null,
			'phone_no'      => isset($param['phone_no']) && $param['phone_no'] != '' ? $param['phone_no']: null,
			'tax'      => $param['tax'],
			'tax_amount' => $param['tax_amount'],
			'invoice_status'      => $param['invoice_status'],
			'local'      => $param['local'],
			'locale'      => $param['locale'],
			'delivery_emails'      => $param['delivery_emails'],
		));
		
		if($order_id){
			
			$order = Order::find($order_id);
			$data = [
				'id' => $order->id,
				'number' => $order->order_number,
			];
			return $data;
		}
	}
		
	public function change_payment(Request $request)
	{
		$order_number = $request->order_number;
		$type = $request->type;
		
		$order = Order::where('order_number', $order_number)->first();
		if($type == 1)
		{
			$order->discount = round($order->order_price * 0.05);
		}
		else
		{
			$order->discount = 0;
		}
		$order->total_price = round(($order->order_price - $order->discount)*1.05);
		if($order->tax == 1)
		$order->tax_amount = round($order->order_price / 1.05);
		if($order->tax == 0)
		$order->tax_amount = round($order->order_price* 0.05);
		$order->save();
		
		$data = [
			'discount' => $order->discount.' NTD',
			'total' => $order->total_price.' NTD',
		];
		return response()->json($data);
	}
		
	public function change_note(Request $request)
	{
		$order_number = $request->order_number;
		$note = $request->note;
		$fileid = $request->fileid;
		
		$order = Order::where('order_number', $order_number)->first();
		$docs = json_decode($order->associated_docs);
		$data = [];
		foreach($docs as $k => $v)
		{
			
			if($v->file_id == $fileid)
			{
				$v->notes = $note;
			}
			$data[] = $v;
		}
		$order->associated_docs = json_encode($data);
		$order->save();
		
		$data = [
			'note' => $note,
		];
		return response()->json($data);
	}
		
	public function do_notice(Request $request)
	{
		$order_number = $request->order_number;
		$order = Order::where('order_number', $order_number)->first();
		$order->payment_type = 2;
		$order->order_status = 3;
		$order->save();
		
		$user = User::where([['email', $order->email], ['user_type', 2]])->first();
		$comb = json_decode($user->language_combination);
		$lang = [];
		if($comb)
		{
			foreach($comb as $k => $v)
			{
				if(isset($v->translator))
				$lang[$v->language] = $v->translator;
			}
		}
		
		$docs = json_decode($order->associated_docs);
		$documents = '';
		$document_for_translators = '';
		$fileName = '';
		$filePath = '';
		foreach($docs as $doc)
		{
			$idRef = strpos($doc->file, 'id');
			$idRef2 = strpos($doc->file, '&');
			$filePath = substr($doc->file, $idRef+3, $idRef2 - ($idRef+3));
			$fileName = $doc->filename;
			if ($order->locale === 'en') {
				$documents .= '<ul style="list-style: none;">
				<li>Service: '.$doc->language_combination.'</li>
				<li>Name: '.$fileName.'</li>
				<li>Word Count: '.$doc->word_count.' words</li>
				<li>Fee: '.round($doc->doc_price).' NTD</li>
				<li>Note: '.$doc->notes.'</li>
				</ul>
				<br />';
			} else {
				$documents .= '<ul style="list-style: none;">
				<li>服務: '.$doc->language_combination.'</li>
				<li>名稱: '.$fileName.'</li>
				<li>字數: '.$doc->word_count.' words</li>
				<li>費用: '.round($doc->doc_price).' NTD</li>
				<li>備註: '.$doc->notes.'</li>
				</ul>
				<br />';
			}
			$document_for_translators = '<ul style="list-style: none;">
			<li>Service: '.$doc->language_combination.'</li>
			<li><a href="https://docs.google.com/document/d/'.$filePath.'/edit">'.$fileName.'</a></li>
			<li>Word Count: '.$doc->word_count.' words</li>
			<li>Due Date/Time: '.$doc->translation_deadline.'</li>
			<li>Fee: '.$doc->translator_pay.' NTD</li>
			<li>Note: '.$doc->notes.'</li>
			</ul>
			<br />';
					
			$array = array(
				'{ document_for_translators }' => $document_for_translators,
				'{ dashboard }'=> url('translator/translator-bin'),
			);

			$translators = User::where([['user_type', 3], ['roles', 1], ['active', 1], ['language_combination', 'like', '%'.$doc->language_combination.'%']])->orWhere([['user_type', 3], ['roles', 3], ['active', 1], ['language_combination', 'like', '%'.$doc->language_combination.'%']])->get();
			unset($preferred_mail);
			unset($normal_mail);

			foreach($translators as $k => $v)
			{
				$email = $v->email;
				$language_combination = $doc->language_combination;
				if(!empty($lang[$language_combination]) && $v->id == $lang[$language_combination])
				{
					$preferred_mail[] = $email;
				}
				else
				{
					$normal_mail[] = $email;
				}
			}

			if(isset($preferred_mail))
			{
				$mail_template = EmailManagement::where('template_name', 'preferred_translator')->first();
				$content = strtr($mail_template->{'mail_body_'.$order->locale}, $array);
				$data = array(
					'content' => $content,
					'name' => $mail_template->sender_name,
					'subject' => $mail_template->{'mail_subject_'.$order->locale},
				);
				Mail::to(env('ADMIN_EMAIL'))->bcc($preferred_mail)->later(now()->addMinutes(0), new OrderComplete($data));
				$min = env('DELAY_MIN');
			}
			else
			$min = 0;
			
			if(isset($normal_mail))
			{
				$mail_template = EmailManagement::where('template_name', 'translator_notice')->first();
				$content = strtr($mail_template->{'mail_body_'.$order->locale}, $array);
				$data = array(
					'content' => $content,
					'name' => $mail_template->sender_name,
					'subject' => $mail_template->{'mail_subject_'.$order->locale},
				);
				Mail::to(env('ADMIN_EMAIL'))->bcc($normal_mail)->later(now()->addMinutes($min), new OrderComplete($data));
			}
		}
		
		$array = array(
			'{ documents }' => $documents,
			'{ quote_url }'=> url('new-order/quote/'.$order->order_number),
			'{ url }'=> url('new-order/order/'.$order->order_number),
			'{ order_date }'=> $order->order_date,
			'{ order_number }'=> $order->order_number,
			'{ order_price }'=> round($order->order_price),
			'{ total_price }'=> round($order->total_price),
			'{ currency }'=> 'NTD',
			'{ delivery_date }'=> $order->delivery_date,
			'{ deadline }'=> $order->deadline,
			'{ email }'=> $order->email,
		);
		$mail_template = EmailManagement::where('template_name', 'order_confirmation')->first();
		$content = strtr($mail_template->{'mail_body_'.$order->locale}, $array);
		
		$data = array(
			'content' => $content,
			'name' => $mail_template->sender_name,
			'subject' => $mail_template->{'mail_subject_'.$order->locale}.$order->order_number,
		);
		$min = 0;
		if (!empty($order->delivery_emails)) {
			$cc = array_map('trim', explode(',', $order->delivery_emails));
			Mail::to($order->email)->cc($cc)->later(now()->addMinutes($min), new OrderComplete($data));
		} else {
			Mail::to($order->email)->later(now()->addMinutes($min), new OrderComplete($data));
		}
		$data = [
			'message' => 'success',
		];
		return response()->json($data);
	}
		
	function updateOrder()
	{
		$str = file_get_contents("php://input");
		
		if (!$str) {
			$param = array_merge($_POST, $_GET);
			$str = json_encode($param, 320);
		} else {
			$param = json_decode($str, true);
			$param ? $param : parse_str($str, $param);
		}
		
		$order = Order::find($param['id']);
		unset($param['id']);
		foreach($param as $k => $v)
		{
			$order->$k = $v;
		}
		$order->save();
	}
		
	function paidOrder()
	{
		$str = file_get_contents("php://input");
		
		if (!$str) {
			$param = array_merge($_POST, $_GET);
			$str = json_encode($param, 320);
		} else {
			$param = json_decode($str, true);
			$param ? $param : parse_str($str, $param);
		}
		
		$order = Order::find($param['id']);
		unset($param['id']);
		foreach($param as $k => $v)
		{
			$order->$k = $v;
		}
		if($order->save())
		{
			$user = User::where([['email', $order->email], ['user_type', 2]])->first();
			$comb = json_decode($user->language_combination);
			$lang = [];
			if($comb)
			{
				foreach($comb as $k => $v)
				{
					if(isset($v->translator))
					$lang[$v->language] = $v->translator;
				}
			}
			
			$docs = json_decode($order->associated_docs);
			$documents = '';
			$document_for_translators = '';
			$fileName = '';
			$filePath = '';
			foreach($docs as $doc)
			{
				$idRef = strpos($doc->file, 'id');
				$idRef2 = strpos($doc->file, '&');
				$filePath = substr($doc->file, $idRef+3, $idRef2 - ($idRef+3));
				$fileName = $doc->filename;
				if ($order->locale === 'en') {
					$documents .= '<ul style="list-style: none;">
					<li>Service: '.$doc->language_combination.'</li>
					<li>Name: '.$fileName.'</li>
					<li>Word Count: '.$doc->word_count.' words</li>
					<li>Fee: '.$doc->doc_price.' USD</li>
					<li>Note: '.$doc->notes.'</li>
					</ul>
					<br />';
				} else {
					$documents .= '<ul style="list-style: none;">
					<li>服務: '.$doc->language_combination.'</li>
					<li>名稱: '.$fileName.'</li>
					<li>字數: '.$doc->word_count.' words</li>
					<li>費用: '.$doc->doc_price.' USD</li>
					<li>備註: '.$doc->notes.'</li>
					</ul>
					<br />';
				}
				$document_for_translators = '<ul style="list-style: none;">
				<li>Service: '.$doc->language_combination.'</li>
				<li><a href="https://docs.google.com/document/d/'.$filePath.'/edit">'.$fileName.'</a></li>
				<li>Word Count: '.$doc->word_count.' words</li>
				<li>Due Date/Time: '.$doc->translation_deadline.'</li>
				<li>Fee: '.$doc->translator_pay.' NTD</li>
				<li>Note: '.$doc->notes.'</li>
				</ul>
				<br />';
					
				$array = array(
					'{ document_for_translators }' => $document_for_translators,
					'{ dashboard }'=> url('translator/translator-bin'),
				);
					
				$translators = User::where([['user_type', 3], ['roles', 1], ['active', 1], ['language_combination', 'like', '%'.$doc->language_combination.'%']])->orWhere([['user_type', 3], ['roles', 3], ['active', 1], ['language_combination', 'like', '%'.$doc->language_combination.'%']])->get();
				unset($preferred_mail);
				unset($normal_mail);

				foreach($translators as $k => $v)
				{
					$email = $v->email;
					$language_combination = $doc->language_combination;
					if(!empty($lang[$language_combination]) && $v->id == $lang[$language_combination])
					{
						$preferred_mail[] = $email;
					}
					else
					{
						$normal_mail[] = $email;
					}
				}

				if(isset($preferred_mail))
				{
					$mail_template = EmailManagement::where('template_name', 'preferred_translator')->first();
					$content = strtr($mail_template->{'mail_body_'.$order->locale}, $array);
					$data = array(
						'content' => $content,
						'name' => $mail_template->sender_name,
						'subject' => $mail_template->{'mail_subject_'.$order->locale},
					);
					Mail::to(env('ADMIN_EMAIL'))->bcc($preferred_mail)->later(now()->addMinutes(0), new OrderComplete($data));
					$min = env('DELAY_MIN');
				}
				else
				$min = 0;
				
				if(isset($normal_mail))
				{
					$mail_template = EmailManagement::where('template_name', 'translator_notice')->first();
					$content = strtr($mail_template->{'mail_body_'.$order->locale}, $array);
					$data = array(
						'content' => $content,
						'name' => $mail_template->sender_name,
						'subject' => $mail_template->{'mail_subject_'.$order->locale},
					);
					Mail::to(env('ADMIN_EMAIL'))->bcc($normal_mail)->later(now()->addMinutes($min), new OrderComplete($data));
				}
			}
			
			$array = array(
				'{ documents }' => $documents,
				'{ quote_url }'=> url('new-order/quote/'.$order->order_number),
				'{ url }'=> url('new-order/order/'.$order->order_number),
				'{ order_date }'=> $order->order_date,
				'{ order_number }'=> $order->order_number,
				'{ order_price }'=> $order->order_price,
				'{ total_price }'=> $order->total_price,
				'{ currency }'=> 'USD',
				'{ delivery_date }'=> $order->delivery_date,
				'{ deadline }'=> $order->deadline,
				'{ email }'=> $order->email,
			);
			$mail_template = EmailManagement::where('template_name', 'order_confirmation')->first();
			$content = strtr($mail_template->{'mail_body_'.$order->locale}, $array);
			
			$data = array(
				'content' => $content,
				'name' => $mail_template->sender_name,
				'subject' => $mail_template->{'mail_subject_'.$order->locale}.$order->order_number,
			);
			$min = 0;
			if (!empty($order->delivery_emails)) {
				$cc = array_map('trim', explode(',', $order->delivery_emails));
				Mail::to($order->email)->cc($cc)->later(now()->addMinutes($min), new OrderComplete($data));
			} else {
				Mail::to($order->email)->later(now()->addMinutes($min), new OrderComplete($data));
			}
			return redirect('new-order/order/'.$order->order_number);
		}
	}
		
	function sendSmsCode(Request $request)
	{
		$phoneNumber = $request['phoneNumber'];
		$randomCode = str_pad(mt_rand(0, 999999),6,'0',STR_PAD_BOTH);
		// Session::put('captcha', $randomCode);
		$message = '一元翻譯手機認證碼：'.$randomCode;
		// APIController::sendSms($phoneNumber, $message);
		
		$curl = curl_init();
		// url
		$url = 'https://smsapi.mitake.com.tw/api/mtk/SmSend?'; 
		$url .= 'CharsetURL=UTF-8';
		// parameters
		$data = 'username=24698317SMS'; // need to move to env
		$data .= '&password=tedlikesweed'; // need to move to env
		$data .= '&dstaddr='.$phoneNumber; 
		$data .= '&smbody='.$message;
		// 設定curl網址
		curl_setopt($curl, CURLOPT_URL, $url);
		// 設定Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/x-www-form-urlencoded") );
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data); 
		curl_setopt($curl, CURLOPT_HEADER,0);
		// 執行
		curl_exec($curl);
		curl_close($curl);
		// echo $output;
		
		return $randomCode; 
	}
}