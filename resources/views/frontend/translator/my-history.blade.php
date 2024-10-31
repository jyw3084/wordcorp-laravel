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
				<li class="nav-item active">
					<a class="nav-link" href="{{ URL::to('translator/translator-bin') }}"
						><i class="bi bi-translate"></i> Translation Cases</a
					>
				</li>
				<li class="nav-item">
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
		<nav class="nav nav-pills flex-column flex-sm-row mb-5">
			<a
				class="flex-sm-fill text-sm-center nav-link"
				href="{{ URL::to('translator/translator-bin') }}"
				>Translator Bin <span class="badge badge-light">{{ $translator_bin_data_count }}</span></a
			>
			@if($user->roles == 3)
			<a
				class="flex-sm-fill text-sm-center nav-link"
				href="{{ URL::to('translator/editor-bin') }}"
				>Editor Bin <span class="badge badge-light">{{ $editor_bin_data_count }}</span></a
			>
			@endif
			<a
				class="flex-sm-fill text-sm-center nav-link"
				href="{{ URL::to('translator/my-translations') }}"
				>My Translations <span class="badge badge-light">{{ $my_translation_data_count }}</span></a
			>
			@if($user->roles == 3)
			<a
				class="flex-sm-fill text-sm-center nav-link"
				href="{{ URL::to('translator/my-editing') }}"
				>My Editing <span class="badge badge-light">{{ $editing_data_count }}</span></a
			>
			@endif
			<a
				class="flex-sm-fill text-sm-center nav-link active"
				href="{{ URL::to('translator/my-history') }}"
				>My History <span class="badge badge-light">{{ $count }}</span></a
			>
		</nav>
		<!-- Only show pagination if more than one page -->
		@if($translator_data)
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
						@for ($i = $translator_count; $i > 0; $i--)
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
			<section class="bin">
				<!-- If cases -->
				@foreach($translator_data as $data)
					<div class="card bg-light mb-3">
						<div class="card-body">
							<div class="row align-items-center mb-3">
								<div class="col">
									<h5>File <i class="bi bi-file-text"></i></h5>
									<!-- Link to Google Docs -->
										@if($data['file'])
											@php
											$idRef = strpos($data['file'], 'id');
											$idRef2 = strpos($data['file'], '&');
											$fileId = substr($data['file'], $idRef+3, $idRef2 - ($idRef+3));
											@endphp
											<a class="file-link" href="https://docs.google.com/document/d/{{$fileId}}/edit" target="_blank">{{ $data['filename'] }}</a>
										@endif
								</div>
							</div>
							<div class="row">
								<div class="col-4">
									<h5>Word Count</h5>
									<p>{{ ($data['word_count']) }}</p>
								</div>
								<div class="col">
									<h5>Service</h5>
									<p>{{ \App\Models\LanguageCombo::where(['code' => $data['language_combination']])->pluck('name')->first(); }}</p>
								</div>
							</div>
							<div class="row">
								<div class="col-4">
									<h5>Expertise</h5>
									<p><span>{{ trans('frontend.order.expertise.'.$data['expertise']) }}</span></p>
								</div>
								<div class="col">
									<h5>Style</h5>
									<p><span>
										@if( $data['style'] == 1 )
											Please perform the translation in a direct manner that accurately conveys the meaning of the source text
										@elseif( $data['style'] == 2 )
											Please use "free style" translation so it sounds good in the target language
										@elseif( $data['style'] == 3 )
											Time is of the essence, please deliver the translation as soon as you can
										@endif
									</span></p>
								</div>
							</div>
							@if($data['type'] == 'translator')
								<div class="row">
									<div class="col-4">
										<h5>Translator Due</h5>
										<p><span>
											@isset($data['translation_deadline'])
												{{ date('F d, Y h:ia', strtotime($data['translation_deadline'])) }}
											@else
												{{ date('F d, Y h:ia', strtotime($data['doc_deadline'])) }}
											@endisset
										</span></p>
									</div>
									<div class="col-4">
										<h5>Translator Fee</h5>
										<p>NTD <span>{{ $data['translator_pay'] }}</span></p>
									</div>
									<div class="col-4">
										<h5>Translator Assigned</h5>
										<p><span>{{ \App\Models\User::where('id',$user->id)->pluck('name')->first(); }}</span></p>
									</div>
								</div>
							@else
								<div class="row">
									<div class="col-4">
										<h5>Editor Due</h5>
										<p><span>
												{{ date('F d, Y h:ia', strtotime($data['doc_deadline'])) }}
										</span></p>
									</div>
									<div class="col-4">
										<h5>Editor Fee</h5>
										<p>NTD <span>{{ $data['editor_pay'] }}</span></p>
									</div>
									<div class="col-4">
										<h5>Editor Assigned</h5>
										<p><span>
										@if(isset($data['editor']))
											{{ \App\Models\User::where('id',$data['editor'])->pluck('name')->first(); }}
										@endif
										</span></p>
									</div>
								</div>
							@endif
						</div>
					</div>
				@endforeach
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
						@for ($i = $translator_count; $i > 0; $i--)
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
		@else
			<!-- If no translation cases -->
			<h3 class="text-muted">No history</h3>
		@endif
	</div>
</main>