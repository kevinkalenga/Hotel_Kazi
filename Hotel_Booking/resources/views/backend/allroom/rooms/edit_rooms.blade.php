@extends('admin.admin_dashboard')

@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

	<div class="page-content">
			
				
				<div class="container">
					<div class="main-body">
						<div class="row">
							
							
						
					         <div class="card">
							<div class="card-body">
								<ul class="nav nav-tabs nav-primary" role="tablist">
									<li class="nav-item" role="presentation">
										<a class="nav-link active" data-bs-toggle="tab" href="#primaryhome" role="tab" aria-selected="true">
											<div class="d-flex align-items-center">
												<div class="tab-icon"><i class="bx bx-home font-18 me-1"></i>
												</div>
												<div class="tab-title">Manage Room</div>
											</div>
										</a>
									</li>
									<li class="nav-item" role="presentation">
										<a class="nav-link" data-bs-toggle="tab" href="#primaryprofile" role="tab" aria-selected="false" tabindex="-1">
											<div class="d-flex align-items-center">
												<div class="tab-icon"><i class="bx bx-user-pin font-18 me-1"></i>
												</div>
												<div class="tab-title">Room Number</div>
											</div>
										</a>
									</li>
									
								</ul>
								<div class="tab-content py-3">
									<div class="tab-pane fade show active" id="primaryhome" role="tabpanel">
														   <div class="col-xl-12 mx-auto">
						
						<div class="card">
							<div class="card-body p-4">
								<h5 class="mb-4">Update Room</h5>
								<form class="row g-3" action="{{route('update.room', $editData->id)}}" method="post" 
								       enctype="multipart/form-data">
									   @csrf
									<div class="col-md-4">
										<label for="input1" class="form-label">Room Type Name</label>
										<input type="hidden" name="roomtype_id" value="{{ $editData->roomtype_id }}">
                                        <input type="text" class="form-control" value="{{ $editData->type->name }}" readonly>

									</div>
									<div class="col-md-4">
										<label for="input2" class="form-label">Total Adult</label>
										<input type="text" name="total_adult" class="form-control" id="input2" value="{{$editData->total_adult}}">
									</div>
									<div class="col-md-4">
										<label for="input2" class="form-label">Total Child</label>
										<input type="text" name="total_child" class="form-control" id="input2" value="{{$editData->total_child}}">
									</div>
									<div class="col-md-6">
										<label for="input3" class="form-label">Main Image</label>
										<input type="file" name="image" class="form-control" id="image" placeholder="Phone">
										 <img id="showImage"
                                           src="{{ (!empty($editData->image) && file_exists(public_path($editData->image)))
                                              ? asset($editData->image)
                                              : asset('upload/default_avatar.jpg') }}"
                                           width="60">


									</div>
									<div class="col-md-6">
										<label for="input4" class="form-label">Gallery Image</label>
										<input multiple type="file" name="multi_img[]" class="form-control" id="multiImg" accept="image/jpeg, image/jpg, image/gif, image/png">

										  <div class="row">
                                             @foreach($multiImages as $img)
                                                 <div class="col-md-2 mb-2">
                                                     <img src="{{ asset('upload/rooming/multi_img/'.$img->multi_img) }}"
                                                          class="img-thumbnail" width="100">
                                                 </div>
                                             @endforeach
                                            </div>
										
										 <div class="row" id="preview_img"></div>
									</div>
									
									<div class="col-md-3">
										<label for="input1" class="form-label">Room Price</label>
										<input type="text" name="price" class="form-control" id="input1" value="{{$editData->price}}">
									</div>
									<div class="col-md-3">
										<label for="input2" class="form-label">Size (%)</label>
										<input type="text" name="size" class="form-control" id="input2" value="{{$editData->size}}">
									</div>
									<div class="col-md-3">
										<label for="input2" class="form-label">Discount (%)</label>
										<input type="text" name="discount" class="form-control" id="input2" value="{{$editData->discount}}">
									</div>
									<div class="col-md-3">
										<label for="input2" class="form-label">Room Capacity</label>
										<input type="text" name="room_capacity" class="form-control" id="input2" value="{{$editData->room_capacity}}">
									</div>
									
									<div class="col-md-6">
										<label for="input7" class="form-label">Room View</label>
										<select name="view" id="input7" class="form-select">
											<option selected="">Choose...</option>
										    <option value="Sea View" {{ $editData->view == 'Sea View' ? 'selected' : '' }}>Sea View</option>
                                            <option value="Hill View" {{ $editData->view == 'Hill View' ? 'selected' : '' }}>Hill View</option>
											
										</select>
									</div>
									<div class="col-md-6">
										<label for="input7" class="form-label">Bed Style</label>
										<select name="bed_style" id="input7" class="form-select">
											<option selected="">Choose...</option>
											<option value="Queen Bed" {{ $editData->bed_style == 'Queen Bed' ? 'selected' : '' }}>Queen Bed</option>
                                            <option value="Twin Bed" {{ $editData->bed_style == 'Twin Bed' ? 'selected' : '' }}>Twin Bed</option>
                                            <option value="King Bed" {{ $editData->bed_style == 'King Bed' ? 'selected' : '' }}>King Bed</option>
										</select>
									</div>
									
									
									<div class="col-md-12">
										<label for="input11" class="form-label">Short Description</label>
										<textarea name="short_desc" class="form-control" id="input11" rows="3">
											{{$editData->short_desc}}
										</textarea>
									</div>
									<div class="col-md-12">
										<label for="input11" class="form-label">Description</label>
										<textarea name="description" class="form-control" id="myeditorinstance" rows="3">
											{!! $editData->description !!}
										</textarea>
									</div>
									
									    <div class="row mt-2">
 
	
		                   @php
    $allFacilities = [
        'Complimentary Breakfast',
        '32/42 inch LED TV',
        'Smoke alarms',
        'Minibar',
        'Work Desk',
        'Free Wi-Fi',
        'Safety box',
        'Rain Shower',
        'Slippers',
        'Hair dryer',
        'Wake-up service',
        'Laundry & Dry Cleaning',
        'Electronic door lock'
    ];
