<main>
	<div class="container py-5">
		<section>
			<div class="table-responsive">
				<div class="mb-3">
					<h3 class="text-center">Payout</h3>
				</div>
				<table class="table">
					<thead class="thead-light">
						<tr>
							<th scope="col">Period</th>
							<th scope="col">Total Translate (words)</th>
							<th scope="col">Total Edit (words)</th>
							<th scope="col">Total Payout (NTD)</th>
							<th scope="col">Details</th>
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
										<span>{{ ($record['trans_word_count']) ?? 0 }}</span>
									</td>
									<td>
										<span>{{ ($record['edit_word_count']) ?? 0 }}</span>
									</td>
									<td>
										<span>{{ ($record['total']) }}</span>
									</td>
									<td>
										<button class="btn btn-info view-details" data-id="{{ strtotime($record['from']) }}">Details</button>
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

		<!-- Modal -->
		<div class="modal fade" id="periodModal" >
			<div class="modal-dialog modal-dialog-centered modal-lg">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">Details</h4>
						<button type="button" class="close" data-dismiss="modal">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
							<table class="table" id="periodTable">
								<thead class="thead-light">
									<tr>
										<th scope="col">Email</th>
										<th scope="col">Translate (words)</th>
										<th scope="col">Edit (words)</th>
										<th scope="col">Payout (NTD)</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
					</div>
					<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>

<script type="text/javascript">
	$(document).ready(function() {
		$('.view-details').on('click', function() {
			var period = $(this).attr('data-id');
			if (period) {
				// AJAX request
				var url = "{{ route('getPayoutDetails',[':period']) }}";
				url = url.replace(':period', period);

				$.ajax({
					url: url,
					dataType: 'json',
					success: function(response) {
						$('#periodTable tbody').html(response.html);
						$('#periodModal').modal('show');
					}
				})
			}
		});
	});
</script>
