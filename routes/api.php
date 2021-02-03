<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/configuration/variable', 'ConfigurationController@getConfigurationVariable');

Route::group(['prefix' => 'posts'], function () {
    Route::get('/','PostController@getPublicPosts');
    Route::get('/{category}/{slug}','PostController@getPublicPost');
});

Route::group(['prefix' => 'pages'], function () {
    Route::get('/','PageController@getPublicPages');
    Route::get('/{slug}','PageController@getPublicPage');
});

 //Socket Room

 Route::prefix('/ws-socket-room')->name('api.ws-socket-room.')->group(function () {

    Route::get('/', 'SocketRoomController@index')->name('index');
    Route::get('/{id}', 'SocketRoomController@show')->name('show');
    Route::post('/', 'SocketRoomController@store')->name('store');
    Route::patch('/{id}', 'SocketRoomController@update')->name('update');
    Route::delete('/{id}', 'SocketRoomController@destroy')->name('destroy');

});

Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', 'AuthController@authenticate');
    Route::post('/check', 'AuthController@check');
    Route::post('/register', 'AuthController@register');
    Route::get('/activate/{token}', 'AuthController@activate');
    Route::post('/password', 'AuthController@password');
    Route::post('/validate-password-reset', 'AuthController@validatePasswordReset');
    Route::post('/reset', 'AuthController@reset');
    Route::post('/send-otp', 'AuthController@SendOtp');
    Route::post('/register-otp', 'AuthController@RegisterWithOtp');
    Route::post('/check-register', 'AuthController@CheckRegister');
    Route::post('/change-pass-otp', 'AuthController@ChangePassOtp');
    Route::post('/login-otp', 'AuthController@LoginOtp');
    Route::get('/check-code/{token}', 'AuthController@checkcode');
});

