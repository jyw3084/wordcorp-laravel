<link href="{{ asset('themes/default/assets/css/login.css') }}" rel="stylesheet">
		<main>
			<div class="row no-gutters">
				<div class="col-md-4">
					<div class="d-md-none p-5">
						<a href="{{ URL::to('/') }}" class="btn btn-primary w-100"
							><i class="bi bi-arrow-left"></i> {{ trans('frontend.login_register.back')}}</a
						>
					</div>
					<div
						class="d-none d-md-block left-sidebar card bg-dark text-white h-100"
					>
						<div
							class="card-img-overlay d-flex flex-column justify-content-between"
						>
							<a href="{{ URL::to('/') }}" class="btn btn-light"
								><i class="bi bi-arrow-left"></i> {{ trans('frontend.login_register.back')}}</a
							>
							<div class="">
								<h3 class="card-title">
									{{ trans('frontend.login_register.qoute')}}
								</h3>
								<p class="card-text">
									{{ trans('frontend.login_register.description')}}
								</p>
							</div>
							<div class="contact-info card-text">
								<div>
									<div>
										<p>
											<i class="bi bi-mailbox2"></i> service@thewordcorp.com
										</p>
									</div>
									<div>
										<p><i class="bi bi-telephone-fill"></i> 0936-917-369</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-8 d-flex flex-column">
					<div class="logo text-center">
						<img
							src="{{URL::to('themes/default/assets/img/logo-wordcorp_600.png')}}"
							alt="Logo"
							class="img-fluid"
						/>
					</div>
					<nav class="nav nav-justified">
						<a class="nav-link active" href="login">{{ trans('frontend.login_register.login')}}</a>
						<a class="nav-link" href="register">{{ trans('frontend.login_register.sign_up')}}</a>
					</nav>
					<div
						class="flex-grow-1 d-flex align-items-center justify-content-center p-5"
					>
						<form id="login-form" class="w-100" method="POST" action='{{URL::to("authLogin")}}'>
							<input type="hidden" name="_token" value="{{ csrf_token() }}" />
							<div class="form-group">
								<label for="email">{{ trans('frontend.login_register.email')}}</label>
								<input required="true" name="email" type="email" class="form-control" id="email" />
							</div>
							<div class="form-group">
								<label for="password">{{ trans('frontend.login_register.password')}}</label>
								<input required="true" name="password" type="password" class="form-control" id="password" />
								<button
									id="forgot-password-btn"
									type="button"
									data-toggle="modal"
									data-target="#forgot-password-modal"
								>
									<small class="form-text text-muted">{{ trans('frontend.login_register.forgot_password')}}</small>
								</button>
							</div>

							<button type="submit" class="btn btn-primary">{{ trans('frontend.login_register.login')}}</button>
						</form>
					</div>
				</div>
			</div>
		</main>
		<!-- Forgot password modal -->
		<div
			class="modal fade"
			id="forgot-password-modal"
			data-backdrop="static"
			data-keyboard="false"
			tabindex="-1"
			aria-labelledby="forgot-password-modal-label"
			aria-hidden="true"
		>
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="forgot-password-modal-label">
							{{ trans('frontend.login_register.forgot_password_modal_title')}}
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
					<form id="change-password-form" method="POST" action='{{URL::to("/authAjax?type=forgotPassword")}}'>
						<input type="hidden" name="_token" value="{{ csrf_token() }}" />
						<div class="modal-body">
							<p>{{ trans('frontend.login_register.forgot_password_description')}}</p>
								<div class="form-group">
									<input type="email" class="form-control" name="reset_email" id="reset_email" required="true" />
								</div>
								<!-- Password reset email sent -->
								{{-- <small class="form-text text-success">{{ trans('frontend.login_register.forgot_password_success')}}</small
								> --}}
						</div>
						<div class="modal-footer">
							<button
								type="button"
								class="btn btn-secondary"
								data-dismiss="modal"
							>
							{{ trans('frontend.buttons.cancel')}}
							</button>
							<button type="submit" class="btn btn-primary">{{ trans('frontend.buttons.submit')}}</button>
						</div>
					</form>

				</div>
			</div>
		</div>
<script type="text/javascript" src="{{ asset('themes/default/assets/js/login.js?').date('YmdHis') }}" ></script>
