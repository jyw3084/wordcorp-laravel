<header>
			<nav class="navbar navbar-expand-lg navbar-light bg-light">
				<a class="navbar-brand" href="{{ URL::to('/') }}">
					<img
						srcset="
						{{URL::to('themes/default/assets/img/logo-wordcorp_150.png')}},
						{{URL::to('themes/default/assets/img/logo-wordcorp_300.png')}} 2x"

						src="{{URL::to('themes/default/assets/img/logo-wordcorp_150.png')}}"
						alt="Logo"
						class="img-fluid"/>
				</a>
				<button
					class="navbar-toggler"
					type="button"
					data-toggle="collapse"
					data-target="#header-menu"
					aria-controls="header-menu"
					aria-expanded="false"
					aria-label="Toggle navigation"
				>
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="header-menu">
					<ul class="navbar-nav ml-auto">
						<li class="nav-item">
							<a class="nav-link" href="{{URL::to('faq')}}"
								><i class="bi bi-question-circle-fill"></i> {{ trans('frontend._header._faq') }}</a
							>
						</li>
						<li class="nav-item">
							<!-- If user not logged in -->
							@if(!auth()->check())
							<a class="nav-link" href="{{URL::to('login')}}"
								><i class="bi bi-door-open-fill"></i>{{ trans('frontend._header._translator_login') }}</a
							>
							@else
							<!-- If user logged in -->
							<a class="nav-link" href="/login"
								><i class="bi bi-door-open-fill"></i> Dashboard</a
							>
							@endif
						</li>
						<li class="nav-item dropdown">
							<a
								class="nav-link dropdown-toggle"
								href="{{ route('locale.setting', 'en') }}"
								id="navbarDropdown"
								role="button"
								data-toggle="dropdown"
								aria-haspopup="true"
								aria-expanded="false"
							>
							{{ App::getLocale() == 'zh' ? '中文' : 'English'}}
							</a>
							<div
								class="dropdown-menu dropdown-menu-md-right"
								aria-labelledby="navbarDropdown"
							>
							@if( App::getLocale() == 'en')
								<a class="dropdown-item" href="{{ route('locale.setting', 'zh') }}">中文</a>
							</div>
							@elseif(App::getLocale() == 'zh')
								<a class="dropdown-item" href="{{ route('locale.setting', 'en') }}">English</a>
							@endif
						</li>
					</ul>
				</div>
			</nav>
			<div class="d-sm-flex justify-content-end py-3">
				<div>
					<span class="mx-3"
						><i class="bi bi-mailbox2"></i> service@thewordcorp.com</span
					>
				</div>
				<div>
					<span class="mx-3"
						><i class="bi bi-telephone-fill"></i> 0936-917-369</span
					>
				</div>
			</div>
		</header>
