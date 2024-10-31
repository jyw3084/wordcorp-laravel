
<header>
	<nav class="navbar navbar-expand-lg navbar-light bg-light">
		<a class="navbar-brand" href="#"
			><img
				srcset="
				{{URL::to('themes/default/assets/img/logo-wordcorp_150.png')}},
				{{URL::to('themes/default/assets/img/logo-wordcorp_300.png')}} 2x"
				
				src="{{URL::to('themes/default/assets/img/logo-wordcorp_150.png')}}"
				alt="Logo"
				class="img-fluid"/>
		/></a>
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
					<a class="nav-link" href="./translator-bin.html"
						><i class="bi bi-translate"></i> Translation Cases</a
					>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="./my-languages.html"
						><i class="bi bi-globe2"></i> My Languages</a
					>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="./my-profile.html"
						><i class="bi bi-person-fill"></i> My Profile</a
					>
				</li>
				<li class="nav-item dropdown">
					<a
						class="nav-link dropdown-toggle"
						href="#"
						id="header-dropdown"
						role="button"
						data-toggle="dropdown"
						aria-haspopup="true"
						aria-expanded="false"
					>
						user@email.com
					</a>
					<div
						class="dropdown-menu dropdown-menu-md-right"
						aria-labelledby="header-dropdown"
					>
						<a class="dropdown-item active" href="translator/my-languages"
							><i class="bi bi-key-fill"></i> Change Password</a
						>
						<a class="dropdown-item" href="{{ URL::to('logout') }}"
							><i class="bi bi-box-arrow-right"></i> Log Out</a
						>
					</div>
				</li>
			</ul>
		</div>
	</nav>
</header>
<main>
	<div class="container py-5">
		<section>
			<div class="mb-3">
				<h3 class="text-center mb-5">Change Password</h3>
				<div class="row">
					<div class="col-md-6 offset-md-3">
						<form id="change-password-form" method="POST" action='{{URL::to("/authAjax?type=changePassword")}}'>
							<input type="hidden" name="_token" value="{{ csrf_token() }}" />
							<div class="form-group">
								<label for="oldPassword">Current Password</label>
								<input
									type="password"
									class="form-control"
									id="oldPassword"
									name="oldPassword"
								/>
								<!-- If password incorrect -->
								<!-- <small id="wrongPassword" class="form-text text-danger mb-1"
									>Wrong password</small
								> -->
							</div>
							<div class="form-group">
								<label for="newPassword">New Password</label>
								<input
									type="password"
									class="form-control"
									id="newPassword"
									name="newPassword"
								/>
							</div>
							<div class="form-group">
								<label for="confirmPassword">Confirm New Password</label>
								<input
									type="password"
									class="form-control"
									id="confirmPassword"
									name="confirmPassword"

								/>
								<!-- If confirm password doesn't match -->
								<!-- <small class="form-text text-danger mb-1"
									>Password doesn't match</small
								> -->
							</div>
							<div class="text-center">
								<a href="#back" class="btn btn-secondary">
									Cancel
								</a>
								<button type="submit" class="btn btn-primary">
									Update
								</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</section>
	</div>
</main>
<!-- Change password successful modal -->
<div
	class="modal fade"
	id="change-success-modal"
	tabindex="-1"
	aria-labelledby="change-success-modal-label"
	aria-hidden="true"
>
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-body">
				<h5>
					<i class="bi bi-check2-circle text-success"></i> Password updated!
				</h5>
				<p>
					You can now use your new password to log in to your dashboard.
				</p>
			</div>
			<div class="modal-footer">
				<a href="{{ URL::to('logout') }}" class="btn btn-primary">
					Go to Log In
				</a>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="{{ asset('themes/default/assets/js/changepassword.js') }}" ></script>
