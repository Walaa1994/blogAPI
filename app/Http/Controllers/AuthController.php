<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends BaseController
{
    public function register(Request $request){
        $input = $request->all();
        $validator = Validator::make($input , [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password'
        ]);

        if($validator->fails()){
            return $this->sendError('Please validate the error',$validator->errors());
        }

        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('walaa')->accessToken;
        $success['name'] = $user->name;

        return $this->sendResponse($success,'User register successfully');

    }

    public function login(Request $request){

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $success['token'] = $user->createToken('walaa')->accessToken;
            $success['name'] = $user->name;
            return $this->sendResponse($success,'User login successfully');

        }else{
            return $this->sendError('Please check your auth',['error' => 'Unauthorized']);
        }
    }
}
