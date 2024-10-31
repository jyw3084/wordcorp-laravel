<link href="{{ asset('themes/default/assets/css/new-order.css') }}" rel="stylesheet">

		<main>
			<div class="container" id="quote_ntd">
				<div class="p-5 border-bottom text-center">
          <!-- If order unconfirmed -->
					<h3 id="pay_fail" style="display:none">
						{!! trans('frontend.order.response.failed') !!}
					</h3>
					@if($order_status == 0)
					<h3 id="order_title">
						{{ trans('frontend.order.qoute.title')}}
					</h3>
					@elseif($order_status == 6)
					<h3 id="order_cancelled">
						{!! trans('frontend.order.response.cancelled') !!}
					</h3>
					@else
					<h3 id="pay_success">
						{!! trans('frontend.order.response.success') !!}
					</h3>
					@endif
				</div>
				<div class="p-5 border-bottom">
					<div class="row">
						<div class="col-md-4">
							<h5>{{ trans('frontend.order.qoute.order_date')}}</h5>
							<p>
								{{ date('Y-m-d' , strtotime($order_date)) }}<br/>
								{{ date('h:i A' , strtotime($order_date)) }}
							</p>
						</div>
						<div class="col">
							<h5>{{ trans('frontend.order.qoute.translation_will_be_delivered')}}</h5>
							<p>
								{{ $email }}
							</p>
							<h5>CC</h5>
							<p><span id="delivery-email-cc">{{ $delivery_emails }}</span> 
							@if($payment_status == 0 && $order_status == 0)
								<button 
									type="button" 
									data-toggle="modal"
									data-target="#changeEmailModal" class="btn btn-link">{{ trans('frontend.order.qoute.change')}}
								</button>
							@endif
							</p>
						</div>
					</div>
				</div>
				<div class="p-5 border-bottom">
					<div class="text-center mb-3">
						<h3>{{ trans('frontend.order.qoute.your_order')}}</h3>
						<p class="text-muted">
							{{ trans('frontend.order.qoute.your_order_sub_title')}} </p>
					</div>
					<div class="row">
						<div class="col-md-4">
							<h5>{{ trans('frontend.order.qoute.estimated_work_time')}}</h5>
							<p><span>{{ $order_time }}</span> {{ trans('frontend.order.qoute.hours')}}</p>
						</div>
						<div class="col-md-4">
							<h5>{{ trans('frontend.order.qoute.estimated_delivery_date')}}</h5>
							<p>
								{{ date('Y-m-d' , strtotime($deadline)) }}<br/>
								{{ date('h:i A' , strtotime($deadline)) }}
							</p>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4">
							<h5>{{ trans('frontend.order.qoute.sub_total')}}</h5>
							<p>{{ number_format($order_price) }} NTD</p>
						</div>
						<div class="col-md-4">
							<h5>{{ trans('frontend.order.qoute.total_price')}}
								<small>({{ trans('frontend.order.qoute.tax_details')}})</small></h5>
							<p>{{ number_format($order_price * 1.05) }} NTD</p>
						</div>
					</div>
				</div>
				<div class="p-5 border-bottom">
					<div class="text-center">
						<h3>{{ trans('frontend.order.qoute.documents')}}</h3>
					</div>
					<div class="uploaded-file-wrapper text-left">

					<?php
						$data = json_decode($associated_docs);
					?>
					@foreach($data as $k => $v)
						<div class="card bg-light mb-3">
							<div class="card-body">
								<div class="row align-items-center mb-3">
									<div class="col">
										<h5>{{ trans('frontend.order.ntd.upload_files.file')}}<i class="bi bi-file-text"></i></h5>
										<p>{{ $v->filename }}</p>
									</div>
								</div>
								<div class="row">
									<div class="col-md-3">
										<h5>{{ trans('frontend.order.ntd.order_summary.translation')}}</h5>
										<p>
											<span>{{ $lang[explode(' -> ', $v->language_combination)[0]] }}</span>
											<i class="bi bi-arrow-right"></i>
											<span>{{ $lang[explode(' -> ', $v->language_combination)[1]] }}</span>
										</p>
									</div>
									<div class="col-md-3">
										<h5>{{ trans('frontend.order.ntd.select_service.service')}}</h5>
										<p>{{ trans('frontend.order.ntd.select_service.option_'.$v->service_type)}}</p>
									</div>
									<div class="col-md-3">
										<h5>{{ trans('frontend.order.ntd.select_service.expertise')}}</h5>
										<p>{{ trans('frontend.order.expertise.'.$v->expertise) }}</p>
									</div>
									<div class="col-md-3">
										<h5>{{ trans('frontend.order.ntd.select_service.style')}}</h5>
										<p>{{ trans('frontend.order.style.'.$v->style)}}</p>
									</div>
								</div>
								<div class="row">
									<div class="col-md-3">
										<h5>{{ trans('frontend.order.ntd.select_service.word_count')}}</h5>
										<p>{{ $v->word_count }}</p>
									</div>
									<div class="col-md-3">
										<h5>{{ trans('frontend.order.ntd.order_summary.service_rate')}}</h5>
										<p>{{ $v->service_rate }} NTD/word</p>
									</div>
									<div class="col-md-3">
										<h5>{{ trans('frontend.order.ntd.order_summary.price')}}</h5>
										<p>{{ round($v->doc_price) }} NTD</p>
									</div>
								</div>
								@if($payment_status == 0 && $order_status == 0)
									<div class="row">
										<div class="col">
											<p class="text-muted">
												{{ trans('frontend.order.qoute.docs_notes_p')}}
												<button 
													type="button" 
													class="btn btn-link y_notes" 
													data-note="{{ $v->notes }}" 
													data-fileid="{{ $v->file_id }}" 
													data-order_number="{{ $order_number }}" 
													id="{{ $order_number }}"
												>
												{{ trans('frontend.order.qoute.your_notes')}}
												</button>
											</p>
										</div>
									</div>
								@endif
							</div>
						</div>

					@endforeach
					</div>
				</div>
				<div class="p-5 border-bottom">
					<div class="text-center mb-3">
						<h3>{{ trans('frontend.invoice.information.title')}}</h3>
					</div>
					<!-- If triplicate invoice -->
					@if($invoice_type != 0)
					<div class="row" id="invoice_triplicate">
						<div class="col-md-2">
							<h5>{{ trans('frontend.invoice.information.type')}}</h5>
							<p>{{ trans('frontend.invoice.information.triplicate')}}</p>
						</div>
						<div class="col-md-5">
							<h5>{{ trans('frontend.invoice.information.compay_name')}}</h5>
							<p class="company">{{ $title }}</p>
						</div>
						<div class="col-md-5">
							<h5>{{ trans('frontend.invoice.information.business_number')}}</h5>
							<p class="serial_no">{{ $serial_no }}</p>
						</div>
					</div>
					@else
					<!-- If duplicate invoice -->
					<div class="row" id="invoice_duplicate">
						<div class="col-md-2">
							<h5>{{ trans('frontend.invoice.information.type')}}</h5>
							<p>{{ trans('frontend.invoice.information.duplicate')}}</p>
						</div>
						<div class="col-md-5">
							<h5>{{ trans('frontend.invoice.information.buyer')}}</h5>
							<p class="buyer">{{ $title }}</p>
						</div>
						<div class="col-md-5">
							<!-- If member carrier -->
							@if($carrier == 1)
							<h5>{{ trans('frontend.invoice.information.carrier')}}</h5>
							<p class="carrier">{{ trans('frontend.invoice.duplicateInvoice.member')}}</p>
							@else
							<!-- If mobile barcode carrier -->
							<h5>{{ trans('frontend.invoice.duplicateInvoice.mobile')}}</h5>
							<p class="barcode">{{ $barcode }}</p>
							@endif
						</div>
					</div>
					@endif
				</div>
        <div class="p-5 border-bottom text-center">
          <div class="mb-3">
            <!-- If order unconfirmed -->
						@if($payment_status == 0 && $order_status == 0)
            <h3>{{ trans('frontend.order.qoute.quotation')}}</h3>
            <!-- If order confirmed -->
						@else
            <h3>{{ trans('frontend.order.qoute.confirmed')}}</h3>
						@endif
          </div>
          <a href='/new-order/quote/{{ $order_number }}' class="btn btn-success" target="_blank">{{ trans('frontend.order.qoute.view_quotation')}}</a> <!-- Link to quotation PDF -->
        </div>
        <!-- If order unconfirmed -->
		@if($payment_status == 0 && $order_status == 0)
		<input type="hidden" id="order_id" value="{{ $id ?? 'null' }}">
		<input type="hidden" id="local" value="{{ $local }}">
				<div class="p-5 border-bottom text-center">
					<div class="mb-3">
						<h3>{{ trans('frontend.order.qoute.payment_option')}}</h3>
					</div>
					<div class="row radio-group">
						<div id="payNow" class="col radio p-3 {{ $discount != 0 ? 'selected':'' }}" data-number="{{ $order_number }}">
							<h5>
								<span class="text-danger">{{ trans('frontend.order.qoute.pay_now')}}</span> {{ trans('frontend.order.qoute.and_receive_a')}}
								<span class="text-danger">5%</span> {{ trans('frontend.order.qoute.last_part')}}
								 <span>{{ round($total_price) }}</span>
							</h5>
							<p class="text-muted">
							{{ trans('frontend.order.qoute.pay_now_note')}}
							</p>
						</div>
						<div id="payLater" class="col radio p-3 {{ $discount == 0 ? 'selected':'' }}" data-number="{{ $order_number }}">
							<h5>{{ trans('frontend.order.qoute.pay_later_div')}}</h5>
							<p class="text-muted">
							{{ trans('frontend.order.qoute.pay_later_note')}}
							</p>
						</div>
					</div>
					
				<button id="payNowBtn" data-id="{{ $id }}" class="btn btn-lg btn-block btn-primary" {{ $discount == 0 ? 'hidden':'' }}>{{ trans('frontend.order.qoute.pay_now_btn')}}</button>
          
		  <button id="payLaterBtn" class="btn btn-lg btn-block btn-primary" {{ $discount != 0 ? 'hidden':'' }}>{{ trans('frontend.order.qoute.start_translating')}}</button>
				</div>
			@endif
			</div>
		</main>
		<!-- Change email modal -->
		<div
			class="modal fade"
			id="changeEmailModal"
			tabindex="-1"
			aria-labelledby="changeEmailModalLabel"
			aria-hidden="true"
		>
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="changeEmailModalLabel">
							{{ trans('frontend.order.qoute.change_email')}}
						</h5>
						<button
							type="button"
							class="close"
							data-dismiss="modal"
							aria-label="Close"
						>
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<input type="email" name="changEmail" class="form-control" >
            <small class="form-text text-muted">{{ trans('frontend.order.qoute.change_email_note')}}</small>
					</div>
					<div class="modal-footer">
						<button
							type="button"
							class="btn btn-secondary"
							data-dismiss="modal"
						>
						{{ trans('frontend.buttons.cancel')}}
						</button>
						<button id="changEmail" type="button" class="btn btn-primary">
							{{ trans('frontend.buttons.save')}}
						</button>
					</div>
				</div>
			</div>
		</div>
		<!-- Adjust notes modal -->
		<div
			class="modal fade"
			id="adjustNotesModal"
			tabindex="-1"
			aria-labelledby="adjustNotesModalLabel"
		>
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="adjustNotesModalLabelX">
							{{ trans('frontend.order.ntd.select_service.notes')}}
						</h5>
						<button
							type="button"
							class="close"
							data-dismiss="modal"
							aria-label="Close"
						>
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<textarea class="form-control" name="notes" rows="3" id="note_data"></textarea
						>
					</div>
					<div class="modal-footer">
						<button
							type="button"
							class="btn btn-secondary"
							data-dismiss="modal"
						>
						{{ trans('frontend.buttons.close')}}
						</button>
						<button id="quote-submit-notes" type="submit" class="btn btn-primary">
							{{ trans('frontend.buttons.save')}}
						</button>
					</div>
				</div>
			</div>
		</div>
		<script src="{{ asset('themes/default/assets/js/new-order-quote.js?'.date('YmdHis')) }}"></script>
