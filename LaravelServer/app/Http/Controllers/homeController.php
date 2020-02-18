<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use JWTFactory;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;

class HomeController extends Controller
{


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
          $this->middleware('auth');

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }


    public function register(Request $request)
    {
       
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255|unique:users',
                'name' => 'required',
                'password'=> 'required'
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors());
            }
            User::create([
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'password' => bcrypt($request->get('password')),
            ]);
            $user = User::first();
            $token = JWTAuth::fromUser($user);
            
            return Response::json(compact('token'));
        }
    

}
