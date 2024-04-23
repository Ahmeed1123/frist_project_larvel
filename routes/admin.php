<?php
use Illuminate\Support\Facades\Routes;
Route::get('/admin', function () {
    return 'Hello admin';
});

// Route::group(['prefix'=> 'users'],function() {
//         Route::get('/', function() {
//             return 'Hello users';
//         });
//     // All routes only access controller or methods in folder name  Front
//         Route::get('users','UserController@Show_AdminName');

// });

// Route::get('check', function() {
//     return 'middlewere';
// })->middleware('auth');

