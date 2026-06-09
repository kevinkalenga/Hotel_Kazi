@extends('admin.admin_dashboard')

@section('admin') 

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

<div class="page-content">

    <!-- breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Edit Gallery</div>

        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item">
                        <a href="javascript:;">
                            <i class="bx bx-home-alt"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item active">Edit Gallery</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- end breadcrumb -->

    <div class="container">
        <div class="main-body">
            <div class="row">

                <div class="col-lg-12">
                    <div class="card">

                        <div class="card-body p-4">

                            <form class="row g-3" action="{{ route('update.gallery', $gallery->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                               

                                <!-- Image actuelle -->
                                <div class="col-md-6">
                                    <label class="form-label">Current Image</label><br>

                                    <img src="{{ asset($gallery->photo_name) }}" 
                                         style="width:120px; height:100px; object-fit:cover;">
                                </div>

                                <!-- Nouvelle image -->
                                <div class="col-md-6">
                                    <label for="multiImg" class="form-label">Change Image</label>

                                    <input 
                                        type="file" 
                                        name="photo_name" 
                                        class="form-control" 
                                        id="multiImg"
                                        accept="image/*"
                                    >

                                    <div class="mt-2" id="preview_img"></div>
                                </div>

                                <div class="col-md-12">
                                    <div class="d-md-flex d-grid align-items-center gap-3">
                                        <button type="submit" class="btn btn-primary px-4">
                                            Update Gallery
                                        </button>
                                    </div>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<!-- ===== Preview Image ===== -->
<script>
$(document).ready(function () {

    $('#multiImg').on('change', function () {

        $('#preview_img').html('');

        let file = this.files[0];

        if (file && file.type.match('image.*')) {

            let reader = new FileReader();

            reader.onload = function (e) {

                let img = $('<img>')
                    .attr('src', e.target.result)
                    .css({
                        width: '120px',
                        height: '100px',
                        objectFit: 'cover'
                    });

                $('#preview_img').append(img);
            };

            reader.readAsDataURL(file);

        } else {
            alert('Image invalide');
        }

    });

});
</script>

@endsection