
<header>
	<nav class="navbar navbar-expand-lg navbar-light bg-light">
		<a class="navbar-brand" href="{{ URL::to('/') }}"
			><img
				srcset="
				{{URL::to('themes/default/assets/img/logo-wordcorp_150.png')}},
				{{URL::to('themes/default/assets/img/logo-wordcorp_300.png')}} 2x"

				src="{{URL::to('themes/default/assets/img/logo-wordcorp_150.png')}}"
				alt="Logo"
				class="img-fluid"
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
					<a class="nav-link" href="{{ URL::to('translator/translator-bin') }}"
					><i class="bi bi-translate"></i> Translation Cases</a
					>
				</li>
				<li class="nav-item active">
					<a class="nav-link" href="{{ URL::to('translator/my-languages') }}"
						><i class="bi bi-globe2"></i> My Languages</a
					>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="{{ URL::to('translator/my-profile') }}"
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
					{{ $user->email }}
					</a>
					<div
						class="dropdown-menu dropdown-menu-md-right"
						aria-labelledby="header-dropdown"
					>
					<a class="dropdown-item" href="{{ URL::to('translator/account/change-password') }}"
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
		<!-- Only show pagination if more than one page -->
		@if($lang_combo)
			@if($count > $page_count)
				<nav aria-label="pagination">
					<ul class="pagination justify-content-end">
						<li class="page-prev page-item">
							<a class="page-link" href="#" aria-label="Previous">
								<span aria-hidden="true">&laquo;</span>
							</a>
						</li>
						<?php 
						$index = 0
							?>
						@for ($i = $lang_combo_count; $i > 0; $i--)
							<?php 
							$index = $index + 1;
							?>
								<li class="page-item page-{{$index}}"><a class="page-link" href="?page={{ $index}}">{{ $index}}</a></li>
						@endfor
						<li class="page-next page-item">
							<a class="page-link" href="#" aria-label="Next">
								<span aria-hidden="true">&raquo;</span>
							</a>
						</li>
					</ul>
				</nav>
			@endif
			<section>
				<div class="mb-3">
					<h3 class="text-center">Permissions</h3>
				</div>
				<div class="table-responsive">
					<table class="table">
						<thead class="thead-light">
							<tr>
								<th scope="col">Language Combination</th>
								<th scope="col">Translator Permission</th>
								<th scope="col">Editor Permission</th>
							</tr>
						</thead>
						<tbody>
							@foreach($lang_combo as $data)
							<tr>
								<td>
									{{ \App\Models\LanguageCombo::where(['code' => $data])->pluck('name')->first(); }}
								</td>
								<td>
									@if(strpos($user->language_combination, $data) !== false && ($user->roles == 1 || $user->roles == 3))
									<i class="bi bi-check-circle-fill text-success"></i>
									@else
									<i class="bi bi-x-circle-fill text-muted"></i>
									@endif
								</td>
								<td>
									@if(strpos($user->language_combination, $data) !== false && ($user->roles == 2 || $user->roles == 3))
									<i class="bi bi-check-circle-fill text-success"></i>
									@else
									<i class="bi bi-x-circle-fill text-muted"></i>
									@endif
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</section>
			<!-- Only show pagination if more than one page -->
			@if($count > $page_count)
				<nav aria-label="pagination">
					<ul class="pagination justify-content-end">
						<li class="page-prev page-item">
							<a class="page-link" href="#" aria-label="Previous">
								<span aria-hidden="true">&laquo;</span>
							</a>
						</li>
						<?php 
						$index = 0
							?>
						@for ($i = $lang_combo_count; $i > 0; $i--)
							<?php 
							$index = $index + 1;
							?>
								<li class="page-item page-{{$index}}"><a class="page-link" href="?page={{ $index}}">{{ $index}}</a></li>
						@endfor
						<li class="page-next page-item">
							<a class="page-link" href="#" aria-label="Next">
								<span aria-hidden="true">&raquo;</span>
							</a>
						</li>
					</ul>
				</nav>
			@endif
		@endif
	</div>
</main>
