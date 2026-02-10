@extends('admin.admin_dashboard')

@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="page-content">
    <div class="container">
        <div class="card">
            <div class="card-body">

                {{-- Tabs --}}
                <ul class="nav nav-tabs nav-primary" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#primaryhome">
                            <i class="bx bx-home me-1"></i> Manage Room
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#primaryprofile">
                            <i class="bx bx-user-pin me-1"></i> Room Number
                        </a>
                    </li>
                </ul>

                <div class="tab-content mt-3">

                    {{-- ================= Manage Room ================= --}}
                    <div class="tab-pane fade show active" id="primaryhome">
                        <div class="col-xl-12 mx-auto">
                            <div class="card">
                                <div class="card-body p-4">
                                    <h5 class="mb-4">Update Room</h5>
                                    <form class="row g-3" action="{{ route('update.room', $editData->id) }}" method="post" enctype="multipart/form-data">
                                        @csrf

                                        {{-- Room Type --}}
                                        <div class="col-md-4">
                                            <label class="form-label">Room Type Name</label>
                                            <input type="hidden" name="roomtype_id" value="{{ $editData->roomtype_id }}">
                                            <input type="text" class="form-control" value="{{ $editData->type->name }}" readonly>
                                        </div>

                                        {{-- Adults / Child --}}
                                        <div class="col-md-4">
                                            <label class="form-label">Total Adult</label>
                                            <input type="text" name="total_adult" class="form-control" value="{{ $editData->total_adult }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Total Child</label>
                                            <input type="text" name="total_child" class="form-control" value="{{ $editData->total_child }}">
                                        </div>

                                        {{-- Main Image --}}
                                        <div class="col-md-6">
                                            <label class="form-label">Main Image</label>
                                            <input type="file" name="image" class="form-control" id="image">
                                            <img id="showImage"
                                                 src="{{ (!empty($editData->image) && file_exists(public_path($editData->image)))
                                                    ? asset($editData->image)
                                                    : asset('upload/default_avatar.jpg') }}"
                                                 width="70" height="50">
                                        </div>

                                        {{-- Gallery Images --}}
                                        <div class="col-md-6">
                                            <label class="form-label">Gallery Image</label>
                                            <input multiple type="file" name="multi_img[]" class="form-control" id="multiImg" accept="image/jpeg, image/jpg, image/gif, image/png">
                                            <div class="row">
                                                @foreach($multiImages as $img)
                                                    <div class="col-md-2 mb-2">
                                                        <img src="{{ asset('upload/rooming/multi_img/'.$img->multi_img) }}" class="img-thumbnail" width="100">
                                                        <a href="{{ route('multi.image.delete', $img->id) }}" onclick="return confirm('Are you sure you want to delete this image?')">
                                                            <li class="lni lni-close"></li>
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="row" id="preview_img"></div>
                                        </div>

                                        {{-- Price, Size, Discount, Capacity --}}
                                        <div class="col-md-3">
                                            <label class="form-label">Room Price</label>
                                            <input type="text" name="price" class="form-control" value="{{ $editData->price }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Size (%)</label>
                                            <input type="text" name="size" class="form-control" value="{{ $editData->size }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Discount (%)</label>
                                            <input type="text" name="discount" class="form-control" value="{{ $editData->discount }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Room Capacity</label>
                                            <input type="text" name="room_capacity" class="form-control" value="{{ $editData->room_capacity }}">
                                        </div>

                                        {{-- Room View --}}
                                        <div class="col-md-6">
                                            <label class="form-label">Room View</label>
                                            <select name="view" class="form-select">
                                                <option selected>Choose...</option>
                                                <option value="Sea View" {{ $editData->view == 'Sea View' ? 'selected' : '' }}>Sea View</option>
                                                <option value="Hill View" {{ $editData->view == 'Hill View' ? 'selected' : '' }}>Hill View</option>
                                            </select>
                                        </div>

                                        {{-- Bed Style --}}
                                        <div class="col-md-6">
                                            <label class="form-label">Bed Style</label>
                                            <select name="bed_style" class="form-select">
                                                <option selected>Choose...</option>
                                                <option value="Queen Bed" {{ $editData->bed_style == 'Queen Bed' ? 'selected' : '' }}>Queen Bed</option>
                                                <option value="Twin Bed" {{ $editData->bed_style == 'Twin Bed' ? 'selected' : '' }}>Twin Bed</option>
                                                <option value="King Bed" {{ $editData->bed_style == 'King Bed' ? 'selected' : '' }}>King Bed</option>
                                            </select>
                                        </div>

                                        {{-- Short Description --}}
                                        <div class="col-md-12">
                                            <label class="form-label">Short Description</label>
                                            <textarea name="short_desc" class="form-control" rows="3">{{ $editData->short_desc }}</textarea>
                                        </div>

                                        {{-- Description --}}
                                        <div class="col-md-12">
                                            <label class="form-label">Description</label>
                                            <textarea name="description" class="form-control" id="myeditorinstance" rows="3">{!! $editData->description !!}</textarea>
                                        </div>

                                        {{-- Room Facilities --}}
                                        @php
                                            $allFacilities = [
                                                'Complimentary Breakfast','32/42 inch LED TV','Smoke alarms','Minibar','Work Desk',
                                                'Free Wi-Fi','Safety box','Rain Shower','Slippers','Hair dryer','Wake-up service',
                                                'Laundry & Dry Cleaning','Electronic door lock'
                                            ];
                                        @endphp

                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Room Facilities</label>
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
                                                            <button type="button" class="btn btn-success add_facility me-1"><i class="lni lni-circle-plus"></i></button>
                                                            <button type="button" class="btn btn-danger remove_facility"><i class="lni lni-circle-minus"></i></button>
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
                                                            <button type="button" class="btn btn-success add_facility me-1"><i class="lni lni-circle-plus"></i></button>
                                                            <button type="button" class="btn btn-danger remove_facility"><i class="lni lni-circle-minus"></i></button>
                                                        </div>
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>

                                        {{-- Hidden Facility Template --}}
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

                                        {{-- Submit --}}
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-primary px-4">Change Save</button>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ================= Room Number ================= --}}
                    <div class="tab-pane fade" id="primaryprofile">
                        <div class="card">
                            <div class="card-body">
                                <button id="addRoomNoBtn" class="btn btn-primary mb-3">
                                    <i class="lni lni-plus"></i> Add New
                                </button>

                                <div id="roomnoHide" style="display:none;">
                                    <form action="#" method="POST" class="row g-3">
                                        @csrf
                                        <input type="hidden" name="room_id" value="{{ $editData->id }}">
                                        <div class="col-md-4">
                                            <label class="form-label">Room No</label>
                                            <input type="text" name="room_no" class="form-control" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-select" required>
                                                <option value="">Select</option>
                                                <option value="Active">Active</option>
                                                <option value="Inactive">Inactive</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 d-flex align-items-end">
                                            <button type="submit" class="btn btn-success me-2">Save</button>
                                            <button type="button" id="cancelRoomNo" class="btn btn-secondary">Cancel</button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- ================= JS ================= --}}