Route::group(['middleware' => ['jwt.auth']], function () {

    Route::get('/activity-log', 'ActivityLogController@index');
    Route::delete('/activity-log/{id}', 'ActivityLogController@destroy');

    Route::post('/auth/logout', 'AuthController@logout');
    Route::post('/auth/lock', 'AuthController@lock');
    Route::post('/change-password', 'AuthController@changePassword');

    Route::get('/configuration', 'ConfigurationController@index');
    Route::post('/configuration', 'ConfigurationController@store');
    Route::post('/configuration/image/{type}', 'ConfigurationController@uploadConfigImages');
    Route::delete('/configuration/image/{type}/remove', 'ConfigurationController@removeConfigImages');
    Route::get('/fetch/lists', 'ConfigurationController@fetchList');

    Route::get('/dashboard', 'HomeController@dashboard');

    Route::get('/locale', 'LocaleController@index');
    Route::post('/locale', 'LocaleController@store');
    Route::get('/locale/{id}', 'LocaleController@show');
    Route::patch('/locale/{id}', 'LocaleController@update');
    Route::delete('/locale/{id}', 'LocaleController@destroy');

    Route::get('/permission', 'PermissionController@index');
    Route::get('/permission/assign/pre-requisite', 'PermissionController@preRequisite');
    Route::get('/permission/{id}', 'PermissionController@show');
    Route::post('/permission', 'PermissionController@store');
    Route::delete('/permission/{id}', 'PermissionController@destroy');
    Route::post('/permission/assign', 'PermissionController@assignPermission');

    Route::get('/role', 'RoleController@index');
    Route::get('/role/{id}', 'RoleController@show');
    Route::post('/role', 'RoleController@store');
    Route::delete('/role/{id}', 'RoleController@destroy');

    Route::get('/user/pre-requisite', 'UserController@preRequisite');
    Route::get('/user/detail', 'UserController@detail');
    Route::get('/user', 'UserController@index');
    Route::get('/user/{id}', 'UserController@show');
    Route::post('/user', 'UserController@store');
    Route::post('/user/{id}/status', 'UserController@updateStatus');
    Route::patch('/user/{id}', 'UserController@update');
    Route::patch('/user-profile/{id}', 'UserController@updateWithApi');
    Route::patch('/user/{id}/contact', 'UserController@updateContact');
    Route::patch('/user/{id}/force-reset-password', 'UserController@forceResetPassword');
    Route::patch('/user/{id}/email', 'UserController@sendEmail');
    Route::post('/user/profile/update', 'UserController@updateProfile');
    Route::post('/user/profile/avatar/{id}', 'UserController@uploadAvatar');
    Route::delete('/user/profile/avatar/remove/{id}', 'UserController@removeAvatar');
    Route::delete('/user/{id}', 'UserController@destroy');

    Route::get('/post/pre-requisite','PostController@preRequisite');
    Route::post('/post/statistics','PostController@statistics');
    Route::post('/post/new','PostController@store');
    Route::get('/post/draft','PostController@getDraftList');
    Route::get('/post/published','PostController@getPublishedList');
    Route::delete('/post/{slug}','PostController@destroy');
    Route::get('/post/{slug}','PostController@show');
    Route::post('/post/upload/image','PostController@uploadImage');
    Route::post('/post/cover/{id}', 'PostController@uploadCover');
    Route::delete('/post/cover/remove/{id}', 'PostController@removeCover');

    Route::get('/category', 'CategoryController@index');
    Route::get('/category/{id}', 'CategoryController@show');
    Route::post('/category', 'CategoryController@store');
    Route::delete('/category/{id}', 'CategoryController@destroy');

    Route::post('/page/statistics','PageController@statistics');
    Route::post('/page/new','PageController@store');
    Route::get('/page/published','PageController@getPublishedList');
    Route::delete('/page/{slug}','PageController@destroy');
    Route::get('/page/{slug}','PageController@show');
    Route::post('/page/upload/image','PageController@uploadImage');

    //Apartment

    Route::prefix('/apartment')->name('api.apartment.')->group(function () {

        Route::get('/', 'ApartmentController@index')->name('index');
        Route::get('/{id}', 'ApartmentController@show')->name('show');
        Route::post('/', 'ApartmentController@store')->name('store');
        Route::patch('/{id}', 'ApartmentController@update')->name('update');
        Route::delete('/{id}', 'ApartmentController@destroy')->name('destroy');

    });

    //Room

    Route::prefix('/room')->name('api.room.')->group(function () {

        Route::get('/', 'RoomController@index')->name('index');
        Route::get('/{id}', 'RoomController@show')->name('show');
        Route::post('/', 'RoomController@store')->name('store');
        Route::patch('/{id}', 'RoomController@update')->name('update');
        Route::delete('/{id}', 'RoomController@destroy')->name('destroy');

    });

    //Device

    Route::prefix('/device')->name('api.device.')->group(function () {

        Route::get('/', 'DeviceController@index')->name('index');
        Route::get('/{id}', 'DeviceController@show')->name('show');
        Route::post('/', 'DeviceController@store')->name('store');
        Route::patch('/{id}', 'DeviceController@update')->name('update');
        Route::delete('/{id}', 'DeviceController@destroy')->name('destroy');

    });

    //Switch

    Route::prefix('/switch')->name('api.switch.')->group(function () {

        Route::get('/', 'SwitchController@index')->name('index');
        Route::get('/{id}', 'SwitchController@show')->name('show');
        Route::post('/', 'SwitchController@store')->name('store');
        Route::patch('/{id}', 'SwitchController@update')->name('update');
        Route::delete('/{id}', 'SwitchController@destroy')->name('destroy');

    });

    //Type Device

    Route::prefix('/type-device')->name('api.type-device.')->group(function () {

        Route::get('/', 'TypeDeviceController@index')->name('index');
        Route::get('/{id}', 'TypeDeviceController@show')->name('show');
        Route::post('/', 'TypeDeviceController@store')->name('store');
        Route::patch('/{id}', 'TypeDeviceController@update')->name('update');
        Route::delete('/{id}', 'TypeDeviceController@destroy')->name('destroy');

    });

   

});
