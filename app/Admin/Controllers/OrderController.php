<?php

namespace App\Admin\Controllers;

use App\Models\Order;
use App\Models\LanguageCombo;
use App\Models\SystemParameter;
use App\Models\EmailManagement;
use App\Models\Doc;
use App\Models\User;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Storage;
use Symfony\Component\HttpFoundation\Response;
use Dcat\Admin\Admin;
use Mail;
use App\Mail\NewOrder;
use App\Admin\Actions\Grid\SendInvoice;
use Dcat\Admin\Widgets\Table;
use Dcat\Admin\Widgets\Card;
use ZipArchive;
use App\Mail\OrderComplete;

class OrderController extends AdminController
{
	/**
	* Make a grid builder.
	*
	* @return Grid
	*/
	protected function grid()
	{
		Admin::js('/packages/deliver/deliver.js');
		return Grid::make(new Order(), function (Grid $grid) {
			$grid->model()->orderBy('order_date', 'desc');
			$grid->column('order_date')
			->display(function ($order_date) {
				$content = date('Y-m-d H:i', strtotime($order_date));
				return $content;
			})
			->sortable();
			$grid->column('order_number')
			->link(function ($order_number){
				return '/new-order/order/'.$order_number;
			});
			$grid->selector(function (Grid\Tools\Selector $selector) {
				$selector->select('invoice_status', 'Invoice-Status', [
					0 => 'Unsent',
					1 => 'Sent',
				]);
				$selector->select('payment_status', 'Payment-Status', [
					0 => 'Not Paid',
				]);
			});
			$grid->column('email', 'Client email');
			$grid->column('associated_docs', 'Docs')
			->display('more')
			->expand(function () {
				$file_list = [];
				if($this->associated_docs)
				{
					$file = '';
					$list = json_decode($this->associated_docs);
					foreach($list as $k => $v)
					{
						if($v){
							$idRef = strpos($v->file, 'id');
							$idRef2 = strpos($v->file, '&');
							$fileId = substr($v->file, $idRef+3, $idRef2 - ($idRef+3));
							$filename = (mb_strlen($v->filename) > 20) ? mb_substr($v->filename, 0, 20, 'UTF-8').'...' : $v->filename;
							$file = '<a href="https://docs.google.com/document/d/'.$fileId.'/edit" target="_blank">'.$filename.'</a><br>';
						}
						$translator = isset($v->translator) ? User::find($v->translator)->name : '';
						$translator_delive = '';
						if($translator)
						$translator_delive = $v->translator_deliver_date ?? '<input type="button" class="grid-column-switch" id="'.$this->id.'_trans_'.$v->translator.'" onclick="trans_deliver('.$this->id.', \''.$v->id.'\')" value="Send">';
						$editor = isset($v->editor) ? User::find($v->editor)->name : '';
						$editor_delive = '';
						if($editor)
						$editor_delive = $v->editor_deliver_date ?? '<input type="button" class="grid-column-switch" id="'.$this->id.'_edit_'.$v->translator.'" onclick="editor_deliver('.$this->id.', \''.$v->id.'\')" value="Send">';
						
						$file_list[] =  [
							$file,
							$v->language_combination,
							$translator,
							$translator_delive,
							$editor,
							$editor_delive,
							$v->word_count,
							// $v->translator_fee,
							// $v->editor_fee,
							$v->translation_deadline,
							$v->doc_deadline,
						];
					}
				}
				// if(!$this->service_type)
				$file_list = Table::make(['File', 'Language combination', 'Translator', 'Finished?', 'Editor', 'Finished?', 'Word Count', 'Translation Deadline', 'Doc Deadline'], $file_list);
				// else
				//     $file_list = Table::make(['File', 'Language combination', 'Translator', 'Deliver', 'Word_count', 'Fee'], $file_list);
				
				$content = '<table class="table">
				<tr>
				<td>'. $file_list.'</td>
				</tr>
				</table>';
				$card = new Card($content);
				return $card;
			});
			$grid->column('overseas', 'Currency')
			->if(function () {
				return $this->overseas;
			})
			->then(function (Grid\Column $column) {
				$column->display('USD');
			})
			->else(function (Grid\Column $column) {
				$column->display('NTD');
			});
			$grid->column('total_price', 'Price (inc. tax)');
			$grid->column('payment_status')->using([0 => 'Unpaid', 1 => 'Paid']);
			$grid->column('order_status')->using([0 => 'Pending confirmation', 3 => 'Accepted case', 4 => 'Partial completion', 5 => 'Order completion', 6 => 'Cancel']);
			$grid->column('deadline', 'Order Deadline')
			->display(function ($deadline) {
				$content = date('Y-m-d H:i', strtotime($deadline));
				return $content;
			});
			$grid->disableViewButton();
			$grid->disableCreateButton();
			$grid->disableDeleteButton();
			$grid->enableDialogCreate();
			$grid->setDialogFormDimensions('60%', '95%');
			$grid->filter(function (Grid\Filter $filter) {
				$filter->equal('email');
				$filter->equal('title');
			});
		});
	}
	
