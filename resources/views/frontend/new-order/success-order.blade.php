<link href="{{ asset('themes/default/assets/css/new-order.css') }}" rel="stylesheet">

<main>
    <div class="container" id="quote_ntd">
        <div class="p-5 border-bottom text-center">
            <!-- If payment success -->
            <h3 class="text-success">
                {!! trans('frontend.order.response.success') !!}
            </h3>
        </div>
        <div class="p-5 border-bottom">
            <div class="row">
                <div class="col-md-4">
                    <h5>{{ trans('frontend.order.qoute.order_date')}}</h5>
                    <p id="qoute_order_date">{{ date( 'Y-m-d' , strtotime($order_date)) }}</p>
                </div>
                <div class="col">
                    <h5>{{ trans('frontend.order.qoute.translation_will_be_delivered')}}</h5>
                    <p id="delivery-email">
                        {{ $email }}
                    </p>
                    <h5>CC</h5>
                    <p><span id="delivery-email-cc">{{ $delivery_emails }}</span></p>
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
                    <p><span id="qoute_estimated_time">{{ $order_time }}</span> hours</p>
                </div>
                <div class="col-md-4">
                    <h5>{{ trans('frontend.order.qoute.estimated_delivery_date')}}</h5>
                    <p id="qoute_estimated_delivery_date">
                        {{ date('Y-m-d' , strtotime($deadline)) }}<br/>
                        {{ date('h:i A' , strtotime($deadline)) }}
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <h5>{{ trans('frontend.order.qoute.sub_total')}}</h5>
                    <p id="total">{{ number_format($order_price) }} NTD</p>
                </div>
                <div class="col-md-4">
                    <h5>{{ trans('frontend.order.qoute.total_price')}}
                        <small>({{ trans('frontend.order.qoute.tax_details')}})</small></h5>
                    <p class="total_with_discount">{{ number_format($total_price) }} NTD</p>
                </div>
            </div>
        </div>
        <div class="p-5 border-bottom">
            <div class="text-center">
                <h3>{{ trans('frontend.order.qoute.documents')}}</h3>
            </div>
            <div class="data-item uploaded-file-wrapper text-left">

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
                                <p>{{ $v->doc_price }} NTD</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <p class="text-muted">
									{{ trans('frontend.order.qoute.docs_notes_p')}}
                                    <button type="button" class="btn btn-link y_notes" data-note="{{ $v->notes }}">
									{{ trans('frontend.order.qoute.your_notes')}}
                                    </button>
                                </p>
                            </div>
                        </div>
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
            <a href='/new-order/quote/{{ $order_number }}' class="btn btn-success"
                target="_blank">{{ trans('frontend.order.qoute.view_quotation')}}</a> <!-- Link to quotation PDF -->
        </div>
    </div>
</main>
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
<script>

$('.y_notes').on('click', function(){
	var note = $(this).data('note');
	$('#adjustNotesModal').modal('show');
	$('#note_data').val(note);
});
</script>
