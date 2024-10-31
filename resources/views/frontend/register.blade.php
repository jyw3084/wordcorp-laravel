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
						<a class="nav-link" href="login">{{ trans('frontend.login_register.login')}}</a>
						<a class="nav-link active" href="register">{{ trans('frontend.login_register.sign_up')}}</a>
					</nav>
					<div
						class="flex-grow-1 d-flex align-items-center justify-content-center p-5">
						<form id="registration-form" class="w-100" method="POST" action='{{URL::to("user/registration")}}'>
							<div class="form-row">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
								<div class="form-group col">
									<label for="name">{{ trans('frontend.login_register.full_name')}}</label>
									<input type="text" name="name" class="form-control" id="name" />
								</div>
								<div class="form-group col">
									<label for="email">{{ trans('frontend.login_register.email')}}</label>
									<input type="email" name="email" class="form-control" id="email" />
								</div>
							</div>
							<div class="form-row">
								<div class="form-group col-md-6">
									<label for="password">{{ trans('frontend.login_register.password')}}</label>
									<input type="password" name="password" class="form-control" id="password" />
								</div>
								<div class="form-group col-md-6">
									<label for="confirm-password">{{ trans('frontend.login_register.confirm_password')}}</label>
									<input name="confirm_password"
										type="password"
										class="form-control"
										id="confirm-password"
									/>
									
								</div>
							</div>
							<button type="submit" class="btn btn-primary">{{ trans('frontend.login_register.sign_up')}}</button>
						</form>
					</div>
					<div class="text-center mb-3">
						<button
							id="become-translator-btn"
							class="text-muted"
							type="button"
							data-toggle="modal"
							data-target="#become-translator-modal"
						>
						{{ trans('frontend.login_register.interrested')}}
						</button>
					</div>
				</div>
			</div>
		</main>
		<!-- Become translator modal -->
		<div
			class="modal fade"
			id="become-translator-modal"
			tabindex="-1"
			aria-labelledby="become-translator-modal-label"
			aria-hidden="true"
		>
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="become-translator-modal-label">
							{{ trans('frontend.login_register.modal_title')}}
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
						<p>
							{{ trans('frontend.login_register.question')}}
						</p>
						<p>{{ trans('frontend.login_register.category')}}</p>
						<ol>
							<li>
								{{ trans('frontend.login_register.category1')}}
							</li>
							<li>
								{{ trans('frontend.login_register.category2')}}
							</li>
							<li>
								{{ trans('frontend.login_register.category3')}}
							</li>
						</ol>
						<p>
							{{ trans('frontend.login_register.please_email_us')}}
							<a href="mailto:service@thewordcorp.com"
								>service@thewordcorp.com</a
							>
							{{ trans('frontend.login_register.with_your')}}
						</p>
					</div>
				</div>
			</div>
		</div>
        <script type="text/javascript" src="{{ asset('themes/default/assets/js/registration.js') }}" ></script>