	/**
	* Make a form builder.
	*
	* @return Form
	*/
	protected function form()
	{
		Admin::script(
			<<<JS
				var currency = '';

				$(document).ready(function() {
					$('.has-many-associated_docs .add').hide();
					$('.has-many-associated_docs .remove').hide();
					$('.field_currency').parent().closest('.form-group').hide();

					currency = $('.field_currency').val();
					
					$('.field_word_count').on('input', function() {
						var newWordCount = $(this).val();
						var serviceRate = $(this).parent().closest('.box-body').find('.field_service_rate').val();
						var docPriceField = $(this).parent().closest('.box-body').find('.field_doc_price');
						var newDocPrice = 0;

						if (currency === 'NTD') {
							newDocPrice = Math.round(serviceRate * newWordCount);
						} else {
							newDocPrice = serviceRate * newWordCount;
						}

						docPriceField.val(newDocPrice);
						calcOrderPrice();
					});

					$('.field_service_rate').on('input', function() {
						var wordCount = $(this).parent().closest('.box-body').find('.field_word_count').val();
						var newServiceRate = $(this).val();
						var docPriceField = $(this).parent().closest('.box-body').find('.field_doc_price');
						var newDocPrice = 0;

						if (currency === 'NTD') {
							newDocPrice = Math.round(newServiceRate * wordCount);
						} else {
							newDocPrice = newServiceRate * wordCount;
						}

						docPriceField.val(newDocPrice);
						calcOrderPrice();
					});

					$('.field_doc_price').on('input', function() {
						calcOrderPrice();
					})

					$('.field_translator_pay_rate').on('input', function() {
						var wordCount = $(this).parent().closest('.box-body').find('.field_word_count').val();
						var newTranslatorPayRate = $(this).parent().closest('.box-body').find('.field_translator_pay_rate').val();
						var translatorPayField = $(this).parent().closest('.box-body').find('.field_translator_pay');
						var newTranslatorPay = Math.round(newTranslatorPayRate * wordCount);

						translatorPayField.val(newTranslatorPay);
					});

					$('.field_editor_pay_rate').on('input', function() {
						var wordCount = $(this).parent().closest('.box-body').find('.field_word_count').val();
						var newEditorPayRate = $(this).parent().closest('.box-body').find('.field_editor_pay_rate').val();
						var editorPayField = $(this).parent().closest('.box-body').find('.field_editor_pay');
						var newEditorPay = Math.round(newEditorPayRate * wordCount);

						editorPayField.val(newEditorPay);
					});

					$('input[name="order_price"]').on('input', function() {
						calcTax();
						calcTotalPrice();
					});

					$('input[type="radio"][name="tax"]').on('change', function() {
						calcTotalPrice();
					});

					$('input[type="radio"][name="local"]').on('change', function() {
						var local = $('input[type="radio"][name="local"]:checked').val();
						currency = local == 1 ? 'NTD' : 'USD';
					});
				});

				function calcOrderPrice() {
					var orderPrice = 0;
					$.each($('.field_doc_price'), function() {
						orderPrice += parseFloat($(this).val());
					});
					$('input[name="order_price"]').val(orderPrice);

					calcTax();
				}

				function calcTax() {
					var orderPrice = parseFloat($('input[name="order_price"]').val());
					var tax = orderPrice * 0.05;
					currency === 'NTD' ? tax = Math.round(tax) : tax.toFixed(2);
					
					$('input[name="tax_amount"]').val(tax);
					calcTotalPrice();
				}

				function calcTotalPrice() {
					var orderPrice = parseFloat($('input[name="order_price"]').val());
					var tax = parseFloat($('input[name="tax_amount"]').val());
					var taxType = $('input[type="radio"][name="tax"]:checked').val();
					taxType == 0 ? $('input[name="total_price"]').val(orderPrice) : $('input[name="total_price"]').val(orderPrice + tax);
				}
			JS
		);
		
		return Form::make(new Order(), function (Form $form) {
			if ($form->getKey()) {
				$order = Order::find($form->getKey());
				$form->hidden('order_number');
			} else {
				$order = new Order;
				$form->hidden('order_number');
			}
			$form->hidden('id');
			$form->hidden('order_date');
			$form->email('email', 'Client email');
			$form->radio('notice', 'Send complete doc to')->options([1 => 'Client', 2 => 'Admin'])->default(1);
			$form->radio('autosend_invoice')->options([0 => 'No', 1 => 'Yes']);
			$form->array('associated_docs', function ($form) {
				Admin::script(
					<<<JS
						$(document).ready(function() {
						});
					JS
				);
				$form->text('filename');
				$form->text('file', 'Google Doc Link (Do not edit)');
				$form->select('language_combination')->options(function () {
					return LanguageCombo::all()->pluck('name', 'code');
				})->load(['translator', 'editor'], ['/order/get-translator-by-language-combo', '/order/get-editor-by-language-combo']);
				$form->radio('service_type')->options([0 => 'Translation Only', 1 => 'Translation & Editing'])->default(1);
				$expertise = array(
					'no_need' => 'No expert needed',
					'art' => 'Art and Culture',
					'bussiness' => 'Business General',
					'ad' => 'Ad-Words / Banners',
					'car' => 'Automotive / Aerospace',
					'cv' => 'CV',
					'certificates' => 'Certificates Translation',
					'finance' => 'Forex / Finance',
					'game' => 'Gaming / Video Games',
					'legal' => 'Legal',
					'marketing' => 'Marketing / Consumer/ Media',
					'medical' => 'Medical',
					'mobile' => 'Mobile Applications',
					'patents' => 'Patents',
					'scientific' => 'Scientific / Academic',
					'it' => 'Software / IT',
					'technical' => 'Technical / Engineering',
					'tourism' => 'Tourism',
				);
				$form->select('expertise')->options($expertise)->default('no_need');
				$style = array(
					1 => 'Please perform the translation in a direct manner that accurately conveys the meaning of the source text',
					2 => 'Please use "free style" translation so it sounds good in the target language',
					3 => 'Time is of the essence, please deliver the translation as soon as you can'
				);
				$form->select('style')->options($style)->default(1);
				$form->decimal('word_count');
				$form->decimal('service_rate');
				$form->decimal('doc_price');
				$form->display('currency');
				$form->decimal('translator_pay_rate');
				$form->decimal('translator_pay');
				$form->decimal('editor_pay_rate');
				$form->decimal('editor_pay');
				$form->select('translator');
				$form->switch('translator_finish', 'Translator finished? (Set translator required)');
				$form->select('editor');
				$form->switch('editor_finish', 'Editor finished? (Set editor required)');
				$form->hidden('id');
				$form->hidden('file_id');
				$form->hidden('translator_permissions_id');
				$form->hidden('translator_deliver_date');
				$form->hidden('editor_deliver_date');
				$form->textarea('notes');
				$form->datetime('translation_deadline')->rules('required|date');
				$form->datetime('doc_deadline', 'Editing deadline')->rules('required|date');
			})->saving(function ($arr) use ($order) {
				$data = [];
				foreach($arr as $v) {
					$combo = LanguageCombo::where('code', $v['language_combination'])->first();
					$translator_deliver_date = $v['translator_deliver_date'] ?: null;
					$editor_deliver_date = $v['editor_deliver_date'] ?: null;
					$translator_name = '';
					$designation_link = '';
					if (!empty($v['translator'])) {
						$translator = USER::find($v['translator']);
						$translator_name = $translator->name;
						$designation_link = url('designation/translator/'.$v['language_combination'].'/'.$order->client->id.'/'.$translator->id);
					}
					if (!empty($v['editor'])) {
						$editor = USER::find($v['editor']);
					}
					$idRef = strpos($v['file'], 'id');
					$idRef2 = strpos($v['file'], '&');
					$filePath = substr($v['file'], $idRef+3, $idRef2 - ($idRef+3));
					$data[] = array(
						'id' => $v['id'] ?? uniqid(),
						'service_type' => $v['service_type'],
						'language_combination' => $v['language_combination'],
						'translator' => $v['translator'],
						'translator_deliver_date' => $translator_deliver_date,
						'editor' => $v['editor'],
						'editor_deliver_date' => $editor_deliver_date,
						'filename' => $v['filename'],
						'file' => $v['file'],
						'file_id' => $filePath,
						'word_count' => $v['word_count'],
						'service_rate' => $v['service_rate'],
						'doc_price' => $v['doc_price'],
						'translator_pay' => $v['translator_pay'],
						'editor_pay' => $v['editor_pay'],
						'translator_pay_rate' => $v['translator_pay_rate'] != $combo->translator_pay_rate ? $v['translator_pay_rate'] : $combo->translator_pay_rate,
						'editor_pay_rate' => $v['editor_pay_rate'] != $combo->editor_pay_rate ? $v['editor_pay_rate'] : $combo->editor_pay_rate,
						'expertise' => $v['expertise'],
						'style' => $v['style'],
						'notes' => $v['notes'],
						'translation_deadline' => $v['translation_deadline'],
						'doc_deadline' => $v['doc_deadline'],
						'translator_permissions_id' => $v['translator_permissions_id'],
						'translator_finish' => $v['translator_finish'],
						'editor_finish' => $v['editor_finish'],
					);
					$fileName = $v['filename'];

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
					
					if ($v['translator_finish'] && $v['service_type'] == 0) {
						$mail_template = EmailManagement::where('template_name', 'order_deliver')->first();
						$email = $order->notice == 1 ? $order->email : env('ADMIN_EMAIL');
						$total_price = $order->total_price;
						$array = array(
							'email' => $email,
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
						// Mail::to($email)->later(now()->addMinutes(0), new OrderComplete($email_data));
					}
					if ($v['editor_finish']) {
						$mail_template = EmailManagement::where('template_name', 'order_deliver')->first();
						$email = $order->notice == 1 ? $order->email : env('ADMIN_EMAIL');
						$total_price = $order->total_price;
						$array = array(
							'email' => $email,
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
						// Mail::to($email)->later(now()->addMinutes(0), new OrderComplete($email_data));
					}
				}
				return json_encode($data);
			});
			$form->decimal('order_price');
			$form->decimal('tax_amount', 'VAT')->disable();
			$form->radio('tax')->options([0 => 'Included in order price', 1 => 'Excluded from order price'])->default(0);
			$form->decimal('total_price');
			$form->radio('overseas')->options([0 => 'Local', 1 => 'Overseas'])->when(0, function(Form $form) {
				$form->radio('invoice_type')->options([0 => 'Duplex', 1 => 'Triplex'])->default(0)
				->when(0, function(Form $form){
					$form->radio('carrier')->options([1 => 'Member Carrier', 2 => 'Mobile Carrier'])->default(1)
						->when(1, function(Form $form){
							$form->text('title', 'Name of Client');
						})
						->when(2, function(Form $form) {
							$form->text('title', 'Name of Client');
							$form->text('barcode');
						});
				})
				->when(1, function(Form $form){
					$form->text('title', 'Name of Client');
					$form->text('serial_no', 'Tax ID No.')->minLength(8)->maxLength(8);
				});
			})->disable();
			$form->radio('invoice_status')->options([0 => 'Unsent', 1 => 'Sent'])->default(0);
			$form->radio('payment_status')->options([0 => 'Unpaid', 1 => 'Paid'])->default(0);
			$form->radio('order_status')->options([0 => 'Pending confirmation', 3 => 'Accepted case', 4 => 'Partial completion', 5 => 'Order completion', 6 => 'Cancel']);
			$form->datetime('deadline', 'Order deadline');
			$form->hidden('admin_id');
			$form->disableViewButton();
			$form->disableResetButton();
			$form->disableViewCheck();
			$form->disableEditingCheck();
			$form->saving(function (Form $form) use ($order) {
				if (!$form->getKey()) {
					$form->order_number = date('Ymd').str_pad(rand(0, 99999),5,'0',STR_PAD_LEFT);
				}
				$user = User::firstOrNew(['email' => $form->email, 'user_type' => 2]);
				$user->name = $form->title;
				$user->save();
				$form->admin_id = Admin::user()->getKey();
				if ($form->order_status != 0) {
					$comb = isset($user->language_combination) ? json_decode($user->language_combination) : null;
					$lang = [];
					if ($comb) {
						foreach ($comb as $k => $v) {
							if (isset($v->translator))
							$lang[$v->language] = $v->translator;
						}
					}
					$documents = '';
					$documents_order_finish = '';
					$document_for_translators = '';
					$filePath = '';

					foreach ($form->associated_docs as $doc) {
						$doc = (object)$doc;
						$idRef = strpos($doc->file, 'id');
						$idRef2 = strpos($doc->file, '&');
						$filePath = substr($doc->file, $idRef+3, $idRef2 - ($idRef+3));
						$translator_fee = $doc->translator_pay;
						if ($order->locale === 'en') {
							$documents .= '<ul style="list-style: none;">
								<li>Service: '.$doc->language_combination.'</li>
								<li>Name: '.$doc->filename.'</li>
								<li>Word Count: '.$doc->word_count.' words</li>
								<li>Fee: '.round($doc->doc_price).' NTD</li>
								<li>Note: '.$doc->notes.'</li>
							</ul>
							<br />';
							$documents_order_finish .= '<li>
								<p>Document: 【'.$doc->filename.'】</p>
								<p>Download link: <a href="https://docs.google.com/document/d/'.$filePath.'/export?format=docx">'.$doc->filename.'</a></p><br/>
							</li>';
						} else {
							$documents .= '<ul style="list-style: none;">
								<li>服務: '.$doc->language_combination.'</li>
								<li>名稱: '.$doc->filename.'</li>
								<li>字數: '.$doc->word_count.' words</li>
								<li>費用: '.round($doc->doc_price).' NTD</li>
								<li>備註: '.$doc->notes.'</li>
							</ul>
							<br />';
							$documents_order_finish .= '<li>
								<p>文件：【'.$doc->filename.'】</p>
								<p>下載連結：<a href="https://docs.google.com/document/d/'.$filePath.'/export?format=docx">'.$doc->filename.'</a></p><br/>
							</li>';
						}
						$document_for_translators = '<ul style="list-style: none;">
							<li>Service: '.$doc->language_combination.'</li>
							<li><a href="https://docs.google.com/document/d/'.$filePath.'/edit">'.$doc->filename.'</a></li>
							<li>Word Count: '.$doc->word_count.' words</li>
							<li>Due Date/Time: '.$doc->doc_deadline.'</li>
							<li>Estimated Time for Translation: '.$order->hours.' Hour</li>
							<li>Fee: '.$translator_fee.' NTD</li>
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

						foreach ($translators as $k => $v) {
							$email = $v->email;
							$language_combination = $doc->language_combination;
							if (!empty($lang[$language_combination]) && $v->id == $lang[$language_combination]) {
								$preferred_mail[] = $email;
							} else {
								$normal_mail[] = $email;
							}
						}
						
						if (isset($preferred_mail)) {
							$mail_template = EmailManagement::where('template_name', 'preferred_translator')->first();
							$content = strtr($mail_template->{'mail_body_'.$order->locale}, $array);
							$data = array(
								'content' => $content,
								'name' => $mail_template->sender_name,
								'subject' => $mail_template->{'mail_subject_'.$order->locale},
							);
							// Mail::to(env('ADMIN_EMAIL'))->bcc($preferred_mail)->later(now()->addMinutes(0), new OrderComplete($data));
							$min = env('DELAY_MIN');
						}
						else
						$min = 0;
						
						if (isset($normal_mail)) {
							$mail_template = EmailManagement::where('template_name', 'translator_notice')->first();
							$content = strtr($mail_template->{'mail_body_'.$order->locale}, $array);
							$data = array(
								'content' => $content,
								'name' => $mail_template->sender_name,
								'subject' => $mail_template->{'mail_subject_'.$order->locale},
							);
							// Mail::to(env('ADMIN_EMAIL'))->bcc($normal_mail)->later(now()->addMinutes($min), new OrderComplete($data));
						}
					}
							
					$array = array(
						'{ documents }' => $documents,
						'{ quote_url }'=> url('new-order/quote/'.$form->order_number),
						'{ url }'=> url('new-order/order/'.$form->order_number),
						'{ order_date }'=> $form->order_date,
						'{ order_number }'=> $form->order_number,
						'{ order_price }'=> $form->order_price,
						'{ total_price }'=> $form->total_price,
						'{ delivery_date }'=> $form->delivery_date,
						'{ deadline }'=> $form->deadline,
						'{ email }'=> $form->email,
						'{ bankcode }'=> env('BANK_CODE'),
						'{ codeNo }'=> env('CODE_NO'),
						'{ currency }'=> $order->overseas ? 'USD' : 'NTD'
					);
							
					$array_order_finish = array(
						'{ documents }' => $documents_order_finish,
						'{ total_price }'=> $form->total_price,
						'{ currency }'=> $order->overseas ? 'USD' : 'NTD'
					);
							
					$order_status = $form->model()->order_status;
					if ($form->order_status == 3 && $order_status != 3) {
						$mail_template = EmailManagement::where('template_name', 'order_confirmation')->first();
						$email = $form->notice == 1 ? $form->email : env('ADMIN_EMAIL');
						$content = strtr($mail_template->{'mail_body_'.$order->locale}, $array);
						$email_data = array(
							'content' => $content,
							'name' => $mail_template->sender_name,
							'subject' => $mail_template->{'mail_subject_'.$order->locale}.$form->order_number,
						);
						Mail::to($email)->later(now()->addMinutes(0), new OrderComplete($email_data));
					}
					if ($form->order_status == 5 && $order_status != 5) {
						$mail_template = EmailManagement::where('template_name', 'order_finish')->first();
						$email = $form->notice == 1 ? $form->email : env('ADMIN_EMAIL');
						$content = strtr($mail_template->{'mail_body_'.$order->locale}, $array_order_finish);
						$email_data = array(
							'content' => $content,
							'name' => $mail_template->sender_name,
							'subject' => $mail_template->{'mail_subject_'.$order->locale},
						);
						Mail::to($email)->later(now()->addMinutes(0), new OrderComplete($email_data));
					}
				}
				$form->overseas = $order->overseas;
			});
		});
	}
		
	public function getLanguageComboRate($id){
		return LanguageCombo::where('code', $id)
		->first();
	}
	
	public function getTranslatorByLanguageCombo(Request $request){
		// dd($id);
		$id = $request->get('q');
		return User::where('language_combination', 'like', '%'.$id.'%')
		->where([['user_type', 3], ['roles', 1], ['active', 1]])
		->orWhere([['user_type', 3], ['roles', 3], ['active', 1]])
		->get(['id', DB::raw('name as text')]);
	}
	
	public function getEditorByLanguageCombo(Request $request){
		$id = $request->get('q');
		return User::where('language_combination', 'like', '%'.$id.'%')
		->where([['user_type', 3], ['roles', 2], ['active', 1]])
		->orWhere([['user_type', 3], ['roles', 3], ['active', 1]])
		->get(['id', DB::raw('name as text')]);
	}
	
	//generate random code
	public function randomPrefix($length){
		$random= "";
		srand((double)microtime()*1000000);
		
		$data = "0123456789";
		
		for($i = 0; $i < $length; $i++){
			$random .= substr($data, (rand()%(strlen($data))), 1);
		}
		
		return $random;
	}
	
	public function count_wrods(Request $request){
		$uploadFile = Storage::path('/public/files/'.$request->file);
		$wordCount = 0;
		// print_r(mime_content_type($uploadFile));die;
		$mimetype = mime_content_type($uploadFile);
		if($mimetype === 'application/msword' || $mimetype === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || $mimetype === 'application/octet-stream' || $mimetype === 'application/vnd.openxmlformats-officedocument.wordprocessingml.documentapplication/vnd.openxmlformats-officedocument.wordprocessingml.document'){
			if($mimetype === 'application/msword'){
				$fileHandle = fopen($uploadFile, "r");
				$text = shell_exec(env('Antiword').' -m UTF-8.txt ' . $uploadFile);
				if($text == "\n"){
					print_r("I'm afraid the text stream of this file is too small to handle.");die;
					$text = shell_exec(env('Antiword').' '.$uploadFile);
				}
				$striped_content= $text;
			}
			if ($mimetype === 'application/octet-stream' || $mimetype === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || $mimetype === 'application/vnd.openxmlformats-officedocument.wordprocessingml.documentapplication/vnd.openxmlformats-officedocument.wordprocessingml.document'){
				
				$striped_content = '';
				$content = '';
				$zip = new ZipArchive;
				if (true === $zip->open($uploadFile)) {
					// If done, search for the data file in the archive
					if (($index = $zip->locateName("word/document.xml")) !== false) {
						// If found, read it to the string
						$data = $zip->getFromIndex($index);
						$zip->close();
						$xml = new \DOMDocument();
						$xml->loadXML($data, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
						$striped_content = strip_tags($xml->saveXML());
						$m = mb_strlen($striped_content,'utf-8');
						$s = strlen($striped_content);
						$striped_content = strval($striped_content);
					}
				}
			}
			
			$wordCount = $this->comment_count_word($striped_content);
			
		}
		if(substr($uploadFile, -3) == "txt"){
			$text = file_get_contents($uploadFile);
			$text_array = explode(PHP_EOL,$text);
			$wordCount = $this->comment_count_word($text);
			
		}
		$array = array([
			'word_count' => $wordCount,
		]);
		return response($array);
	}
	
	static public function comment_count_word($text){
		$encoding = mb_detect_encoding($text);
		
		$result = array(
			'count_zh' => 0,
			'count_en' => 0,
			'count_jp' => 0,
			'count_es' => 0,
			'count_id' => 0,
			'count_ko' => 0,
		);
		
		$text_zh  = preg_replace("/[^\p{Han}\？\！\；\．\〜\ー\。\，\「\」\《\、\》\【\】\『\』\：\（\）\（\）\／\・]/u","", $text);
		$result['count_zh'] =  mb_strlen($text_zh, $encoding);
		
		$text_en  = preg_replace("/[\'\"]/","", $text);
		$text_en  = preg_replace("/[^a-zA-Z\s]/"," ", $text_en);
		$result['count_en'] = str_word_count($text_en);
		
		$text_cyrillic  = preg_replace("/[^\p{Cyrillic}\s]/","", $text);
		$result['count_ru'] = str_word_count($text_cyrillic);
		
		//$pattern_jp = "[^\p{Hiragana}\p{Katakana}\]";
		$text_jp  = preg_replace("/[^\p{Han}\p{Hiragana}\p{Katakana}\．\〜\ー\。\，\「\」\《\、\》\【\】\『\』\：\（\）\（\）\／\・]/u","", $text );
		//$this->count_jp = mb_strlen($text_jp, $encoding);
		$result['count_jp'] =  mb_strlen($text_jp, $encoding);
		
		$text_ko  = preg_replace("/[^\p{Han}\p{Hangul}\？\！\；\．\〜\ー\。\，\「\」\《\、\》\【\】\『\』\：\（\）\（\）\／\・]/u","", $text);
		$result['count_ko'] =  mb_strlen($text_ko, $encoding);
		
		$text_ru  = preg_replace("/[^\x{0430}-\x{044F}\x{0410}-\x{042F}\s]/u"," ", $text);
		$result['count_ru'] = count(preg_split('/\s+/', $text_ru));
		
		$result['count_ru_literra'] = $result['count_ru'];
		
		$result['count_es'] = str_word_count($text_en);
		
		$result['count_id'] = str_word_count($text_en);
		
		return $result;
	}
}