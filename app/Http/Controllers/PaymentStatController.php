<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Theme;
use App\Models\Payment;
use App\Models\User;

class PaymentStatController extends Controller
{
    public function payout()
    {
			$payments = Payment::all();
			$records = [];
			foreach($payments as $payment) {
				$order_deadline = strtotime($payment->order_deadline);
				if (date('d', $order_deadline) >= 20) {
					$from = date('Y-m-', $order_deadline).'20';
				} else {
					$from = date('Y-m-', strtotime('-1 month', $order_deadline)).'20';
				}
				$new_record = array(
					'from' => $from,
					'total' => $payment->total,
					'trans_word_count' => $payment->trans,
					'edit_word_count' => $payment->edit,
				);
				$found_record = current(array_filter($records, function ($record) use ($from) {
				return $record['from'] === $from;
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
				'records' => $records,
			);
			return Theme::uses('default')->layout('loginlayout')->of('frontend.stat.payout', $array)->render();
    }

    public function get_payout_details(Request $request)
    {
			$from = date('Y-m-d', $request->period);
			$to = date('Y-m-', strtotime('+1 month', $request->period)).'19';
			$payments = Payment::whereRaw('(order_deadline >= ? AND order_deadline <= ?)', [$from.' 00:00:00', $to. ' 23:59:59'])->get();

			$users = [];
			foreach ($payments as $payment) {
				$found_user = User::where('id', $payment->uid)->first();
				$found_user['trans'] = $payment->trans;
				$found_user['edit'] = $payment->edit;
				$found_user['payout'] = $payment->total;

				if (empty($users)) {
					$users[] = $found_user;
				} else {
					foreach ($users as $user) {
						if ($user->id === $payment->uid) {
							$user['trans'] += $payment->trans;
							$user['edit'] += $payment->edit;
							$user['payout'] += $payment->total;
						} else {
							$users[] = $found_user;
						}
					}
				}
			}

			$html = '';
			if (!empty($users)) {
				foreach ($users as $user) {
					$trans = ($user->trans === null) ? 0 : $user->trans;
					$edit = ($user->edit === null) ? 0 : $user->edit;
					$html .= '<tr>
							<td>
								<span>'.$user->email.'</span>
							</td>
							<td>
								<span>'.$trans.'</span>
							</td>
							<td>
								<span>'.$edit.'</span>
							</td>
							<td>
								<span>'.$user->payout.'</span>
							</td>
						</tr>';
				}
			}
			$array = array(
				'html' => $html,
			);
			return response()->json($array);
    }
}
