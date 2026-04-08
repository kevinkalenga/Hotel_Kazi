@extends('admin.admin_dashboard')

@section('admin') 

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

<div class="page-content">

    <!-- breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Add Gallery</div>

        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item">
                        <a href="javascript:;">
                            <i class="bx bx-home-alt"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Add Gallery
                    </li>
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

                            <form class="row g-3" action="{{ route('store.gallery') }}" method="post" enctype="multipart/form-data">
                                @csrf

                                <div class="col-md-6">
                                    <label for="multiImg" class="form-label">Gallery Image</label>

                                    <input 
                                        type="file" 
                                        name="photo_name[]" 
                                        class="form-control" 
                                        id="multiImg" 
                                        multiple
                                        accept="image/*"
                                    >

                                    <div class="row mt-2" id="preview_img"></div>
                                </div>

                                <div class="col-md-12">
                                    <div class="d-md-flex d-grid align-items-center gap-3">
                                        <button type="submit" class="btn btn-primary px-4">
                                            Save Changes
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

<!-- ===== Show Multi Image Preview ===== -->
<script>
$(document).ready(function () {

    $('#multiImg').on('change', function () {

        // Nettoyer les anciennes images
        $('#preview_img').html('');

        if (window.File && window.FileReader && window.FileList && window.Blob) {

            let files = this.files;

            $.each(files, function (index, file) {

                // Vérifier type image
                if (file.type.match('image.*')) {

                    let reader = new FileReader();

                    reader.onload = function (e) {

                        let img = $('<img>')
                            .addClass('thumb me-2 mb-2')
                            .attr('src', e.target.result)
                            .css({
                                width: '100px',
                                height: '80px',
                                objectFit: 'cover'
                            });

                        $('#preview_img').append(img);
                    };

                    reader.readAsDataURL(file);

                } else {
                    alert('Seules les images sont autorisées !');
                }

            });

        } else {
            alert("Votre navigateur ne supporte pas File API !");
        }

    });

});
</script>

@endsection