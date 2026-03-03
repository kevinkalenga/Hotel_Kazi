@extends('admin.admin_dashboard')
@section('admin') 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

<div class="page-content">
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Add Room List</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item">
                        <a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Add Room List</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="container">
        <div class="main-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body p-4">
                            <form class="row g-3">
                                <div class="col-md-4">
                                    <label for="roomtype_id" class="form-label">Room Type</label>
                                    <select name="room_id" id="room_id" class="form-select">
                                        <option selected="">Select Room Type </option>
                                        @foreach ($roomtype as $item) 
                                             <option value="{{ $item->id }}" {{ collect(old('roomtype_id'))->contains($item->id) ? 'selected' : '' }}>{{ $item->name }}</option>
                                             
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="input2" class="form-label">Checkin</label>
                                    <input type="date" name="check_in" class="form-control" id="check_in" placeholder="Last Name">
                                </div>

                                <div class="col-md-4">
                                    <label for="input2" class="form-label">CheckOut</label>
                                    <input type="date" name="checkout_out" class="form-control" id="check_out" placeholder="Last Name">

                                </div>
                                <div class="col-md-4">
                                    <label for="input3" class="form-label">Room</label>
                                    <input type="number" name="number_of_rooms" class="form-control" >

                                    <input type="hidden" name="available_room" id="available_room" class="form-control" >
                                    <div class="mt-2">
                                        <label for="">Availability <span class="text-success availability"></span> </label>
                                    </div>

                                </div>

                                <div class="col-md-4">
                                 <label for="input4" class="form-label">Guest</label>
                                 <input type="text" name="number_of_person" class="form-control" id="number_of_person"  >
                                </div>

                                 <h3 class="mt-3 mb-5 text-center">Customer Information </h3>

                                

                                <div class="col-md-4">
                                    <label for="input5" class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name" value="{{old('name')}}">
                                </div>
                                <div class="col-md-4">
                                    <label for="input5" class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" value="{{old('email')}}">
                                </div>
                                <div class="col-md-4">
                                    <label for="input5" class="form-label">Phone</label>
                                    <input type="text" class="form-control" name="phone" value="{{old('phone')}}">
                                </div>
                                <div class="col-md-4">
                                    <label for="input5" class="form-label">Country</label>
                                    <input type="text" class="form-control" name="country" value="{{old('country')}}">
                                </div>
                                <div class="col-md-4">
                                    <label for="input5" class="form-label">State</label>
                                    <input type="text" class="form-control" name="state" value="{{old('state')}}">
                                </div>
                                <div class="col-md-4">
                                    <label for="input5" class="form-label">Zip Code</label>
                                    <input type="text" class="form-control" name="zip_code" value="{{old('zip_code')}}">
                                </div>
                                <div class="col-md-12">
                                    <label for="input11" class="form-label">Address</label>
                                    <textarea name="address" class="form-control"rows="3">
                                        {{old('address')}}
                                    </textarea>
                                </div>

                                

                                <div class="col-md-12">
                                    <div class="d-md-flex d-grid align-items-center gap-3">
                                        <button type="Submit" class="btn btn-primary px-4">Submit</button>
                                        
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
$(document).ready(function () {

    function resetAvailability() {
        $(".availability").text(0);
        $("#available_room").val(0);
    }

    // Reset quand on change le type de chambre
    $("#room_id").on('change', function () {
        resetAvailability();
    });

    // Lancer le calcul quand une date change
    $("#check_in, #check_out").on('change', function () {
        getAvailability();
    });

});

function getAvailability() {

    var roomtype_id = $("#room_id").val();
    var check_in = $("#check_in").val();
    var check_out = $("#check_out").val();

    console.log('roomtype_id:', roomtype_id);
    console.log('check_in:', check_in);
    console.log('check_out:', check_out);

    if (!roomtype_id || !check_in || !check_out) {
        return; // on attend que tout soit rempli
    }

    var startDate = new Date(check_in);
    var endDate = new Date(check_out);

    if (startDate > endDate) {
        alert('Check-out must be after Check-in');
        $("#check_out").val('');
        resetAvailability();
        return;
    }

    $.ajax({
        url: "{{ route('check_room_availability') }}",
        data: {
            roomtype_id: roomtype_id,
            check_in: check_in,
            check_out: check_out
        },
        success: function (data) {
            console.log('response:', data);
            $(".availability").text(data.available_room);
            $("#available_room").val(data.available_room);
        },
        error: function (xhr) {
            console.error('AJAX error:', xhr.responseText);
        }
    });
}
</script>
</div>
@endsection