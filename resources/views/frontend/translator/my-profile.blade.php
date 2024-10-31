<header>
	<nav class="navbar navbar-expand-lg navbar-light bg-light">
		<a class="navbar-brand" href="{{ URL::to('/') }}">
			<img
				srcset="
				{{URL::to('themes/default/assets/img/logo-wordcorp_150.png')}},
				{{URL::to('themes/default/assets/img/logo-wordcorp_300.png')}} 2x"

				src="{{URL::to('themes/default/assets/img/logo-wordcorp_150.png')}}"
				alt="Logo"
				class="img-fluid"
			/>
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
					<a class="nav-link" href="{{ URL::to('translator/translator-bin') }}">
						<i class="bi bi-translate"></i> Translation Cases
				</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="{{ URL::to('translator/my-languages') }}">
						<i class="bi bi-globe2"></i> My Languages
					</a>
				</li>
				<li class="nav-item active">
					<a class="nav-link" href="{{ URL::to('translator/my-profile') }}">
						<i class="bi bi-person-fill"></i> My Profile
					</a>
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
					<div class="dropdown-menu dropdown-menu-md-right" aria-labelledby="header-dropdown">
						<a class="dropdown-item" href="{{ URL::to('translator/account/change-password') }}">
							<i class="bi bi-key-fill"></i> Change Password
						</a>
						<a class="dropdown-item" href="{{ URL::to('logout') }}">
							<i class="bi bi-box-arrow-right"></i> Log Out
						</a>
					</div>
				</li>
			</ul>
		</div>
	</nav>
</header>
<main>
	<div class="container py-5">
		<section>
			<div class="table-responsive">
				<div class="mb-3">
					<h3 class="text-center">My Profile</h3>
				</div>
				<table class="table">
					<thead class="thead-light">
						<tr>
							<th scope="col">Period</th>
							<th scope="col">Type</th>
							<th scope="col">Edited (chars/words)</th>
							<th scope="col">Translated (chars/words)</th>
							<th scope="col">Total Earnings (NTD)</th>
						</tr>
					</thead>
					<tbody>
						@if($records)
							@foreach($records as $record)
								<tr>
									<td>
										<span>{{ $record['from'] }}</span>
									</td>
									<td>
										<span>{{ \App\Models\LanguageCombo::where(['code' => $record['lang_combo']])->pluck('name')->first(); }}</span>
									</td>
									<td>
										<span>{{ ($record['edit_word_count']) ?? 0 }}</span>
									</td>
									<td>
										<span>{{ ($record['trans_word_count']) ?? 0 }}</span>
									</td>
									<td>
										<span>{{ ($record['total']) }}</span>
									</td>
								</tr>
							@endforeach
						@else
							<tr>
								<td>
									<h3 class="text-muted">No records</h3>
								</td>
							</tr>
						@endif
					</tbody>
				</table>
			</div>
		</section>
	</div>
</main>
