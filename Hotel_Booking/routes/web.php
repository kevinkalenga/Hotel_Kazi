<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Backend\TeamController;
use App\Http\Controllers\Backend\RoomTypeController;
use App\Http\Controllers\Backend\RoomController;
use App\Http\Controllers\Backend\RoomListController;
use App\Http\Controllers\Backend\SettingController;
use App\Http\Controllers\Backend\TestimonialController;
use App\Http\Controllers\Backend\BlogController;
use App\Http\Controllers\Backend\CommentController;
use App\Http\Controllers\Backend\ReportController;
use App\Http\Controllers\Backend\GalleryController;
use App\Http\Controllers\Backend\ContactController;

use App\Http\Controllers\Frontend\FrontendRoomController;
use App\Http\Controllers\Frontend\BookingController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [UserController::class, 'index'])->name('index');

Route::get('/dashboard', function () {
    return view('frontend.dashboard.user_dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserController::class, 'userProfile'])->name('user.profile');
    Route::post('/profile/store', [UserController::class, 'userProfileStore'])->name('profile.store');
    Route::get('/user/logout', [UserController::class, 'userLogout'])->name('user.logout');
    Route::get('/user/change/password', [UserController::class, 'userChangePassword'])->name('user.change.password');
    Route::post('/password/change/store', [UserController::class, 'changePasswordStore'])->name('password.change.store');
    
});

require __DIR__.'/auth.php';

// Admin Groupe Middleware

Route::middleware(['auth',  'adminRole:admin'])->group(function() {
    Route::get('/admin/dashboard', [AdminController::class, 'AdminDashboard'])->name('admin.dashboard');
    Route::get('/admin/logout', [AdminController::class, 'AdminLogout'])->name('admin.logout');
    Route::get('/admin/profile', [AdminController::class, 'AdminProfile'])->name('admin.profile');
    Route::post('/admin/profile/store', [AdminController::class, 'AdminProfileStore'])->name('admin.profile.store');
    Route::get('/admin/change/password', [AdminController::class, 'AdminChangePassword'])->name('admin.change.profile');
    Route::post('/admin/password/update', [AdminController::class, 'AdminPasswordUpdate'])->name('admin.password.update');
});

// Test
Route::group(['middleware' => 'guest'], function () {
    Route::get('/admin/login', [AdminController::class, 'AdminLogin'])
        ->name('admin.login');
});





Route::middleware(['auth',  'adminRole:admin'])->group(function() {

    // Team All Routes

    Route::controller(TeamController::class)->group(function() {
        Route::get('/all/team', 'AllTeam')->name('all.team');
        Route::get('/add/team', 'AddTeam')->name('add.team');
        Route::post('/team/store', 'StoreTeam')->name('team.store');
        Route::get('/edit/team/{id}', 'EditTeam')->name('edit.team');
        Route::post('/team/update', 'UpdateTeam')->name('team.update');
        Route::get('/team/delete/{id}', 'DeleteTeam')->name('delete.team');
    });
    // Book Area All Routes

    Route::controller(TeamController::class)->group(function() {
        Route::get('/book/area', 'BookArea')->name('book.area');
        Route::post('/book/area/update', 'BookAreaUpdate')->name('book.area.update');
      
    });
    // RoomType All Routes

    Route::controller(RoomTypeController::class)->group(function() {
       
        Route::get('/room/type/list', 'RoomTypeList')->name('room.type.list');
        Route::get('/add/room/type', 'AddRoomType')->name('add.room.type');
        Route::post('/room/type/store', 'RoomTypeStore')->name('room.type.store');
      
    });
    // Room All Routes

    Route::controller(RoomController::class)->group(function() {
       
        Route::get('/edit/room/{id}', 'EditRoom')->name('edit.room');
        Route::post('/update/room/{id}', 'updateRoom')->name('update.room');
        Route::get('/multi/image/delete/{id}', 'MultiImageDelete')->name('multi.image.delete');
        Route::post('/store/room/no/{id}', 'StoreRoomNumber')->name('store.room.no');
        Route::get('/edit/roomno/{id}', 'EditRoomNumber')->name('edit.roomno');
        Route::post('/update/roomno/{id}', 'UpdateRoomNumber')->name('update.roomno');
        Route::get('/delete/roomno/{id}', 'DeleteRoomNumber')->name('delete.roomno');
        Route::get('/delete/room/{id}', 'DeleteRoom')->name('delete.room');
       
      
    });

    // Admin Booking All Routes

    Route::controller(BookingController::class)->group(function() {
       
        Route::get('/booking/list', 'BookingList')->name('booking.list');
        Route::get('/edit_booking/{id}', 'EditBooking')->name('edit.booking');
        Route::get('/download/invoice/{id}', 'DownloadInvoice')->name('download.invoice');
      
      
    });

});


// Room All Routes

Route::controller(FrontendRoomController::class)->group(function() {
       
        Route::get('/rooms', 'AllFrontendRoomList')->name('froom.all');
        Route::get('/room/details/{id}', 'RoomDetailsPage'); // on utilise url on aura pas besoin de nom de la route
        Route::get('/bookings', 'BookingSearch')->name('booking.search'); 
        Route::get('/search/room/details/{id}', 'SearchRoomDetails')->name('search_room_details');
        Route::get('/check_room_availability', 'CheckRoomAvailability')->name('check_room_availability'); 
     
       
});

// Auth middleware User must be logged in so as to access this route
Route::middleware(['auth'])->group(function() {
    // Checkout All routes
    Route::controller(BookingController::class)->group(function() {
       
        Route::get('/checkout', 'Checkout')->name('checkout');
        Route::post('/booking/store', 'BookingStore')->name('user_booking_store');
        Route::post('/checkout/store', 'CheckoutStore')->name('checkout.store');
        Route::match(['get', 'post'],'/stripe_pay', [BookingController::class, 'stripe_pay'])->name('stripe_pay');

        // booking Update
        Route::post('/update/booking/status/{id}', 'UpdateBookingStatus')->name('update.booking.status');
        Route::post('/update/booking/{id}', 'UpdateBooking')->name('update.booking');
        
        // Assign Room Route
        Route::get('/assign_room/{id}', 'AssignRoom')->name('assign_room');
        Route::get('/assign_room/store/{booking_id}/{room_number_id}', 'AssignRoomStore')->name('assign_room_store');
        Route::get('/assign_room_delete/{id}', 'AssignRoomDelete')->name('assign_room_delete');

        // User Booking Route 
        Route::get('/user/booking', 'UserBooking')->name('user.booking');
        Route::get('/user/invoice/{id}', 'UserInvoice')->name('user.invoice');
       
    });

});


Route::middleware(['auth', 'adminRole:admin'])->group(function () {
    // Admin RoomList
    Route::controller(RoomListController::class)->group(function() {
       
      Route::get('/view/room/list', 'ViewRoomList')->name('view.room.list');
      Route::get('/add/room/list', 'AddRoomList')->name('add.room.list');
      Route::post('/store/roomlist', 'StoreRoomList')->name('store.roomlist'); 
    
   });
   // Admin Setting Smtp Routes
    Route::controller(SettingController::class)->group(function() {
       
      Route::get('/smtp/setting', 'SmtpSetting')->name('smtp.setting');
      Route::post('/smtp/update', 'SmtpUpdate')->name('smtp.update');
     
    
   });
    // Admin Testimonial Routes
   Route::controller(TestimonialController::class)->group(function() {
       
      Route::get('/all/testimonial', 'AllTestimonial')->name('all.testimonial');
      Route::get('/add/testimonial', 'AddTestimonial')->name('add.testimonial');
      Route::post('/store/testimonial', 'StoreTestimonial')->name('testimonial.store'); 
      Route::get('/edit/testimonial/{id}', 'EditTestimonial')->name('edit.testimonial'); 
      Route::post('/update/testimonial', 'UpdateTestimonial')->name('testimonial.update');
      Route::get('/delete/testimonial/{id}', 'DeleteTestimonial')->name('delete.testimonial');
     
    
   });
    // Admin Blog Category Routes
   Route::controller(BlogController::class)->group(function() {
       
      Route::get('/blog/category', 'BlogCategory')->name('blog.category');
      Route::post('/store/blog/category', 'StoreBlogCategory')->name('store.blog.category');
      Route::get('/edit/blog/category/{id}', 'EditBlogCategory');
      Route::post('/update/blog/category/', 'UpdateBlogCategory')->name('update.blog.category');
      Route::get('/delete/blog/category/{id}', 'DeleteBlogCategory')->name('delete.blog.category');
     
    
   });
    // All Blog Post Routes
   Route::controller(BlogController::class)->group(function() {
       
      Route::get('/all/blog/post', 'AllBlogPost')->name('all.blog.post');
      Route::get('/add/blog/post', 'AddBlogPost')->name('add.blog.post');
      Route::post('/store/blog/post', 'StoreBlogPost')->name('store.blog.post');
      Route::get('/edit/blog/post/{id}', 'EditBlogPost')->name('edit.blog.post');
      Route::post('/update/blog/post', 'UpdateBlogPost')->name('update.blog.post');
      Route::get('/delete/blog/post/{id}', 'DeleteBlogPost')->name('delete.blog.post');


    
   });


   // Frontend Comment All Routes

    Route::controller(CommentController::class)->group(function() {
       
        Route::get('/all/comment/', 'AllComment')->name('all.comment');
        Route::post('/update/comment/status', 'UpdateCommentStatus')->name('update.comment.status'); 
       
       
      
    });


    /// Booking Report All Route 
   Route::controller(ReportController::class)->group(function(){ 
      Route::get('/booking/report/', 'BookingReport')->name('booking.report');
      Route::post('/search-by-date', 'SearchByDate')->name('search-by-date');
     
    });

    // Site Setting All Route
    Route::controller(SettingController::class)->group(function() {
       
      Route::get('/site/setting', 'SiteSetting')->name('site.setting');
       Route::post('/site/update', 'SiteUpdate')->name('site.update');
   
     
    
   });
    // Gallery All Route
    Route::controller(GalleryController::class)->group(function() {
       
      Route::get('/all/gallery', 'AllGallery')->name('all.gallery');
      Route::get('/add/gallery', 'AddGallery')->name('add.gallery');
      Route::post('/store/gallery', 'StoreGallery')->name('store.gallery'); 
      Route::get('/edit/gallery/{id}', 'EditGallery')->name('edit.gallery');
      Route::post('/update/gallery/{id}', 'UpdateGallery')->name('update.gallery');
      Route::get('/delete/gallery/{id}', 'DeleteGallery')->name('delete.gallery');
      Route::post('/delete/gallery/multiple', 'DeleteGalleryMultiple')->name('delete.gallery.multiple');
       
    });


});




// Frontend Blog All Routes

    Route::controller(BlogController::class)->group(function() {
       
        Route::get('/blog/details/{slug}', 'BlogDetails');
        Route::get('/blog/cat/list/{id}', 'BlogCatList');
        Route::get('/blog/', 'BlogList')->name('blog.list');
       
       
      
    });
// Frontend Comment All Routes

    Route::controller(CommentController::class)->group(function() {
       
        Route::post('/store/comment/', 'StoreComment')->name('store.comment');
        
       
       
      
    });


/// Frontend Gallery All Route 
Route::controller(GalleryController::class)->group(function(){
 
    Route::get('/gallery', 'ShowGallery')->name('show.gallery');
   
 
});
/// Frontend Contact All Route 
Route::controller(ContactController::class)->group(function(){
 
    Route::get('/contact', 'ContactUs')->name('contact.us');
     Route::post('/store/contact', 'StoreContactUs')->name('store.contact');
   
 
});






