  
     
  @php  
  
    $bookArea = App\Models\BookArea::find(1);
  
  @endphp
  
  <div class="book-area-two pt-100 pb-70">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="book-content-two">
                            <div class="section-title">
                                @if($bookArea)
                                    <span class="sp-color">{{ $bookArea->short_title }}</span>
                                    <h2>{{ $bookArea->main_title }}</h2>
                                    <p>{!! $bookArea->short_desc !!}</p>
                                @else
                                    <p>No book area data available</p>
                                @endif
                                
                                @if($bookArea)
                                    <span class="sp-color">{{ $bookArea->short_title }}</span>

                                    <h2>{{ $bookArea->main_title }}</h2>

                                    <p>{!! $bookArea->short_desc !!}</p>

                                    <a href="{{ $bookArea->link_url }}" class="default-btn btn-bg-three">
                                        Quick Booking
                                    </a>
                                @else
                                    <p>No book area data available</p>
                                @endif
                            </div>
                            
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="book-img-2">
                            <img src="{{asset($bookArea->image)}}" alt="Images">
                        </div>
                    </div>
                </div>
            </div>
        </div>