<script>
$(document).ready(function () {

    // Show main image preview
    $('#image').change(function(e){
        var reader = new FileReader();
        reader.onload = function(e){
            $("#showImage").attr('src', e.target.result)
        }
        reader.readAsDataURL(e.target.files[0])
    });

    // Show multiple images preview
    $('#multiImg').on('change', function(){
        $('#preview_img').html('');
        if (window.File && window.FileReader) {
            var files = this.files;
            $.each(files, function(index, file){
                if(/(\.|\/)(gif|jpe?g|png)$/i.test(file.type)){
                    var fRead = new FileReader();
                    fRead.onload = (function(file){
                        return function(e){
                            $('<img/>', {src:e.target.result, class:'thumb me-2 mb-2', width:100, height:80}).appendTo('#preview_img');
                        }
                    })(file);
                    fRead.readAsDataURL(file);
                }
            });
        } else {
            alert("Your browser doesn't support File API!");
        }
    });

    // Facility add/remove
    $(document).on('click', '.add_facility', function(){
        let newFacility = $('#facility_template').html();
        $('#facility_wrapper').append(newFacility);
    });
    $(document).on('click', '.remove_facility', function(){
        $(this).closest('.facility_item').remove();
    });

    // Add / Hide Room Number Form
    $('#addRoomNoBtn').on('click', function () {
        $('#roomnoHide').slideDown();
        $(this).hide();
    });
    $('#cancelRoomNo').on('click', function () {
        $('#roomnoHide').slideUp();
        $('#addRoomNoBtn').show();
    });

});
</script>
@endsection
