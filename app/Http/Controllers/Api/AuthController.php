<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\User;


class AuthController extends BaseController
{   
    /**
     * signin user and create session
     * mendatory params - email,password
     * return collection
     */
    public function signin(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        
        if($validator->fails()){
            return $this->sendError('Validation Error', $validator->errors(),403);       
        }

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $request->session()->regenerate();
            $authUser = Auth::user(); 
            $success['name'] = $authUser->name;
            $success['email'] = $authUser->email;
  
            return $this->sendResponse($success, 'User signed in');
        } 
        else{ 
            return $this->sendError('Wrong name or password', ['error'=>'Unauthorized'],403);
        } 
    }

    /**
     * signup user and create session
     * mendatory params - email,password,name
     * return collection
     */
    public function signup(Request $request)
    {
   
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);
        
        if($validator->fails()){
            return $this->sendError('Validation Error', $validator->errors(),403);       
        }

        $input = $request->all();

        $input['password'] = bcrypt($input['password']);

        $user = User::create($input);

        if(!$user){
            return $this->sendError('Failed to Create User', [],500);    
        }

        if(Auth::attempt(['email' => $user->email, 'password' => $request->password])){ 
        $request->session()->regenerate();
        $success['name']= $user->name;
        $success['email'] = $user->email;

        return $this->sendResponse($success, 'User created successfully.');
        }else{
            return $this->sendError('Failed to Create User', [],500); 
        }
    }
    
    /**
     * logout user
     */
    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return $this->sendResponse('success', 'User logout successfully.');
    }


}