@endphp
	

									

<div class="col-md-12 mb-3">
    <label for="facility_name" class="form-label">Room Facilities</label>

    <div id="facility_wrapper">
        @forelse ($basic_facility as $facility)
        <div class="facility_item row mb-2">
            <div class="col-md-10">
                <select name="facility_name[]" class="form-control">
                    <option value="">Select Facility</option>
                    @foreach($allFacilities as $f)
                        <option value="{{ $f }}" {{ $f == $facility ? 'selected' : '' }}>{{ $f }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-center">
                <button type="button" class="btn btn-success add_facility me-1">
                    <i class="lni lni-circle-plus"></i>
                </button>
                <button type="button" class="btn btn-danger remove_facility">
                    <i class="lni lni-circle-minus"></i>
                </button>
            </div>
        </div>
        @empty
        <div class="facility_item row mb-2">
            <div class="col-md-10">
                <select name="facility_name[]" class="form-control">
                    <option value="">Select Facility</option>
                    @foreach($allFacilities as $f)
                        <option value="{{ $f }}">{{ $f }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-center">
                <button type="button" class="btn btn-success add_facility me-1">
                    <i class="lni lni-circle-plus"></i>
                </button>
                <button type="button" class="btn btn-danger remove_facility">
                    <i class="lni lni-circle-minus"></i>
                </button>
            </div>
        </div>
        @endforelse
    </div>
</div>

{{-- Hidden template for cloning --}}
<div id="facility_template" class="d-none">
    <div class="facility_item row mb-2">
        <div class="col-md-10">
            <select name="facility_name[]" class="form-control">
                <option value="">Select Facility</option>
                @foreach($allFacilities as $f)
                    <option value="{{ $f }}">{{ $f }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-center">
            <button type="button" class="btn btn-success add_facility me-1">
                <i class="lni lni-circle-plus"></i>
            </button>
            <button type="button" class="btn btn-danger remove_facility">
                <i class="lni lni-circle-minus"></i>
            </button>
        </div>
    </div>
</div>

{{-- Add / Remove Facilities --}}
<script>
$(document).ready(function () {
    // Add new facility
    $(document).on('click', '.add_facility', function () {
        let newFacility = $('#facility_template').html();
        $('#facility_wrapper').append(newFacility);
    });

    // Remove facility
    $(document).on('click', '.remove_facility', function () {
        $(this).closest('.facility_item').remove();
    });
});
</script>

									
									





{{-- Hidden template for cloning --}}
<div id="facility_template" class="d-none">
    <div class="facility_item row mb-2">
        <div class="col-md-10">
            <select name="facility_name[]" class="form-control">
                <option value="">Select Facility</option>
                @foreach($allFacilities as $f)
                    <option value="{{ $f }}">{{ $f }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-center">
            <button type="button" class="btn btn-success add_facility me-1"><i class="lni lni-circle-plus"></i></button>
            <button type="button" class="btn btn-danger remove_facility"><i class="lni lni-circle-minus"></i></button>
        </div>
    </div>
</div>									
									
									
									
									
									
									
									
									<div class="col-md-12">
										<div class="d-md-flex d-grid align-items-center gap-3">
											<button type="submit" class="btn btn-primary px-4">Change Save</button>
											
										</div>
									</div>
								</form>
							</div>
						</div>

						


					</div>
									
									
									
									
									
									</div>
									
					
									
									
									
									
									
									
									
									<div class="tab-pane fade" id="primaryprofile" role="tabpanel">
										<p>Food truck fixie locavore, accusamus mcsweeney's marfa nulla single-origin coffee squid. Exercitation +1 labore velit, blog sartorial PBR leggings next level wes anderson artisan four loko farm-to-table craft beer twee. Qui photo booth letterpress, commodo enim craft beer mlkshk aliquip jean shorts ullamco ad vinyl cillum PBR. Homo nostrud organic, assumenda labore aesthetic magna delectus mollit. Keytar helvetica VHS salvia yr, vero magna velit sapiente labore stumptown. Vegan fanny pack odio cillum wes anderson 8-bit, sustainable jean shorts beard ut DIY ethical culpa terry richardson biodiesel. Art party scenester stumptown, tumblr butcher vero sint qui sapiente accusamus tattooed echo park.</p>
									</div>
									
								</div>
							</div>
						</div>
					
					
					    
					
					
					
					    </div>
					</div>
				</div>
	</div>


    	<script type="text/javascript">
               $(document).ready(function(){
				$('#image').change(function(e){
					var reader = new FileReader();
					reader.onload = function(e){
						$("#showImage").attr('src', e.target.result)
					}
					reader.readAsDataURL(e.target.files['0'])
				})
			   })
		</script>


        <!--------===Show MultiImage ========------->
<script>
   $(document).ready(function(){
    $('#multiImg').on('change', function(){ //on file input change
       if (window.File && window.FileReader && window.FileList && window.Blob) //check File API supported browser
       {
           var data = $(this)[0].files; //this file data
            
           $.each(data, function(index, file){ //loop though each file
               if(/(\.|\/)(gif|jpe?g|png)$/i.test(file.type)){ //check supported file type
                   var fRead = new FileReader(); //new filereader
                   fRead.onload = (function(file){ //trigger function on successful read
                   return function(e) {
                       var img = $('<img/>').addClass('thumb').attr('src', e.target.result) .width(100)
                   .height(80); //create image element 
                       $('#preview_img').append(img); //append image to output element
                   };
                   })(file);
                   fRead.readAsDataURL(file); //URL representing the file's data.
               }
           });
            
       }else{
           alert("Your browser doesn't support File API!"); //if File API is absent
       }
    });
   });
</script>


<!--========== Start of add Basic Plan Facilities ==============-->

<!--========== End of Basic Plan Facilities ==============-->


@endsection