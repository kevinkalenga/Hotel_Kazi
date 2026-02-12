@extends('admin.admin_dashboard')

@section('admin')

<div class="page-content">


    <!-- new breadcrumb -->

          
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
					
					<div class="ps-3">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb mb-0 p-0">
								<li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
								</li>
								<li class="breadcrumb-item active" aria-current="page">Room Type List</li>
							</ol>
						</nav>
					</div>
					<div class="ms-auto">
						<div class="btn-group">
							 <a href="{{ route('add.room.type') }}" class="btn btn-primary px-5">Add Room Type</a>
							
						</div>
					</div>
			</div>


    <!-- end new breadcrumb -->

  
    <hr/>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allData as $key => $item)
                            @php 
                                $rooms = $item->rooms; // collection des chambres du type
                                $firstRoom = $rooms->first(); // première chambre pour l'image
                            @endphp
                            
                            <!-- Afficher la ligne seulement s'il y a au moins une room -->
                            @if($firstRoom)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        <img style="width:50px; height:30px;" 
                                             src="{{ $firstRoom->image ? asset($firstRoom->image) : asset('upload/default_avatar.jpg') }}" 
                                             alt="">
                                    </td>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        <!-- Boucle sur toutes les rooms pour les actions -->
                                        @foreach($rooms as $roo)
                                            <a href="{{ route('edit.room', $roo->id) }}" class="btn btn-warning px-3 radius-30 me-2">Edit</a>
                                            <a href="{{ route('delete.room', $roo->id) }}" class="btn btn-danger px-3 radius-30 delete-button">Delete</a>
                                        @endforeach
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <hr/>
</div>

@endsection
