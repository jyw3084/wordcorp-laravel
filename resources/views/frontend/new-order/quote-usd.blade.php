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
							<p id="delivery-email">
								{{$email}}
							</p>
							<h5>CC</h5>
							<p><span id="delivery-email-cc">{{$delivery_emails}}</span> 
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
							{{ trans('frontend.order.qoute.your_order_sub_title')}}	
						</p>
					</div>
					<div class="row">
						<div class="col-md-4">
							<h5>{{ trans('frontend.order.qoute.estimated_work_time')}}</h5>
							<p><span>{{$hours}}</span> {{ trans('frontend.order.qoute.hours')}}</p>
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
							<p>{{ number_format($order_price, 2, '.', ',') }} USD</p>
						</div>
						<div class="col-md-4">
							<h5>{{ trans('frontend.order.qoute.total_price')}}
								<small>({{ trans('frontend.order.qoute.tax_details')}})</small>
							</h5>
							<p>{{ number_format($total_price, 2, '.', ',') }} USD</p>
						</div>
					</div>
				</div>
				<div class="p-5 border-bottom">
					<div class="text-center">
						<h3>{{ trans('frontend.order.qoute.documents')}}</h3>
					</div>
					<div class="data-item uploaded-file-wrapper text-left">
						@foreach(json_decode($associated_docs) as $k => $v)
							<div class="card bg-light mb-3">
								<div class="card-body">
									<div class="row align-items-center mb-3">
										<div class="col">
											<h5>{{ trans('frontend.order.ntd.upload_files.file') }}<i class="bi bi-file-text"></i></h5>
											<p>{{ $v->filename }}</p>
										</div>
									</div>
									<div class="row">
										<div class="col-md-3">
											<h5>{{ trans('frontend.order.ntd.order_summary.translation') }}</h5>
											<p>
											<span>{{ $v->language_combination }}</span>
										</div>
										<div class="col-md-3">
											<h5>{{ trans('frontend.order.ntd.select_service.service') }}</h5>
											<p>{{ trans('frontend.order.ntd.select_service.option_'.$v->service_type) }}</p>
										</div>
										<div class="col-md-3">
											<h5>{{ trans('frontend.order.ntd.select_service.expertise') }}</h5>
											<p>{{ trans('frontend.order.expertise.'.$v->expertise) }}</p>
										</div>
										<div class="col-md-3">
											<h5>{{ trans('frontend.order.ntd.select_service.style') }}</h5>
											<p>{{ trans('frontend.order.style.'.$v->style) }}</p>
										</div>
									</div>
									<div class="row">
										<div class="col-md-3">
											<h5>{{ trans('frontend.order.ntd.select_service.word_count') }}</h5>
											<p>{{ $v->word_count }}</p>
										</div>
										<div class="col-md-3">
											<h5>{{ trans('frontend.order.ntd.order_summary.service_rate') }}</h5>
											<p> {{ number_format($v->service_rate, 2, '.', ',') }} USD/word</p>
										</div>
										<div class="col-md-3">
											<h5>{{ trans('frontend.order.ntd.order_summary.price') }}</h5>
											<p>{{ number_format($v->doc_price, 2, '.', ',') }} USD</p>
										</div>
									</div>
									@if($payment_status == 0 && $order_status == 0)
										<div class="row">
											<div class="col">
												<p class="text-muted">
												{{ trans('frontend.order.qoute.docs_notes_p') }}
													<button
														type="button"
														class="btn btn-link y_notes"
														id ="note{{$k}}"
														data-key="{{$k}}"
														data-note="{{ $v->notes }}"
														data-fileid="{{ $v->file_id }}"
														data-order_number="{{ $order_number }}"
													>
													{{ trans('frontend.order.qoute.your_notes') }}
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
				<!-- If order unconfirmed -->
				@if($payment_status == 0)
				<div class="p-5 border-bottom text-center">
					<div class="mb-3">
						<h3>{{ trans('frontend.order.qoute.payment_option')}}</h3>
					</div>
					<p>
						{{ trans('frontend.order.qoute.usd_payment_note')}}
					</p>
					
					<p hidden id="paypal_client_id" data-id="{{ env('PAYPAl_CLIENT_ID') }}"></p>
					<div class="mb-3" id="paypal-button">
						{{-- <button class="btn btn-link">
							<img
								src="{{URL::to('themes/default/assets/img/btn_xpressCheckout.png')}}"
								alt="Paypal Checkout"
							/>
						</button> --}}
					</div>
					<p class="text-muted">
						{{ trans('frontend.order.qoute.usd_footer_note_1')}}
						<a href="mailto:service@thewordcorp.com">service@thewordcorp.com</a>
						{{ trans('frontend.order.qoute.usd_footer_note_2')}}
					</p>
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
						<button type="submit" id="changEmail" class="btn btn-primary">
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
						<textarea class="form-control" name="notes" rows="3" id="note_data"></textarea>
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
		<script src="{{ asset('themes/default/assets/js/new-order-quote.js?').date('YmdHis') }}"></script>
		@if($payment_status == 0)
		<script src="https://www.paypalobjects.com/api/checkout.js"></script>

		<script>
			var paypal_client_id = $('#paypal_client_id').attr('data-id');
			paypal.Button.render({
			  // Configure environment
			  env: 'sandbox',
			  client: {
				sandbox: paypal_client_id,
				production: 'demo_production_client_id'
			  },
			  // Customize button (optional)
			  locale: '{{ str_replace(['en', 'zh'], ['en_US', 'zh_TW'], app()->getLocale()) }}',
			  style: {
				size: 'small',
				color: 'gold',
				shape: 'pill',
			  },
		  
			  // Enable Pay Now checkout flow (optional)
			  commit: true,
		  
			  // Set up a payment
			  payment: function(data, actions) {
				return actions.payment.create({
				  transactions: [{
					amount: {
					  total: {{ $total_price }},
					  currency: 'USD'
					}
				  }]
				});
			  },
			  // Execute the payment
			  onAuthorize: function(data, actions) {
				return actions.payment.execute().then(function(data) {
					if(data.state == 'approved')
					{
						// Show a confirmation message to the buyer
						$.ajax({
							url: "/api/paid-order",
							type: "POST",
							enctype: 'multipart/form-data',
							contentType: false,
							headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							},
							dataType:"json",
							data: {
								id: {{ $id }},
								payment_status: 1,
								order_status: 3,
							},
							async:false,
						});

						window.location.reload();
					}
					else
					{
						$('#pay_fail').show();
						$('#order_title').hide();
						window.scrollTo(0, 0);
					}
				});
			  }
			}, '#paypal-button');
		  
		  </script>
		@endif
