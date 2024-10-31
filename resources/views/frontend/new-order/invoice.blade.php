<link href="{{ asset('themes/default/assets/css/new-order.css?').date('YmdHis') }}" rel="stylesheet">
		
		<main class="d-flex align-items-center">
			<div class="container pt-3">
				<form id="msform">
					<!-- If old client -->
					<fieldset id="updateInvoiceField">
						<!-- with triplicate invoice -->
						<div id="invoice_triplicate">
							<div class="mb-3">
								<h2>
									{{ trans('frontend.invoice.information.title')}}
								</h2>
								<p class="text-muted">
									{!! trans('frontend.invoice.information.triplicate_title') !!}
								</p>
							</div>
							<div class="row">
								<div class="col-md-4 py-3">
									<h5>{{ trans('frontend.invoice.information.compay_name')}}</h5>
									<p class="company"></p>
								</div>
								<div class="col-md-4 py-3">
									<h5>{{ trans('frontend.invoice.information.business_number')}}</h5>
									<p class="serial_no"></p>
								</div>
								<div class="col-md-4 py-3">
									<h5>{{ trans('frontend.invoice.information.phone_number')}}</h5>
									<p class="phone_no"></p>
								</div>
							</div>
						</div>
						<!-- with duplicate invoice -->
						<div id="invoice_duplicate">
							<div class="mb-3">
								<h2>{{ trans('frontend.invoice.information.title')}}</h2>
								<p class="text-muted">
									{!! trans('frontend.invoice.information.duplicate_title') !!}
								</p>
							</div>
							<div class="row">
								<div class="col-md-4 py-3">
									<h5>{{ trans('frontend.invoice.information.buyer')}}</h5>
									<p class="buyer">John Doe</p>
								</div>
								<div class="col-md-4 py-3">
									<!-- If member carrier -->
									<h5>{{ trans('frontend.invoice.information.carrier')}}</h5>
									<p class="carrier"></p>
									<!-- If mobile barcode carrier -->
									<h5>{{ trans('frontend.invoice.information.mobile_carrier')}}</h5>
									<p class="barcode"></p>
								</div>
								<div class="col-md-4 py-3">
									<h5>{{ trans('frontend.invoice.information.phone_number')}}</h5>
									<p class="phone_no"></p>
								</div>
							</div>
						</div>
						<input
							name="updateInvoice"
							type="button"
							class="updateInvoice btn btn-secondary"
							value="{{ trans('frontend.invoice.information.update_details')}}"
						/>
						<button type="submit" name="submit" class="submit btn btn-primary">
						{{ trans('frontend.invoice.information.confirm')}}
						</button>
					</fieldset>
					<!-- If new client -->
					<fieldset id="newClient">
						<h2>{{ trans('frontend.invoice.choice.title')}}</h2>
						<h5 class="text-muted">
						{{ trans('frontend.invoice.choice.sub_title')}}
						</h5>
						<div class="d-md-flex justify-content-center my-5">
							<div class="form-group mx-md-3">
								<input
									id="duplicate"
									type="radio"
									name="invoiceType"
									value="duplicate"
								/><br />
								<label for="duplicate" class="form-check-label h5 my-3"
									>{{ trans('frontend.invoice.choice.no')}}</label
								>
							</div>
							<div class="form-group mx-md-3">
								<input
									id="triplicate"
									type="radio"
									name="invoiceType"
									value="triplicate"
									checked
								/><br />
								<label for="triplicate" class="form-check-label h5 my-3"
									>{{ trans('frontend.invoice.choice.yes')}}</label
								>
							</div>
						</div>
						<input
							id="firstNext"
							type="button"
							class="btn btn-primary"
							value="{{ trans('frontend.invoice.next')}}"
						/>
					</fieldset>
					<!-- If triplicate invoice -->
					<fieldset id="triplicateInvoice">
						<div class="row text-left">
							<div class="col-md-6 offset-md-3">
								<div class="form-group">
									<label for="companyName"
										>{{ trans('frontend.invoice.triplicateInvoice.companyName')}}</label
									>
									<input
										type="text"
										id="companyName"
										class="form-control"
										required
									/>
								</div>
								<div class="form-group">
									<label for="bizNumber"
										>{{ trans('frontend.invoice.triplicateInvoice.serial_no')}}</label
									>
									<input
										type="text"
										id="bizNumber"
										class="form-control"
										maxlength="8"
										minlength="8"
										onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))"
										required
									/>
								</div>
							</div>
						</div>
						<input
							type="button"
							name="previous"
							class="previous btn btn-secondary"
							value="{{ trans('frontend.invoice.back')}}"
						/>
						<input
							type="button"
							name="next"
							class="btn btn-primary"
							value="{{ trans('frontend.invoice.next')}}"
							disabled
						/>
					</fieldset>
					<fieldset id="triplicateInvoiceConfirm">
						<div class="mb-3">
							<h2>
								{{ trans('frontend.invoice.information.title')}}
							</h2>
							<p class="text-muted">
								{!! trans('frontend.invoice.information.triplicate_title') !!}
							</p>
						</div>
						<div class="row">
							<div class="col-md-4 py-3">
								<h5>{{ trans('frontend.invoice.information.compay_name')}}</h5>
								<p class="company"></p>
							</div>
							<div class="col-md-4 py-3">
								<h5>{{ trans('frontend.invoice.information.business_number')}}</h5>
								<p class="serial_no"></p>
							</div>
								<div class="col-md-4 py-3">
									<h5>{{ trans('frontend.invoice.information.phone_number')}}</h5>
									<p class="phone_no"></p>
								</div>
						</div>
						<input
							name="updateInvoice"
							type="button"
							class="updateInvoice btn btn-secondary"
							value="{{ trans('frontend.invoice.information.update_details')}}"
						/>
						<button type="submit" name="submit" class="submit btn btn-primary">
							{{ trans('frontend.invoice.information.confirm')}}
						</button>
					</fieldset>
					<!-- If duplicate invoice -->
					<fieldset id="duplicateInvoice">
						<div class="row text-left">
							<div class="col-md-6 offset-md-3">
								<div class="form-group">
									<label for="buyerName"
										>{{ trans('frontend.invoice.duplicateInvoice.buyerName')}}</label
									>
									<input
										type="text"
										id="buyerName"
										class="form-control"
										required
									/>
								</div>
								<div class="form-group">
									<label for="selectCarrier"
										>{{ trans('frontend.invoice.duplicateInvoice.carrier')}}</label
									>
									<select
										name="selectCarrier"
										id="selectCarrier"
										class="form-control"
									>
										<option value="1">{{ trans('frontend.invoice.duplicateInvoice.member')}}</option>
										<option value="2"
											>{{ trans('frontend.invoice.duplicateInvoice.mobile')}}</option
										>
									</select>
									<small>{{ trans('frontend.invoice.duplicateInvoice.carrier_note') }}</small>
								</div>
								<div class="form-group d-none" id="mobileBarcodeInput">
									<label for="mobileBarcode"
										>{{ trans('frontend.invoice.duplicateInvoice.barcode')}}</label
									>
									<input
										type="text"
										id="mobileBarcode"
										class="form-control"
										placeholder="/12345678"
										required
									/>
								</div>
							</div>
						</div>
						<input
							type="button"
							name="previous"
							class="previous btn btn-secondary"
							value="{{ trans('frontend.invoice.back')}}"
						/>
						<input
							type="button"
							name="next"
							class="btn btn-primary"
							value="{{ trans('frontend.invoice.next')}}"
							disabled
						/>
					</fieldset>
					<fieldset id="duplicateInvoiceConfirm">
						<div class="mb-3">
							<h2>{{ trans('frontend.invoice.information.title')}}</h2>
							<p class="text-muted">
								{!! trans('frontend.invoice.information.duplicate_title') !!}
							</p>
						</div>
						<div class="row">
							<div class="col-md-4 py-3">
								<h5>{{ trans('frontend.invoice.information.buyer')}}</h5>
								<p class="buyer"></p>
							</div>
							<div class="col-md-4 py-3">
								<!-- If member carrier -->
								<h5>{{ trans('frontend.invoice.information.carrier')}}</h5>
								<p class="carrier"></p>
								<!-- If mobile barcode carrier -->
								<h5>{{ trans('frontend.invoice.information.mobile_carrier')}}</h5>
								<p class="barcode"></p>
							</div>
							<div class="col-md-4 py-3">
								<h5>{{ trans('frontend.invoice.information.phone_number')}}</h5>
								<p class="phone_no"></p>
							</div>
						</div>
						<input
							name="updateInvoice"
							type="button"
							class="updateInvoice btn btn-secondary"
							value="{{ trans('frontend.invoice.information.update_details')}}"
						/>
						<button type="submit" name="submit" class="submit btn btn-primary">
							{{ trans('frontend.invoice.information.confirm')}}
						</button>
					</fieldset>
					<!-- Update phone number -->
					<fieldset id="updatePhoneField">
						<div class="row text-left">
							<div class="col-md-6 offset-md-3">
								<div class="form-group">
									<label for="phoneNumber">{{ trans('frontend.invoice.phone.number')}}</label>
									<div class="input-group mb-3">
										<input
											type="text"
											placeholder="0912345678"
											id="phoneNumber"
											class="form-control"
											maxlength="10"
										/>
										<div class="input-group-append">
											<input 
												id="sendCode" 
												class="btn btn-primary" 
												type="button" 
												value="{{ trans('frontend.invoice.phone.verify')}}" 
												disabled
											/>
										</div>
										<div class="w-100 error d-none">
											<p class="text-danger">{{ trans('frontend.invoice.phone.invalid_phone') }}</p>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="code">{{ trans('frontend.invoice.phone.code') }}</label>
									<input type="text" id="code" name="code" class="form-control">
									<input type="hidden" id="recode" name="recode" class="form-control" />
									<p class="text-danger error d-none">{{ trans('frontend.invoice.phone.invalid_code') }}</p>
								</div>
							</div>
						</div>
						<input
							type="button"
							name="previous"
							class="btn btn-secondary"
							value="{{ trans('frontend.invoice.back')}}"
						/>
						<input
							type="button"
							name="next"
							class="btn btn-primary"
							value="{{ trans('frontend.invoice.next')}}"
							disabled
						/>
					</fieldset>
				</form>
			</div>
		</main>

		<script>
			$(document).ready(function () {
				var signUpPhone = $("#signUpPhone");
					$('#sendCode').on('click', function () {
						var phoneNumber = $('#phoneNumber').val();
						if (phoneNumber == '' || phoneNumber == ' ') {
							console.log('no phone number');
						} else {

							settime();

							$.ajax({
								url: '/api/send-sms-code',
								type: 'POST',
								data: { phoneNumber: phoneNumber },
								success: function (data) {
									data = data.slice(data.length - 6);
									$('#recode').val(data);
								}
							});
						}

						});

					var countdown = 60;
					function settime() {
						if (countdown == 0) {
							$('#sendCode').prop('disabled', false);
							$('#sendCode').val('{{ trans('frontend.invoice.phone.verify')}}');
							countdown = 60;
							return;
						} else {
							$('#sendCode').prop('disabled', true);
							$('#sendCode').val('Resend (' + countdown + ')');
							countdown--;
						}
						setTimeout(function () {
							settime()
						}, 1000)
					}
			});
		</script>
		<script src="{{ asset('themes/default/assets/js/new-order-quote.js?').date('YmdHis') }}"></script>
