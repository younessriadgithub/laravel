<?php

namespace App\Http\Controllers;

use Mail;

use App\User;
use App\Role;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
//use JWTAuth;
//use Auth;
use DB;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\PayloadFactory;
use Tymon\JWTAuth\JWTManager as JWT;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use DateTime;

class APIRegisterController extends Controller
{

    public function register(Request $request)
    {


        $validator = Validator::make($request->json()->all() , [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6', 
            'image' => 'required|string|min:6', 

        ]);

        if( User::where( 'email', '=', $request->json()->get('email')  )->count() > 0  ){
            return response()->json([
                'message' =>  'Email <b>'. $request->json()->get('email') .'</b> is already exist. '
            ], 200);
      
        }
 

        $user = User::create([
            'name' => $request->json()->get('name'),
            'email' => $request->json()->get('email'),
            'password' => Hash::make($request->json()->get('password')),
            'image' => "users/user.jpg"

        ]);
  
        $user_id = DB::getPdo()->lastInsertId();
   
        $role= new Role;
        $role->user_id=$user_id;
        $role->name="User";
        $role->save();
        $user->active=true;
    
        
        
        $token = JWTAuth::fromUser($user);
        $user->notify(new \App\Notifications\MailVerifyEmailNotification($token));

        return response()->json(compact('user','token'),201);
    }

    public function sendtoken(Request $request)
    {

      $user = User:: where('email', '=', $request->json()->get('email'))->first();
      $token = JWTAuth::fromUser($user);
      $user->notify(new \App\Notifications\MailVerifyEmailNotification($token));

      return response()->json(compact('user','token'),201);

    }


    
    public function activeaccount(Request $request){

      $th = JWTAuth::authenticate($request->bearerToken());
      $array = json_decode($th, true);

      $user_id= $array['id']; 
          $user = User::find($user_id);
          $user->email_verified_at = new DateTime();
          $user->save();
          return response()->json(['message' =>"Your account has been activated successfully.!",'success' => 'success']);




    }

    public function checkpassword(Request $request)
    {
    
                $id =$request->get('id');
                $oldpassword=  $request->get('oldpassword');
                $user = User::find($id);
                if (Hash::check($oldpassword, $user->password)) {
                  return response()->json([
                    'user' =>  $user,
                    'check' => true,
                    'message' =>  ' eeegggaaall <br>',
                  ], 200);
                
                }else{
                  return response()->json([
                    'user' =>  $user,
                    'check' => false,
                    'message' =>  'not egale <br>',
                  ], 200);
    
                }            
        }
        public function restpassword(Request $request)
        {
          $oldpassword=  $request->get('oldpassword');
          $newpassword=  $request->get('newpassword');
    
                $id =$request->get('id');
                $user = User::find($id);
                if (Hash::check($oldpassword, $user->password)) {
                 $user->password = bcrypt($request->get('newpassword'));
    
                 $user->update();
                  return response()->json([
                    'message' =>  'success',
                    'user' =>  $user
                  ], 200);
      
                }else{
      
                    return response()->json([
                      'user' =>  $user,
                      'oldpassword' =>   bcrypt($request->get('oldpassword')),
                      'userpassword' =>  $user->password,
                      'message' =>  'not eeegggaaall',
                    ], 200);
      
                }
    
        }






      protected function rest(Request $request)
      {
         

        $user = User:: where('email', '=', $request->json()->get('email'))->first();
        $token = JWTAuth::fromUser($user);
        $user->notify(new \App\Notifications\MailResetPasswordNotification($token));

        return response()->json(['views' =>$user,'nmbrepage' => $token]);  
      }


      public function contact(Request $request)
      {


        $email = $request->json()->get('email');
        $subject = $request->json()->get('subject');
        $body =$request->json()->get('body');

   
        Mail::send('mail.contact', ['email' => $email,'subject' => $subject,'body' => $body], function ($m) use ($email, $body ,$subject) {
          $m->from('younessriadme@gmail.com', 'jjulxumgysrmhpxg');

          $m->to('younessriadme@gmail.com', $subject)->subject($body);
        });

        return response()->json(['email' =>$request->json()->get('email'),'body'  =>$request->json()->get('body')]);  
      }
}