<link href="{{ asset('themes/default/assets/css/style.css') }}" rel="stylesheet">
		<main>
			<section class="container py-3 py-md-5">
				<div class="row">
					<div class="col-md-6">
						<img
						src="{{URL::to('themes/default/assets/img/homeImg@2x.png')}}"
						alt="Hero Image"
						class="img-fluid"/>
					</div>
					<div class="col-md-6">
						<p>
							{{ trans('frontend._home._section1._p')}}
						</p>
						<h3>{{ trans('frontend._home._section1._title')}}</h3>
						<p>
							{{ trans('frontend._home._section1._description')}}
						</p>
						<a
							href="{{ URL::to('/new-order/select-billing') }}"
							class="btn btn-lg btn-primary btn-block"
							>{{ trans('frontend._home._section2.place_order') }}</a
						>
					</div>
				</div>
			</section>
			<section id="team" class="text-center text-light py-3 py-md-5">
				<div class="mb-3">
					<h2>{{ trans('frontend._home._section2.meet_the_team') }}</h2>
					<hr />
				</div>
				<div class="container">
					<div class="row">
						<div class="col-6 col-md-3">
							<div class="p-4">
								<img
									src="{{URL::to('themes/default/assets/img/jennG.jpg')}}"
									alt="Jennifer"
									class="img-fluid border border-light rounded-circle"
								/>
							</div>
							<p>Jennifer</p>
						</div>
						<div class="col-6 col-md-3">
							<div class="p-4">
								<img
									src="{{URL::to('themes/default/assets/img/lana.jpg')}}"
									alt="Lana"
									class="img-fluid border border-light rounded-circle"
								/>
							</div>
							<p>Lana</p>
						</div>
						<div class="col-6 col-md-3">
							<div class="p-4">
								<img
									src="{{URL::to('themes/default/assets/img/jessica.jpg')}}"
									alt="Jessica"
									class="img-fluid border border-light rounded-circle"
								/>
							</div>
							<p>Jessica</p>
						</div>
						<div class="col-6 col-md-3">
							<div class="p-4">
								<img
									src="{{URL::to('themes/default/assets/img/matt.jpg')}}"
									alt="Matt"
									class="img-fluid border border-light rounded-circle"
								/>
							</div>
							<p>Matt</p>
						</div>
					</div>
					<button
						type="button"
						data-toggle="modal"
						data-target="#viewTeamModal"
						class="btn btn-light"
					>
					{{ trans('frontend._home._section2.view_more') }}
					</button>
				</div>
			</section>
			<section class="container py-3 py-md-5">
				<div class="row mb-3">
					<div class="col-md-6">
						<h5>{{ trans('frontend._home._section3.1_question') }}</h5>
						<hr />
						<p>
							{{ trans('frontend._home._section3.1_answer') }}
						</p>
					</div>
					<div class="col-md-6">
						<h5>{{ trans('frontend._home._section3.2_question') }}</h5>
						<hr />
						<p>
							{{ trans('frontend._home._section3.2_answer') }}
						</p>
					</div>
				</div>
				<div class="row mb-3">
					<div class="col-md-6">
						<h5>
							{{ trans('frontend._home._section3.3_question') }}
						</h5>
						<hr />
						<p>
							{{ trans('frontend._home._section3.3_answer') }}
						</p>
					</div>
					<div class="col-md-6">
						<h5>{{ trans('frontend._home._section3.4_question') }}</h5>
						<hr />
						<p>
							{{ trans('frontend._home._section3.4_answer') }}
						</p>
					</div>
				</div>
			</section>
			
		</main>
		<!-- Team view more modal -->
		<div
			class="modal fade"
			id="viewTeamModal"
			tabindex="-1"
			aria-labelledby="viewTeamModalLabel"
			aria-hidden="true"
		>
			<div
				class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg"
			>
				<div class="modal-content">
					<div class="modal-header">
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
						<div class="row mb-3">
							<div class="col-md-4">
								<img
									src="{{URL::to('themes/default/assets/img/david.jpg')}}"
									alt="David"
									class="img-fluid rounded-circle"
								/>
							</div>
							<div class="col-md-8">
								<p>張代偉 | David Chang</p>
								<p>
									{{ trans('frontend._home._section2.david_chang')}}
								</p>
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-md-4">
								<img
									src="{{URL::to('themes/default/assets/img/jamie.jpg')}}"
									alt="Jamie"
									class="img-fluid rounded-circle"
								/>
							</div>
							<div class="col-md-8">
								<p>簡暉娟 | Jamie Geng</p>
								<p>
									{{ trans('frontend._home._section2.jamie_geng')}}
								</p>
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-md-4">
								<img
									src="{{URL::to('themes/default/assets/img/matt.jpg')}}"
									alt="Matt"
									class="img-fluid rounded-circle"
								/>
							</div>
							<div class="col-md-8">
								<p>任桐慕 | Matt Nelms</p>
								<p>
									{{ trans('frontend._home._section2.matt_nelms')}}
								</p>
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-md-4">
								<img
									src="{{URL::to('themes/default/assets/img/jennG.jpg')}}"
									alt="Jennifer"
									class="img-fluid rounded-circle"
								/>
							</div>
							<div class="col-md-8">
								<p>遊貞苓 | Jennifer Geng</p>
								<p>
									{{ trans('frontend._home._section2.jennifer_geng')}}
								</p>
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-md-4">
								<img
									src="{{URL::to('themes/default/assets/img/davidchen.jpg')}}"
									alt="David"
									class="img-fluid rounded-circle"
								/>
							</div>
							<div class="col-md-8">
								<p>陳正傑 | David Chen</p>
								<p>
									{{ trans('frontend._home._section2.david_chen')}}
								</p>
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-md-4">
								<img
									src="{{URL::to('themes/default/assets/img/gabor.jpg')}}"
									alt="Gabor"
									class="img-fluid rounded-circle"
								/>
							</div>
							<div class="col-md-8">
								<p>加博 | Gabor Kovács</p>
								<p>
									{{ trans('frontend._home._section2.gabor_kovacs')}}
								</p>
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-md-4">
								<img
									src="{{URL::to('themes/default/assets/img/jessica.jpg')}}"
									alt="Jessica"
									class="img-fluid rounded-circle"
								/>
							</div>
							<div class="col-md-8">
								<p>孫紹真 | Jessica Sun</p>
								<p>
									{{ trans('frontend._home._section2.jessica_sun')}}
								</p>
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-md-4">
								<img
									src="{{URL::to('themes/default/assets/img/jack-lin.png')}}"
									alt="Jack"
									class="img-fluid rounded-circle"
								/>
							</div>
							<div class="col-md-8">
								<p>林士鈞 | Jack Lin</p>
								<p>
									{{ trans('frontend._home._section2.jack_lin')}}
								</p>
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-md-4">
								<img
									src="{{URL::to('themes/default/assets/img/alvin-andryan.png')}}"
									alt="Alvin"
									class="img-fluid rounded-circle"
								/>
							</div>
							<div class="col-md-8">
								<p>彭嘉賓 | Alvin Andryan Phielip</p>
								<p>
									{{ trans('frontend._home._section2.alvin_andryan_phieled')}}
								</p>
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-md-4">
								<img
									src="{{URL::to('themes/default/assets/img/lana.jpg')}}"
									alt="Lana"
									class="img-fluid rounded-circle"
								/>
							</div>
							<div class="col-md-8">
								<p>Lana</p>
								<p>
									{{ trans('frontend._home._section2._lana')}}
								</p>
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-md-4">
								<img
									src="{{URL::to('themes/default/assets/img/hendrik-wu.png')}}"
									alt="Hendrik"
									class="img-fluid rounded-circle"
								/>
							</div>
							<div class="col-md-8">
								<p>吳鼎汶 | Hendrik Wu</p>
								<p>
									{{ trans('frontend._home._section2.hendrik_wu')}}
								</p>
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-md-4">
								<img
									src="{{URL::to('themes/default/assets/img/chandra-zheng.png')}}"
									alt="Chandra"
									class="img-fluid rounded-circle"
								/>
							</div>
							<div class="col-md-8">
								<p>鄭漢泳 | Chandra Zheng</p>
								<p>
									{{ trans('frontend._home._section2.chandra_zheng')}}
								</p>
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-md-4">
								<img
									src="{{URL::to('themes/default/assets/img/anna.jpg')}}"
									alt="Anna"
									class="img-fluid rounded-circle"
								/>
							</div>
							<div class="col-md-8">
								<p>柯安娜 | Anna</p>
								<p>
									{{ trans('frontend._home._section2._anna')}}
								</p>
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-md-4">
								<img
									src="{{URL::to('themes/default/assets/img/edgehill.jpg')}}"
									alt="Manuel"
									class="img-fluid rounded-circle"
								/>
							</div>
							<div class="col-md-8">
								<p>Manuel Edgehill</p>
								<p>
									{{ trans('frontend._home._section2.Manuel_edgehil')}}
								</p>
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-md-4">
								<img
									src="{{URL::to('themes/default/assets/img/lovisa.jpg')}}"
									alt="Lovisa"
									class="img-fluid rounded-circle"
								/>
							</div>
							<div class="col-md-8">
								<p>岸直美 | Lovisa</p>
								<p>
									{{ trans('frontend._home._section2._loviza')}}
								</p>
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-md-4">
								<img
									src="{{URL::to('themes/default/assets/img/jean.jpg')}}"
									alt="Jean"
									class="img-fluid rounded-circle"
								/>
							</div>
							<div class="col-md-8">
								<p>Jean-Baptiste Fichet</p>
								<p>
									{{ trans('frontend._home._section2.jean_baptiste_fichet')}}
								</p>
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-md-4">
								<img
									src="{{URL::to('themes/default/assets/img/ronald-lovel.png')}}"
									alt="Ronald"
									class="img-fluid rounded-circle"
								/>
							</div>
							<div class="col-md-8">
								<p>盧冠達 | Ronald Lovel</p>
								<p>
									{{ trans('frontend._home._section2.ronald_lovel')}}
								</p>
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-md-4">
								<img
									src="{{URL::to('themes/default/assets/img/josefina-chou.png')}}"
									alt="Josefina"
									class="img-fluid rounded-circle"
								/>
							</div>
							<div class="col-md-8">
								<p>周厚瑜 | Josefina Chou</p>
								<p>
									{{ trans('frontend._home._section2.josefina_chou')}}
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		{{-- <script src="{{ asset('themes/default/assets/js/jquery.js') }}"></script>
		<script src="{{ asset('themes/default/assets/js/bootstrap.js') }}"></script> --}}
	