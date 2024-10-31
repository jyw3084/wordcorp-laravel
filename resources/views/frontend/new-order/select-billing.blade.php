<!-- <!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link
			rel="stylesheet"
			href="../node_modules/bootstrap-icons/font/bootstrap-icons.css"
		/>
		<link rel="stylesheet" href="../assets/scss/new-order.css" />
		<title>New Order - Wordcorp</title>
	</head>
	<body> -->
		<link href="{{ asset('themes/default/assets/css/new-order.css') }}" rel="stylesheet">
		
		<main class="d-flex align-items-center">
			<div class="container">
				<div class="text-center">
					<h2>{{ trans('frontend.order.select_billing.question') }}</h2>
					<div
						id="selectBillingLocale"
						class="d-md-flex justify-content-center my-5"
					>
						<div class="form-group mx-md-3">
							<input
								type="radio"
								name="billingLocale"
								value="ntd"
								checked
							/><br />
							<label for="ntd" class="form-check-label h5 my-3"
								>{{ trans('frontend.order.select_billing.option_1') }}</label
							>
						</div>
						<div class="form-group mx-md-3">
							<input type="radio" name="billingLocale" value="usd" /><br />
							<label for="usd" class="form-check-label h5 my-3"
								>{{ trans('frontend.order.select_billing.option_2') }}</label
							>
						</div>
					</div>
					<div class="my-5">
						<a id="proceedBtn" class="btn btn-lg btn-primary" href="./ntd"
							>{{ trans('frontend.order.select_billing.proceed') }}</a
						>
					</div>
				</div>
			</div>
		</main>
		<script src="{{ asset('themes/default/assets/js/new-order.js?').date('YmdHis') }}"></script>
