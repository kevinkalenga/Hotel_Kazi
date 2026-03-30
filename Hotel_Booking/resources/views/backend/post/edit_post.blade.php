@extends('admin.admin_dashboard')

@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="page-content">
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Edit Blog Post</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Blog Post</li>
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
                            <form class="row g-3" action="{{ route('update.blog.post') }}" method="post" enctype="multipart/form-data">
                                @csrf

                                <input type="hidden" name="id" value="{{ $post->id }}">

                                <!-- Blog Category -->
                                <div class="col-md-6">
                                    <label for="blog_category" class="form-label">Blog Category</label>
                                    <select name="blogcat_id" id="blog_category" class="form-select">
                                        <option selected="">Select Category</option>
                                        @foreach ($blogcat as $cat)
                                            <option value="{{ $cat->id }}" {{ $cat->id == $post->blogcat_id ? 'selected' : '' }}>
                                                {{ $cat->category_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Post Title -->
                                <div class="col-md-6">
                                    <label for="post_title" class="form-label">Post Title</label>
                                    <input type="text" name="post_title" class="form-control" id="post_title" value="{{ $post->post_title }}">
                                </div>

                                <!-- Short Description -->
                                <div class="col-md-12">
                                    <label for="short_descp" class="form-label">Short Description</label>
                                    <textarea name="short_descp" class="form-control" id="short_descp" rows="3">{{ $post->short_descp }}</textarea>
                                </div>

                                <!-- Long Description -->
                                <div class="col-md-12">
                                    <label for="long_descp" class="form-label">Post Description</label>
                                    <textarea name="long_descp" class="form-control" id="myeditorinstance">{!! $post->long_descp !!}</textarea>
                                </div>

                                <!-- Post Image Upload -->
                                <div class="col-md-6">
                                    <label for="image" class="form-label">Post Image</label>
                                    <input class="form-control" name="post_image" type="file" id="image">
                                </div>

                                <!-- Image Preview -->
                                <div class="col-md-6">
                                    <label class="form-label">&nbsp;</label>
                                    <img id="showImage" src="{{ asset($post->post_image) }}" alt="Post Image" class="rounded-circle p-1 bg-primary" width="80">
                                </div>

                                <!-- Submit Button -->
                                <div class="col-md-12">
                                    <div class="d-md-flex d-grid align-items-center gap-3">
                                        <button type="submit" class="btn btn-primary px-4">Submit</button>
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

<!-- Preview Image Script -->
<script type="text/javascript">
    $(document).ready(function() {
        $('#image').change(function(e) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#showImage').attr('src', e.target.result);
            }
            reader.readAsDataURL(e.target.files[0]);
        });
    });
</script>

@endsection