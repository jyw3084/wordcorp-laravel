		<link href="{{ asset('themes/default/assets/css/new-order.css') }}" rel="stylesheet">
		
		<main>
			<div class="container">
				<form name="form" id="msform" method="POST">
					<!-- progressbar -->
					<ul id="progressbar">
						<li class="active" id="account">
							<strong>{{ trans('frontend.order.ntd.upload_files.tab_title') }}</strong>
						</li>
						<li id="personal">
							<strong>{{ trans('frontend.order.ntd.select_service.tab_title') }}</strong>
						</li>
						<li id="payment">
							<strong>{{ trans('frontend.order.ntd.order_summary.tab_title') }}</strong>
						</li>
					</ul>
					<fieldset>
						<div class="row text-left mb-5">
							<div class="col-lg-8">
								<h2>{{ trans('frontend.order.ntd.upload_files.title') }}</h2>
								<ul>
									<li>
										{{ trans('frontend.order.ntd.upload_files.list_1') }}
									</li>
									<li>
										{{ trans('frontend.order.ntd.upload_files.list_2') }}
									</li>
									<li>
										{{ trans('frontend.order.ntd.upload_files.list_3') }}
									</li>
								</ul>
							</div>
						</div>
						<div class="uploaded-file-wrapper text-left" id="main-div">
							{{-- data for js since trans cant be use there --}}
							<p hidden id="trans_file" data-id="<?php echo trans('frontend.order.ntd.upload_files.file'); ?>"></p>
							<p hidden id="trans_remove" data-id="<?php echo trans('frontend.order.ntd.upload_files.remove'); ?>"></p>
							<p hidden id="trans_selectLang" data-id="<?php echo trans('frontend.order.ntd.upload_files.select_language'); ?>"></p>
							<p hidden id="trans_from" data-id="<?php echo trans('frontend.order.ntd.upload_files.from'); ?>"></p>
							<p hidden id="trans_to" data-id="<?php echo trans('frontend.order.ntd.upload_files.to'); ?>"></p>
							<p hidden id="trans_choose_file" data-id="<?php echo trans('frontend.order.ntd.upload_files.choose_file'); ?>"></p>
							
						</div>
						<div class="form-group text-left">
							<label class="btn btn-success">
								<i class="bi bi-plus-lg"></i> {{ trans('frontend.order.ntd.upload_files.add_new_file') }}
								<input type="file" id="uploadDoc" class="upload-file" hidden />
							</label>
						</div>
						
						<a href="{{ URL::to('/') }}" class="btn btn-light">{{ trans('frontend.order.ntd.upload_files.cancel_order') }}</a>
						<button type="button" name="next" id="word-count" class="next btn btn-primary" disabled>{{ trans('frontend.order.ntd.upload_files.get_word_count') }}</button>
					</fieldset>
					<fieldset>
						{{-- data for step 2 in js since trans cant be use there --}}
						<p hidden id="trans_selectedLang" data-id="<?php echo trans('frontend.order.ntd.select_service.selected_languages'); ?>"></p>
						<p hidden id="trans_wordCount" data-id="<?php echo trans('frontend.order.ntd.select_service.word_count'); ?>"></p>
						<p hidden id="trans_expertise" data-id="<?php echo trans('frontend.order.ntd.select_service.expertise'); ?>"></p>
						<p hidden id="trans_style" data-id="<?php echo trans('frontend.order.ntd.select_service.style'); ?>"></p>
						<p hidden id="trans_notes" data-id="<?php echo trans('frontend.order.ntd.select_service.notes'); ?>"></p>
						<p hidden id="trans_service" data-id="<?php echo trans('frontend.order.ntd.select_service.service'); ?>"></p>
						<p hidden id="trans_type_of_service" data-id="<?php echo trans('frontend.order.ntd.select_service.type_of_service'); ?>"></p>
						<p hidden id="trans_translation" data-id="<?php echo trans('frontend.order.ntd.order_summary.translation'); ?>"></p>
						<p hidden id="trans_service_rate" data-id="<?php echo trans('frontend.order.ntd.order_summary.service_rate'); ?>"></p>
						<p hidden id="trans_price" data-id="<?php echo trans('frontend.order.ntd.order_summary.price'); ?>"></p>
						
						
						<div class="step3 uploaded-file-wrapper text-left">
							
						</div>
						<input
							id='btn_back2'
							type="button"
							name="previous"
							class="previous btn btn-secondary"
							value="{{ trans('frontend.buttons.back') }}"
						/>
						<input
							id='btn_next2'
							type="button"
							name="next"
							class="next btn btn-primary"
							value="{{ trans('frontend.buttons.next_step') }}"
						/>
					</fieldset>
					<fieldset>
						<div class="text-left mb-5">
							<h2>{{ trans('frontend.order.ntd.order_summary.tab_title') }}</h2>
							<p>{{ trans('frontend.order.ntd.order_summary.total') }}: <span id="span-total">X,XXX NTD</span></p>
							<p>{{ trans('frontend.order.ntd.order_summary.estimated_delivery_time') }}: <span id="delivery_hours"> hours</span> {{ trans('frontend.order.ntd.order_summary.hours') }}</p>
							<p>{{ trans('frontend.order.ntd.order_summary.estimated_delivery_date') }}: <span id="delivery_date">September 20, 2021</span></p>
							<small
								>*{{ trans('frontend.order.ntd.order_summary.tab_3_note') }}</small
							>
						</div>
						<div class="uploaded-file-wrapper text-left last_step">
							
						</div>
						<div class="card mb-3">
							<div class="card-body">
								<h4 class="card-title">{{ trans('frontend.order.ntd.order_summary.terms_of_service') }}</h4>
								<p class="card-text text-left">
									{!! trans('frontend.order.ntd.order_summary.terms_p1_1') !!}
								</p>
								<p class="card-text text-left">
									{!! trans('frontend.order.ntd.order_summary.terms_p1_2') !!}
								</p>
								<p class="card-text text-left">
									{!! trans('frontend.order.ntd.order_summary.terms_p2_1') !!}
								</p>
								<p class="card-text text-left">
									{!! trans('frontend.order.ntd.order_summary.terms_p2_2') !!}
								</p>
								<p class="card-text text-left">
									{!! trans('frontend.order.ntd.order_summary.terms_p2_3') !!}
								</p>
								<div class="form-group text-left">
									<label for="clientEmail">{{ trans('frontend.order.ntd.order_summary.your_email') }}</label>
									<input
										type="email"
										id="clientEmail"
										class="form-control"
										aria-describedby="emailHelp"
									/>
									<small id="emailError1" hidden class="hidden form-text text-danger">{{ trans('frontend.validations.required') }}</small>
									<small id="emailError2" hidden class="hidden form-text text-danger">{{ trans('frontend.validations.email') }}</small>
									<small id="emailHelp" class="form-text text-muted"
										>{{ trans('frontend.order.ntd.order_summary.complete_note') }}</small
									>
								</div>
							</div>
						</div>
						<input
							id="order_summary_back"
							type="button"
							name="previous"
							class="previous btn btn-secondary"
							value="{{ trans('frontend.buttons.back') }}"
						/>
						<button id="agree_submit" type="button" name="submit" class="submit btn btn-primary">
							{{ trans('frontend.buttons.agree_and_continue') }}
						</button>
					</fieldset>
				</form>
			</div>
		</main>
		<!-- Upload file not supported modal -->
		<div
			class="modal fade"
			id="fileUnsupportedModal"
			tabindex="-1"
			aria-labelledby="fileUnsupportedModalLabel"
			aria-hidden="true"
		>
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body">
						<h5><i class="bi bi-file-x-fill text-danger"></i> {{ trans('frontend.order.ntd.select_service.upload_failed') }}</h5>
						<p>
						{{ trans('frontend.order.ntd.select_service.not_support') }}
						</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" data-dismiss="modal">
						{{ trans('frontend.order.ntd.select_service.ok') }}
						</button>
					</div>
				</div>
			</div>
		</div>
		<!-- Types of service modal -->
		<div
			class="modal fade"
			id="serviceTypesModal"
			tabindex="-1"
			aria-labelledby="serviceTypesModalLabel"
			aria-hidden="true"
		>
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="serviceTypesModalLabel">
						{{ trans('frontend.order.ntd.select_service.modal_header') }}
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
						{!! trans('frontend.order.ntd.select_service.modal_content') !!}
					</div>
					<div class="modal-footer">
						<button
							type="button"
							class="btn btn-secondary"
							data-dismiss="modal"
						>
							Close
						</button>
					</div>
				</div>
			</div>
		</div>
		<script src="{{ asset('themes/default/assets/js/bs-custom-file-input.js?'.date('YmdHis')) }}"></script>
		<script src="{{ asset('themes/default/assets/js/new-order.js?'.date('YmdHis')) }}"></script>
		<script src="{{ asset('themes/default/assets/js/validation/jquery.validate.min.js') }}"></script>
		<script>
			// $('#fileUnsupportedModal').modal('show');

			
		</script>
