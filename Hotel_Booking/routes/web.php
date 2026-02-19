<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Backend\TeamController;
use App\Http\Controllers\Backend\RoomTypeController;
use App\Http\Controllers\Backend\RoomController;

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
    
     
       
    });

});






