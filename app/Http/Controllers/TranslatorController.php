<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Theme;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Payment;
use App\Models\LanguageCombo;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TranslatorController extends Controller
{
	private $order_array = [];
	private $final_order_arr = [];
	private $pagecount = 20;
	private $_perpage = 20;
	
	public function paginate($items, $perPage = 20, $page = null, $options = [])
	{
		$page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
		$items = $items instanceof Collection ? $items : Collection::make($items);
		return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
	}
	
	public function translator_bin()
	{
		$orders = Order::whereIn('order_status', [1,3,4])->get();
		$translator_data = [];
		foreach($orders as $order){
			foreach(json_decode($order->associated_docs, 420) as $data){
				if(!array_key_exists('translator', $data) || $data['translator'] == null){
					array_push($data, ['orderID' => $order['id'], 'deadline' => $order['deadline']]);
					array_push($translator_data, $data);
				}
			}
		}
		$count = count($translator_data);
		
		// $orders = Order::where('order_status', 4)->get();
		$editor_bin_data = [];
		if($orders != null){
			foreach($orders as $order){
				foreach(json_decode($order->associated_docs, 420) as $data){
					if((!array_key_exists('editor', $data) || $data['editor'] == null) && $data['service_type'] == 1){
						array_push($data, ['orderID' => $order['id'], 'deadline' => $order['deadline']]);
						array_push($editor_bin_data, $data);
					}
				}
			}
		}
		$editor_bin_data_count = count($editor_bin_data);
		
		$editing_orders = Order::whereRaw('JSON_CONTAINS(associated_docs, \'{"editor": '. '"'.Auth::user()->id.'"' . '}\')')
		->where('order_status','!=', 5)
		->get();
		$editing_data = [];
		if($editing_orders != null){
			foreach($editing_orders as $order){
				foreach(json_decode($order->associated_docs, 420) as $data){
					if(isset($data['editor']) && $data['editor'] == Auth::user()->id && empty($data['editor_finish'])){
						array_push($data, ['deadline' => $order['deadline']]);
						array_push($editing_data, $data);
					}
				}
			}
		}
		$editing_data_count = count($editing_data);
		
		
		//my translation
		$my_translation_orders = Order::whereRaw('JSON_CONTAINS(associated_docs, \'{"translator": '. '"'.Auth::user()->id.'"' . '}\') and order_status < 5')
		->get();
		$my_translation_data = [];
		foreach($my_translation_orders as $order){
			foreach(json_decode($order->associated_docs, 420) as $data){
				if(isset($data['translator']) && $data['translator'] == Auth::user()->id && empty($data['translator_finish'])){
					array_push($data, ['deadline' => $order['deadline']]);
					array_push($my_translation_data, $data);
				}
			}
			
		}
		$my_translation_data_count = count($my_translation_data);
		
		//my history
		$my_history_orders = Order::whereRaw('JSON_CONTAINS(associated_docs, \'{"editor": '. '"'.Auth::user()->id.'"' . '}\') or JSON_CONTAINS(associated_docs, \'{"translator": '. '"'.Auth::user()->id.'"' . '}\')')->get();
		$translator_history_data = [];
		$editor_history_data = [];
		if($my_history_orders != null){
			foreach($my_history_orders as $order){
				foreach(json_decode($order->associated_docs, 420) as $data){
					array_push($data, ['deadline' => $order['deadline']]);
					$data['order_status'] = $order->order_status;
					if(isset($data['translator']) && $data['translator'] == Auth::user()->id && isset($data['translator_finish']) && !empty($data['translator_finish']))
					{
						$data['type'] = 'translator';
						array_push($translator_history_data, $data);
					}
					if(isset($data['editor']) && $data['editor'] == Auth::user()->id && isset($data['editor_finish']) && !empty($data['editor_finish']))
					{
						$data['type'] = 'editor';
						array_push($editor_history_data, $data);
					}
				}
				
			}
		}
		$all_data = array_merge($translator_history_data, $editor_history_data);
		$my_history_data_count = count($all_data);
		
		$translator_count = count($translator_data) / $this->_perpage;
		$translator_data = $this->paginate($translator_data);
		$array = array(
			'user' => Auth::user(),
			'translator_data' => $translator_data,
			'count' => $count,
			'my_translation_data_count' => $my_translation_data_count,
			'my_history_data_count' => $my_history_data_count,
			'translator_count' => $translator_count,
			'editor_bin_data_count' => $editor_bin_data_count,
			'editing_data_count' => $editing_data_count,
			'page_count' => $this->pagecount
		);
		return Theme::uses('default')->layout('loginlayout')->of('frontend.translator.translator-bin', $array)->render();
	}
	
	public function editor_bin()
	{
		$editor = Auth::User();
		$orders = Order::whereIn('order_status', [1,3,4])->get();
		$editor_data = [];
		if($orders != null){
			foreach($orders as $order){
				foreach(json_decode($order->associated_docs, 420) as $data){
					if((empty($data['editor']) && $data['service_type'] == 1)){
						array_push($data, ['orderID' => $order['id'], 'deadline' => $order['deadline']]);
						array_push($editor_data, $data);
					}
				}
			}
		}
		$count = count($editor_data);
		
		// $translation_bin_orders = Order::whereIn('order_status', [1,3,4])->get();
		// print_r(count($orders));die;
		$translator_bin_data = [];
		foreach($orders as $order){
			foreach(json_decode($order->associated_docs, 420) as $data){
				if(!array_key_exists('translator', $data) || $data['translator'] == null){
					array_push($data, ['orderID' => $order['id'], 'deadline' => $order['deadline']]);
					array_push($translator_bin_data, $data);
				}
			}
		}
		$translator_bin_data_count = count($translator_bin_data);
		
		//my translations
		$my_translation_orders = Order::whereRaw('JSON_CONTAINS(associated_docs, \'{"translator": '. '"'.Auth::user()->id.'"' . '}\') and order_status < 5')
		->get();
		$my_translation_data = [];
		foreach($my_translation_orders as $order){
			foreach(json_decode($order->associated_docs, 420) as $data){
				if(isset($data['translator']) && $data['translator'] == Auth::user()->id && empty($data['translator_finish'])){
					array_push($data, ['orderID' => $order['id'], 'deadline' => $order['deadline']]);
					array_push($my_translation_data, $data);
				}   
			}
		}
		$my_translation_data_count = count($my_translation_data);
		
		//my editing
		$editing_orders = Order::whereRaw('JSON_CONTAINS(associated_docs, \'{"editor": '. '"'.Auth::user()->id.'"' . '}\')')
		->where('order_status','!=', 5)
		->get();
		$editing_data = [];
		if($editing_orders != null){
			foreach($editing_orders as $order){
				foreach(json_decode($order->associated_docs, 420) as $data){
					if(isset($data['editor']) && $data['editor'] == Auth::user()->id && empty($data['editor_finish'])){
						array_push($data, ['deadline' => $order['deadline']]);
						array_push($editing_data, $data);
					}
				}
				
			}
		}
		$editing_data_count = count($editing_data);
		
		//my history
		$my_history_orders = Order::whereRaw('JSON_CONTAINS(associated_docs, \'{"editor": '. '"'.Auth::user()->id.'"' . '}\') or JSON_CONTAINS(associated_docs, \'{"translator": '. '"'.Auth::user()->id.'"' . '}\')')->get();
		$translator_history_data = [];
		$editor_history_data = [];
		$all_data = [];
		if($my_history_orders != null){
			foreach($my_history_orders as $order){
				foreach(json_decode($order->associated_docs, 420) as $data){
					array_push($data, ['deadline' => $order['deadline']]);
					$data['order_status'] = $order->order_status;
					if(isset($data['translator']) && $data['translator'] == $editor->id && isset($data['translator_finish']) && !empty($data['translator_finish']))
					{
						$data['type'] = 'translator';
						array_push($translator_history_data, $data);
					}
					if(isset($data['editor']) && $data['editor'] == $editor->id && isset($data['editor_finish']) && $data['editor_finish'] == true)
					{
						$data['type'] = 'editor';
						array_push($editor_history_data, $data);
					}
				}
				
			}
		}
		$all_data = array_merge($translator_history_data, $editor_history_data);
		$my_history_data_count = count($all_data);
		
		$editor_count = count($editor_data) / $this->_perpage;
		$editor_data = $this->paginate($editor_data);
		$array = array(
			'editor' => $editor,
			'editor_data' => $editor_data,
			'count' => $count,
			'editing_data_count' => $editing_data_count,
			'my_history_data_count' => $my_history_data_count,
			'editor_count' => $editor_count,
			'translator_bin_data_count' => $translator_bin_data_count,
			'my_translation_data_count' => $my_translation_data_count,
			'page_count' => $this->pagecount
		);
		return Theme::uses('default')->layout('loginlayout')->of('frontend.translator.editor-bin', $array)->render();
	}
	
	public function my_translations()
	{
		//translation bin;
		$translation_bin_orders = Order::whereIn('order_status', [1,3,4])->get();
		$translator_bin_data = [];
		foreach($translation_bin_orders as $order){
			foreach(json_decode($order->associated_docs, 420) as $data){
				if(!array_key_exists('translator', $data) || $data['translator'] == null){
					array_push($data, ['deadline' => $order['deadline']]);
					array_push($translator_bin_data, $data);
				}
			}
			
		}
		$translator_bin_data_count = count($translator_bin_data);
		
		//my translations
		$orders = Order::whereRaw('JSON_CONTAINS(associated_docs, \'{"translator": '. '"'.Auth::user()->id.'"' . '}\') and order_status < 5')
		->get();
		$translator_data = [];
		foreach($orders as $order){
			foreach(json_decode($order->associated_docs, 420) as $data){
				if(isset($data['translator']) && $data['translator'] == Auth::user()->id && empty($data['translator_finish'])){
					array_push($data, ['orderID' => $order['id'], 'deadline' => $order['deadline']]);
					array_push($translator_data, $data);
				}
			}
			
		}
		// print_r($translator_data[2]);die;
		$count = count($translator_data);
		
		$editor_bin_orders = Order::whereIn('order_status', [1,3,4])->get();
		$editor_bin_data = [];
		if($editor_bin_orders != null){
			foreach($editor_bin_orders as $order){
				foreach(json_decode($order->associated_docs, 420) as $data){
					if(empty($data['editor']) && $data['service_type'] == 1){
						array_push($data, ['orderID' => $order['id'], 'deadline' => $order['deadline']]);
						array_push($editor_bin_data, $data);
					}
					
				}
				
			}
		}
		$editor_bin_data_count = count($editor_bin_data);
		
		$editing_orders = Order::whereRaw('JSON_CONTAINS(associated_docs, \'{"editor": '. '"'.Auth::user()->id.'"' . '}\')')
		->where('order_status','!=', 5)
		->get();
		$editing_data = [];
		if($editing_orders != null){
			foreach($editing_orders as $order){
				foreach(json_decode($order->associated_docs, 420) as $data){
					if(isset($data['editor']) && $data['editor'] == Auth::user()->id && empty($data['editor_finish'])){
						array_push($data, ['deadline' => $order['deadline']]);
						array_push($editing_data, $data);
					}
				}
				
			}
		}
		$editing_data_count = count($editing_data);
		
		//my history
		$my_history_orders = Order::whereRaw('JSON_CONTAINS(associated_docs, \'{"editor": '. '"'.Auth::user()->id.'"' . '}\') or JSON_CONTAINS(associated_docs, \'{"translator": '. '"'.Auth::user()->id.'"' . '}\')')->get();
		$translator_history_data = [];
		$editor_history_data = [];
		if($my_history_orders != null){
			foreach($my_history_orders as $order){
				foreach(json_decode($order->associated_docs, 420) as $data){
					array_push($data, ['deadline' => $order['deadline']]);
					$data['order_status'] = $order->order_status;
					if(isset($data['translator']) && $data['translator'] == Auth::user()->id && isset($data['translator_finish']) && !empty($data['translator_finish']))
					{
						$data['type'] = 'translator';
						array_push($translator_history_data, $data);
					}
					if(isset($data['editor']) && $data['editor'] == Auth::user()->id && isset($data['editor_finish']) && !empty($data['editor_finish']))
					{
						$data['type'] = 'editor';
						array_push($editor_history_data, $data);
					}
				}
				
			}
		}
		$all_data = array_merge($translator_history_data, $editor_history_data);
		$my_history_data_count = count($all_data);
		
		$translator_count = count($translator_data) / $this->_perpage;
		$translator_data = $this->paginate($translator_data);
		$array = array(
			'user' => Auth::user(),
			'translator_data' => $translator_data,
			'count' => $count,
			'translator_bin_data_count' => $translator_bin_data_count,
			'my_history_data_count' => $my_history_data_count,
			'translator_count' => $translator_count,
			'editor_bin_data_count' => $editor_bin_data_count,
			'editing_data_count' => $editing_data_count,
			'page_count' => $this->pagecount
		);
		return Theme::uses('default')->layout('loginlayout')->of('frontend.translator.my-translations', $array)->render();
	}
	
	public function my_editing()
	{
		$editor = Auth::User();
		// $orders = Order::whereRaw('JSON_CONTAINS(associated_docs, \'{"editor": '. '"'.Auth::user()->id.'"' . '}\')')->get();
		$editor_bin_orders = Order::whereIn('order_status', [1,3,4])->get();
		$editor_bin_data = [];
		if($editor_bin_orders != null){
			foreach($editor_bin_orders as $order){
				foreach(json_decode($order->associated_docs, 420) as $data){
					if((!array_key_exists('editor', $data) || $data['editor'] == null) && $data['service_type'] == 1){
						array_push($data, ['orderID' => $order['id'], 'deadline' => $order['deadline']]);
						array_push($editor_bin_data, $data);
					}
				}
			}
		}
		$editor_bin_data_count = count($editor_bin_data);
		
		$translation_bin_orders = Order::whereIn('order_status', [1,3,4])->get();
		// print_r(count($orders));die;
		$translator_bin_data = [];
		foreach($translation_bin_orders as $order){
			foreach(json_decode($order->associated_docs, 420) as $data){
				// if($data['translator'] == Auth::user()->id){
					if(!array_key_exists('translator', $data) || $data['translator'] == null){
						array_push($data, ['deadline' => $order['deadline']]);
						array_push($translator_bin_data, $data);
					}
					// }
				}
				
			}
			$translator_bin_data_count = count($translator_bin_data);
			
			//my translations
			$my_translation_orders = Order::whereRaw('JSON_CONTAINS(associated_docs, \'{"translator": '. '"'.Auth::user()->id.'"' . '}\') and order_status < 5')
			->get();
			$my_translation_data = [];
			foreach($my_translation_orders as $order){
				foreach(json_decode($order->associated_docs, 420) as $data){
					if(isset($data['translator']) && $data['translator'] == Auth::user()->id && empty($data['translator_finish'])){
						array_push($data, ['orderID' => $order['id'], 'deadline' => $order['deadline']]);
						array_push($my_translation_data, $data);
					}
				}
				
			}
			$my_translation_data_count = count($my_translation_data);
			
			//my editing
			$orders = Order::whereRaw('JSON_CONTAINS(associated_docs, \'{"editor": '. '"'.Auth::user()->id.'"' . '}\')')
			->where('order_status','!=', 5)
			->get();
			$editor_data = [];
			if($orders != null){
				foreach($orders as $order){
					foreach(json_decode($order->associated_docs, 420) as $data){
						if(isset($data['editor']) && $data['editor'] == Auth::user()->id && empty($data['editor_finish'])){
							array_push($data, ['orderID' => $order['id'], 'deadline' => $order['deadline']]);
							array_push($editor_data, $data);
						}
					}
					
				}
			}
			$count = count($editor_data);
			
			//my history
			$my_history_orders = Order::whereRaw('JSON_CONTAINS(associated_docs, \'{"editor": '. '"'.Auth::user()->id.'"' . '}\') or JSON_CONTAINS(associated_docs, \'{"translator": '. '"'.Auth::user()->id.'"' . '}\')')->get();
			$translator_history_data = [];
			$editor_history_data = [];
			if($my_history_orders != null){
				foreach($my_history_orders as $order){
					foreach(json_decode($order->associated_docs, 420) as $data){
						array_push($data, ['deadline' => $order['deadline']]);
						$data['order_status'] = $order->order_status;
						if(isset($data['translator']) && $data['translator'] == $editor->id && isset($data['translator_finish']) && !empty($data['translator_finish']))
						{
							$data['type'] = 'translator';
							array_push($translator_history_data, $data);
						}
						if(isset($data['editor']) && $data['editor'] == $editor->id && isset($data['editor_finish']) && $data['editor_finish'] == true)
						{
							$data['type'] = 'editor';
							array_push($editor_history_data, $data);
						}
					}
					
				}
			}
			$all_data = array_merge($translator_history_data, $editor_history_data);
			$my_history_data_count = count($all_data);
			
			$editor_count = count($editor_data) / $this->_perpage;
			$editor_data = $this->paginate($editor_data);
			$array = array(
				'editor' => $editor,
				'editor_data' => $editor_data,
				'count' => $count,
				'editor_bin_data_count' => $editor_bin_data_count,
				'my_history_data_count' => $my_history_data_count,
				'editor_count' => $editor_count,
				'translator_bin_data_count' => $translator_bin_data_count,
				'my_translation_data_count' => $my_translation_data_count,
				'page_count' => $this->pagecount
			);
		return Theme::uses('default')->layout('loginlayout')->of('frontend.translator.my-editing', $array)->render();
	}

	public function my_history()
	{
		//translation bin;
		$translation_bin_orders = Order::whereIn('order_status', [1,3,4])->get();
		// print_r(count($orders));die;
		$translator_bin_data = [];
		foreach($translation_bin_orders as $order){
			foreach(json_decode($order->associated_docs, 420) as $data){
				// if($data['translator'] == Auth::user()->id){
					if(!array_key_exists('translator', $data)){
						array_push($data, ['deadline' => $order['deadline']]);
						array_push($translator_bin_data, $data);
					}
					else{
						if($data['translator'] == null){
							array_push($data, ['deadline' => $order['deadline']]);
							array_push($translator_bin_data, $data);
						}
					}
			}
		}
		$translator_bin_data_count = count($translator_bin_data);
			
			//my translations
			$my_translation_orders = Order::whereRaw('JSON_CONTAINS(associated_docs, \'{"translator": '. '"'.Auth::user()->id.'"' . '}\') and order_status < 5')
			->get();
			$my_translation_data = [];
			foreach($my_translation_orders as $order){
				foreach(json_decode($order->associated_docs, 420) as $data){
					if(isset($data['translator']) && $data['translator'] == Auth::user()->id && empty($data['translator_finish'])){
						array_push($data, ['orderID' => $order['id'], 'deadline' => $order['deadline']]);
						array_push($my_translation_data, $data);
					}
				}
				
			}
			$my_translation_data_count = count($my_translation_data);
			
			$editor_bin_orders = Order::whereIn('order_status', [1,3,4])->get();
			$editor_bin_data = [];
			if($editor_bin_orders != null){
				foreach($editor_bin_orders as $order){
					foreach(json_decode($order->associated_docs, 420) as $data){
						if((!array_key_exists('editor', $data) || $data['editor'] == null) && $data['service_type'] == 1){
							array_push($data, ['orderID' => $order['id'], 'deadline' => $order['deadline']]);
							array_push($editor_bin_data, $data);
						}
					}
				}
			}
			$editor_bin_data_count = count($editor_bin_data);
			
			$editing_orders = Order::whereRaw('JSON_CONTAINS(associated_docs, \'{"editor": '. '"'.Auth::user()->id.'"' . '}\')')
			->where('order_status','!=', 5)
			->get();
			$editing_data = [];
			if($editing_orders != null){
				foreach($editing_orders as $order){
					foreach(json_decode($order->associated_docs, 420) as $data){
						if(isset($data['editor']) && $data['editor'] == Auth::user()->id && empty($data['editor_finish'])){
							array_push($data, ['deadline' => $order['deadline']]);
							array_push($editing_data, $data);
						}
					}
					
				}
			}
			$editing_data_count = count($editing_data);
			
			//my history
			$orders = Order::whereRaw('JSON_CONTAINS(associated_docs, \'{"editor": '. '"'.Auth::user()->id.'"' . '}\') or JSON_CONTAINS(associated_docs, \'{"translator": '. '"'.Auth::user()->id.'"' . '}\')')->get();
			$translator_data = [];
			$editor_data = [];
			foreach($orders as $order){
				foreach(json_decode($order->associated_docs, 420) as $data){
					array_push($data, ['deadline' => $order['deadline']]);
					$data['order_status'] = $order->order_status;
					if(isset($data['translator']) && $data['translator'] == Auth::user()->id && isset($data['translator_finish']) && !empty($data['translator_finish']))
					{
						$data['type'] = 'translator';
						array_push($translator_data, $data);
					}
					if(isset($data['editor']) && $data['editor'] == Auth::user()->id && isset($data['editor_finish']) && !empty($data['editor_finish']))
					{
						$data['type'] = 'editor';
						array_push($editor_data, $data);
					}
				}
				
			}
			$all_data = array_merge($translator_data, $editor_data);
			$all_data = array_reverse($all_data);
			$count = count($all_data);
			$translator_count = $count / $this->_perpage;
			$translator_data = $this->paginate($all_data);
			$array = array(
				'user' => Auth::user(),
				'translator_data' => $translator_data,
				'count' => $count,
				'translator_bin_data_count' => $translator_bin_data_count,
				'my_translation_data_count' => $my_translation_data_count,
				'translator_count' => $translator_count,
				'editor_bin_data_count' => $editor_bin_data_count,
				'editing_data_count' => $editing_data_count,
				'page_count' => $this->pagecount
			);
			return Theme::uses('default')->layout('loginlayout')->of('frontend.translator.my-history', $array)->render();
	}

	public function my_languages()
	{
		$user = Auth::User();
		$lang_combo = $user->language_combination;
		$lang_combo = json_decode($lang_combo, 420);
		
		$count = count($lang_combo);
		$lang_combo_count = count($lang_combo) / $this->_perpage;
		$lang_combo = $this->paginate($lang_combo);
		$array = array(
			'user' => $user,
			'lang_combo' => $lang_combo, 
			'count' => $count,
			'lang_combo_count' => $lang_combo_count, 
			'page_count' => $this->pagecount);
		return Theme::uses('default')->layout('loginlayout')->of('frontend.translator.my-languages', $array)->render();
	}
	
	function setter($order_data)
	{
		if($order_data){
			$_next_date = '';
			$_next_date = $order_data[0].'T00:00:00.000000Z';
			$_next_date =  date('Y-m-d', strtotime($_next_date . ' +1 day'));
			if(in_array($_next_date, $order_data)){
				$key = array_search($_next_date, $order_data);
				array_push($this->final_order_arr, array('date' => $order_data[0], 'next_date' => $_next_date));
				unset($order_data[0]);
				unset($order_data[$key]);
				$order_data = array_merge($order_data);
				$this->setter($order_data);
			}
			else{
				array_push($this->final_order_arr, array('date' => $order_data[0], 'next_date' => $_next_date));
				unset($order_data[0]);
				$order_data = array_merge($order_data);
				$this->setter($order_data);
			}
		}
		
	}
	
	public function my_profile()
	{
		$payments = Payment::where('uid', Auth::user()->id)->get();
		$records = [];
		foreach($payments as $payment){
			$order_deadline = strtotime($payment->order_deadline);
			$lang_combo = $payment->lang_combo;
			
			if(date('d', $order_deadline) >= 20) {
				$from = date('Y-m-', $order_deadline).'20';
			} else {
				$from = date('Y-m-', strtotime('-1 month', $order_deadline)).'20';
			}
			
			$new_record = array(
				'from' => $from,
				'lang_combo' => $lang_combo,
				'total' => $payment->total,
				'trans_word_count' => $payment->trans,
				'edit_word_count' => $payment->edit,
			);
			
			$found_record = current(array_filter($records, function ($record) use ($from, $lang_combo) {
				return $record['from'] === $from && $record['lang_combo'] === $lang_combo;
			}));
			
			if(!empty($found_record)) {
				$key = array_search($found_record, $records, true);
				unset($records[$key]);
				$found_record['total'] += $payment->total;
				$found_record['trans_word_count'] += $payment->trans;
				$found_record['edit_word_count'] += $payment->edit;
				array_push($records, $found_record);
			} else {
				array_push($records, $new_record);
			}
		}
		
		$array = array(
			'user' => Auth::user(),
			'records' => $records,
		);
		
		return Theme::uses('default')->layout('loginlayout')->of('frontend.translator.my-profile', $array)->render();
	}
	
	public function change_password()
	{
		$array = array('user' => Auth::user());
		return Theme::uses('default')->layout('loginlayout')->of('frontend.translator.change-password', $array)->render();
	}
}