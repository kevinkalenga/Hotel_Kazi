@extends('admin.admin_dashboard')

@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="page-content">
	<!--breadcrumb-->
	<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
		<div class="breadcrumb-title pe-3">Edit Testimonial</div>
		<div class="ps-3">
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb mb-0 p-0">
					<li class="breadcrumb-item">
						<a href="javascript:;"><i class="bx bx-home-alt"></i></a>
					</li>
					<li class="breadcrumb-item active" aria-current="page">
						Edit Testimonial
					</li>
				</ol>
			</nav>
		</div>
	</div>
	<!--end breadcrumb-->

	<div class="container">
		<div class="main-body">
			<div class="row">
				<div class="col-lg-8">
					<form id="myForm" action="{{ route('testimonial.update') }}" method="post" enctype="multipart/form-data">
						@csrf
						<input type="hidden" name="id" value="{{ $testimonial->id }}">
						<div class="card">
							<div class="card-body">

								<!-- Name -->
								<div class="row mb-3">
									<div class="col-sm-3">
										<h6 class="mb-0">Name</h6>
									</div>
									<div class="col-sm-9 text-secondary">
										<input type="text" name="name" class="form-control" value="{{ $testimonial->name }}" />
									</div>
								</div>

								<!-- City -->
								<div class="row mb-3">
									<div class="col-sm-3">
										<h6 class="mb-0">City</h6>
									</div>
									<div class="col-sm-9 text-secondary">
										<input type="text" name="city" class="form-control" value="{{ $testimonial->city }}" />
									</div>
								</div>

								<!-- Message -->
								<div class="row mb-3">
									<div class="col-sm-3">
										<h6 class="mb-0">Message</h6>
									</div>
									<div class="col-sm-9 text-secondary">
										<textarea name="message" class="form-control" placeholder="Message ..." rows="3">{{ $testimonial->message }}</textarea>
									</div>
								</div>

								<!-- Image -->
								<div class="col-lg-12 col-md-6 mb-3">
									<div class="form-group">
										<label>Image</label>
										<input type="file" name="image" class="form-control" id="image" />
									</div>
								</div>

								<!-- Preview -->
								<div class="row mb-3">
									<div class="col-sm-3"></div>
									<div class="col-sm-9 text-secondary">
										<img id="showImage" src="{{ asset($testimonial->image ?? 'upload/default_avatar.jpg') }}"
											class="rounded-circle p-1 bg-primary" width="80">
									</div>
								</div>

								<!-- Submit -->
								<div class="row">
									<div class="col-sm-3"></div>
									<div class="col-sm-9 text-secondary">
										<input type="submit" class="btn btn-primary px-4" value="Update Testimonial" />
									</div>
								</div>

							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Image preview -->
<script type="text/javascript">
$(document).ready(function(){
	$('#image').change(function(e){
		var reader = new FileReader();
		reader.onload = function(e){
			$('#showImage').attr('src', e.target.result);
		}
		reader.readAsDataURL(e.target.files[0]);
	});
});
</script>

<!-- Form validation -->
<script type="text/javascript">
$(document).ready(function () {
	$('#myForm').validate({
		rules: {
			name: { required: true },
			city: { required: true },
			message: { required: true },
			image: {
				extension: "jpg|jpeg|png|webp"
			},
		},
		messages: {
			name: { required: 'Please enter testimonial name' },
			city: { required: 'Please enter the city' },
			message: { required: 'Please enter the message' },
			image: {
				extension: 'Only JPG, JPEG, PNG or WEBP files are allowed',
			},
		},
		errorElement: 'span',
		errorPlacement: function (error, element) {
			error.addClass('invalid-feedback');
			element.after(error);
		},
		highlight: function (element) {
			$(element).addClass('is-invalid');
		},
		unhighlight: function (element) {
			$(element).removeClass('is-invalid');
		},
	});
});
</script>
@endsection