<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller; // Import the base Controller class

class UserController extends Controller
{
    public function showAdminName()
    {
        return 'Ahmed Teet'; // Adjusted method name and returned string
    }

    public function getIndex()
    {
        $data = ['name', 'email' , 'age' ];
        // $data['id'] = 5;
        // $data['name'] = 'ahmed';

        // $obj = new \stdClass();
        // $obj->name = 'ahmed emam';
        // $obj->id = 21;
        // $obj->gender = 'male';
        return view('welcome', compact('data'));
    }
}
