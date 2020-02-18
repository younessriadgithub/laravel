<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Property;
use App\View;
use App\Categorie;
use App\User;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\PayloadFactory;
use Tymon\JWTAuth\JWTManager as JWT;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Illuminate\Support\Facades\DB;

class AuthjwtController extends Controller
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);

        $this->middleware('IsActivated');

        
    }


    public function getAuthenticatedUser(Request $request){

      $th = JWTAuth::authenticate($request->bearerToken());

      $array = json_decode($th, true);

      return response()->json( User::find($array['id'])->first());

    }





    public function isAdmin(Request $request){

      $th = JWTAuth::authenticate($request->json()->get('token'));

      $array = json_decode($th, true);

      $user_id= $array['id']; 
         $message= DB::table('roles')   
         ->where([['name',"admin" ] , ["user_id",   $user_id]])
         ->count() ;

         if ($message===0) {
          return $autorisation = "false";



      }else{

          return $autorisation = "true";

      }
  }

  public function profile(Request $request){

    $th = JWTAuth::authenticate($request->bearerToken());
    $array = json_decode($th, true);

    $user_id= $array['id'];  
    $user = User::find($user_id);
    
    return response()->json( compact('user') );
}

  public function getRole(Request $request){

    $th = JWTAuth::authenticate($request->bearerToken());

      $array = json_decode($th, true);

      $user_id= $array['id']; 
         $role= DB::table('roles')   
         ->where("user_id",$user_id)
         ->select('name')
         ->first();

         $name=$role->name;
         return response()->json( compact('name') );


          
  }




  public function updateuser( $id,Request $request){

      $th = JWTAuth::authenticate($token = $request->bearerToken());
      $array = json_decode($th, true);

      $user_id= $array['id'];  
      $array = json_decode($th, true);
      $role = Role::where('user_id' , '=',$id)->first();;

      $role->name=$request->json()->get('role');
      $role->save();

      return response()->json(['countcategories' => $role]);


          
  }




  public function updateuserprofile(Request $request){

      $th = JWTAuth::authenticate($token = $request->bearerToken());
      $array = json_decode($th, true);

      $user_id= $array['id'];  

      $User= User::find($user_id);

      $User->name=$request->json()->get('name');
      $User->email=$request->json()->get('email');
      $imageurl=  str_replace("@", "_", $request->json()->get('email'));
      $imageurlname=  str_replace(".", "_", $imageurl);


      

      if($request->json()->get('imgURL'))
      {
          $imgUrl = $request->json()->get('imgURL');
          $mimeType = mime_content_type($imgUrl);
          $imgext = str_replace("image/", ".", $mimeType);
          $fileName = $imageurlname.$imgext ;
          $image = file_get_contents($imgUrl);
          $destinationPath = base_path() . '/public/images/users/' . $fileName;
          file_put_contents($destinationPath, $image);
          $User->image='users/' . $fileName;
      }


  
     
        $User->save();
      return response()->json(['user' =>$User]);


  }
  public function updateprofile(Request $request)
  {

      $th = JWTAuth::authenticate($token = $request->bearerToken());
      $array = json_decode($th, true);
      $user_id= $array['id']; 
      
      
        if( User::where( [ ['email', '=', $request->json()->get('email') ] , [ 'id', '<>', $user_id ] ] )->count()==1  ){
              $user = User::find($id);
              return response()->json([
                  'user' =>  $user,
                  'message' =>  'Username <b>'. $request->json()->get('email') .'</b> is already exist. '
              ], 200);
        
        }
        


          $user = User::find($id);
          $user->name =  $request->json()->get('name');
          $user->email =  $request->json()->get('email');

          if($request->json()->get('imgURL'))
          {
  
  
              $imgUrl = $request->json()->get('imgURL');
              $mimeType = mime_content_type($imgUrl);
              $imgext = str_replace("image/", ".", $mimeType);
              $fileName = $Property->titleurl.$imgext ;
  
  
              $image = file_get_contents($imgUrl);
              $destinationPath = base_path() . '/public/images/users/' . $fileName;
   
              file_put_contents($destinationPath, $image);
              $user->image='users/' . $fileName;

          }

          $user->save();

          $image =  $request->get('image');



          return response()->json(['user' =>$user]);

    
      
      }


      public function newpassword(Request $request)
      {

          $th = JWTAuth::authenticate($token = $request->bearerToken());
          $array = json_decode($th, true);
          $user_id= $array['id']; 
          $user = User::find($user_id);
      
          $oldpassword=$request->json()->get('oldpassword');
          if (Hash::check($oldpassword, $user->password)) {
              $user->password =  bcrypt( $request->json()->get('newpassword'));
              $user->save();
              return response()->json([
                'user' =>  $user,
                'check' => true,
              ], 200);
            
            }else{
              return response()->json([
                'user' =>  $user,
                'check' => false,
              ], 200);

            }    
          


          return response()->json(['user' =>$User]);

      }


      public function changePassword(Request $request){

        $th = JWTAuth::authenticate($token = $request->bearerToken());
        $array = json_decode($th, true);
        $user_id= $array['id']; 
        $user = User::find($user_id);
        $user->password = bcrypt( $request->json()->get('password'));
        $user->save();
        return response()->json(['message' =>"Password changed successfully !",'success' => 'success']);

    }







































    public function statistic()
    {

        return response()->json(['countcategories' => Categorie::count() ,'countusers' =>User::count(),'countproperties' => Property::count(),'countviews' =>View::count()]);

    }
    public function newview(Request $request)
    {

        $View= new View;
        $View->country	='';
      //  $View->country	=$request->json()->get('country');

        $Property = Property::find($request->json()->get('idproperty'));
        $View->property_id=$Property->id;

        $View->save();

        return response()->json(['countcategories' => '88']);

    }
    public function deleteview( $id )
    {

        $View=  View::find($id);
        $View->delete();

        return response()->json(['countcategories' => true]);

    }


    public function views(Request $request)
    {

      return $views = View::with('property')->get();
     // return $views = View::get();

    }

    public function countviews(Request $request)
    {
      $data = DB::table('views')
      ->select(DB::raw('count(*) as property_count , property_id'))
      ->groupBy('property_id')
      ->orderBy('property_count', 'desc')
      ->get();
      $views=[];
      for ($i=0; $i < count($data); $i++) { 
        array_push($views, ['name'  => Property::find($data[$i]->property_id)->title , 'value'  => $data[$i]->property_count ]);
      }
      return $views ;
     // return $views = View::get();

    }


    public function chartviews(Request $request)
    {
      $year0=date("Y");
      $year1=date("Y",strtotime("-1 year"));
      $year2=date("Y",strtotime("-2 year"));
      $year3=date("Y",strtotime("-3 year"));
      $year4=date("Y",strtotime("-4 year"));



  
      return response()->json([
            [
              'name' =>$year0,
              'series' =>    $this->getViewSeries($year0)
            ],
            [
              'name' =>$year1,
              'series' => $this->getViewSeries($year1)
            ],
            [
              'name' =>$year2,
              'series' =>    $this->getViewSeries($year2)
            ],
            [
              'name' =>$year3,
              'series' => $this->getViewSeries($year3)
            ],
            [
              'name' =>$year4,
              'series' => $this->getViewSeries($year4)
            ]
      ], 200);


    }

    public function getViewSeries( $year)

    {

      $data = DB::select('select  month(created_at) as name, count(id) as value from views  where  year(created_at) ='.$year.' group by year(created_at), month(created_at)');

      $jan=0;
      $February=0;
      $march=0;
      $April=0;
      $May=0;
      $June=0;
      $July=0;
      $August=0;
      $September=0;
      $October=0;
      $November=0;
      $December=0;


      for ($i=0; $i < count($data) ; $i++) { 

        if ($data[$i]->name==1) {
          $jan= $data[$i]->value;
        } else if ($data[$i]->name==2) {
          $February= $data[$i]->value;
        } else if ($data[$i]->name==3) {
          $march= $data[$i]->value;
        } else if ($data[$i]->name==4) {
          $April= $data[$i]->value;
        } else if ($data[$i]->name==5) {
          $May = $data[$i]->value;
        } else if ($data[$i]->name==6) {
          $June = $data[$i]->value;
        } else if ($data[$i]->name==7) {
          $July = $data[$i]->value;
        } else if ($data[$i]->name==8) {
          $August = $data[$i]->value;
        } else if ($data[$i]->name==9) {
          $September = $data[$i]->value;
        } else if ($data[$i]->name==10) {
          $October = $data[$i]->value;
        } else if ($data[$i]->name==11) {
          $November = $data[$i]->value;
        } else if ($data[$i]->name==12) {
          $December = $data[$i]->value;
        }

      }
      $series = [

        [
          'name' => "January",
          'value' => $jan,
        ],
        [
          'name' => "February",
          'value' => $February,
        ],
        [
          'name' => "March",
          'value' =>  $march  ,
        ],
        [
          'name' => "April",
          'value' => $April ,
        ],
        [
          'name' => "May ",
          'value' => $May ,
        ],
        [
          'name' => "June ",
          'value' =>  $June   ,
        ],
        [
          'name' => "July ",
          'value' => $July ,
        ],
        [
          'name' => "August ",
          'value' => $August ,
        ],
        [
          'name' => "September ",
          'value' =>  $September   ,
        ],
        [
          'name' => "October",
          'value' => $October,
        ],
        [
          'name' => "November",
          'value' => $November,
        ],
        [
          'name' => "December",
          'value' =>  $December  ,
        ],
        
      ];

       return $series;
    }



    public function newcategory(Request $request)
    {

        $Categorie= new Categorie;
        //$Categorie->name	=$request->get('name');
        $Categorie->name	=$request->json()->get('name');
        $Categorie->description="";


        $Categorie->save();

        return response()->json(['countcategories' => '88','countusers' => '88','countproducts' => '88','countorders' => '88']);

    }

    public function updatecategory($id,Request $request)
    {

        $Categorie=  Categorie::find($id);
        //$Categorie->name	=$request->get('name');
        $Categorie->name	=$request->json()->get('name');
        $Categorie->description="";


        $Categorie->save();

        return response()->json(['countcategories' => '88','countusers' => '88','countproducts' => '88','countorders' => '88']);

    }


    public function categories()
    {
      $data=[];

      $categories = Categorie::with('property')
      ->get();

       for ($i=0; $i < count($categories) ; $i++) { 
           array_push($data, ['id'  =>  $categories[$i]->id,'name'  =>  $categories[$i]->name, 'value'  => count($categories[$i]->property) ]);
       }
        return $data ;
      //  return $categories = Categorie::get();
    }
    public function categoriespag($page,$count,Request $request)
    {

         $search	=$request->json()->get('name');

 
         $categories = Categorie::with('property')
         ->where('name', 'like', $search.'%')
         ->offset($page-1)->limit($count)
         ->get();
         $nmbrepage = Categorie::
         where('name', 'like', $search.'%')
         ->count();
         $nmbrepage = $nmbrepage/$count;
         if ( is_float($nmbrepage)) {
          $nmbrepage = round($nmbrepage)+1;
         }
        return response()->json(['categories' =>$categories,'nmbrepage' => $nmbrepage]);


    }





    public function properties($page,$count,Request $request)
    {
 
         $search	=$request->json()->get('name');

         $properties = Property::with("categorie","user")
         ->where('title', 'like', $search.'%')
         ->offset($page)->limit($count)->get();
         $nmbrepage = Property::where('title', 'like', $search.'%')->count();
         $nmbrepage = $nmbrepage/$count;
         if ( is_float($nmbrepage)) {
          $nmbrepage = round($nmbrepage)+1;
         }


        return response()->json(['properties' =>$properties,'nmbrepage' => $nmbrepage]);

    }





    public function chartproperties()
    {
      $year0=date("Y");
      $year1=date("Y",strtotime("-1 year"));
      $year2=date("Y",strtotime("-2 year"));
      $year3=date("Y",strtotime("-3 year"));
      $year4=date("Y",strtotime("-4 year"));


  
      return response()->json([
        

                [
                  'name' =>$year0,
                  'series' =>    $this->getSeries($year0)
                ],
                [
                  'name' =>$year1,
                  'series' => $this->getSeries($year1)
                ],
                [
                  'name' =>$year2,
                  'series' =>    $this->getSeries($year2)
                ],
                [
                  'name' =>$year3,
                  'series' => $this->getSeries($year3)
                ],
                [
                  'name' =>$year4,
                  'series' => $this->getSeries($year4)
                ]



        

      ], 200);

      //  return $properties = Property::get();
    }
    public function getSeries( $year)

    {

      $data = DB::select('select  month(created_at) as name, count(id) as value from properties  where  year(created_at) ='.$year.' group by year(created_at), month(created_at)');

      $jan=0;
      $February=0;
      $march=0;
      $April=0;
      $May=0;
      $June=0;
      $July=0;
      $August=0;
      $September=0;
      $October=0;
      $November=0;
      $December=0;


      for ($i=0; $i < count($data) ; $i++) { 

        if ($data[$i]->name==1) {
          $jan= $data[$i]->value;
        } else if ($data[$i]->name==2) {
          $February= $data[$i]->value;
        } else if ($data[$i]->name==3) {
          $march= $data[$i]->value;
        } else if ($data[$i]->name==4) {
          $April= $data[$i]->value;
        } else if ($data[$i]->name==5) {
          $May = $data[$i]->value;
        } else if ($data[$i]->name==6) {
          $June = $data[$i]->value;
        } else if ($data[$i]->name==7) {
          $July = $data[$i]->value;
        } else if ($data[$i]->name==8) {
          $August = $data[$i]->value;
        } else if ($data[$i]->name==9) {
          $September = $data[$i]->value;
        } else if ($data[$i]->name==10) {
          $October = $data[$i]->value;
        } else if ($data[$i]->name==11) {
          $November = $data[$i]->value;
        } else if ($data[$i]->name==12) {
          $December = $data[$i]->value;
        }

      }
      $series = [

        [
          'name' => "January",
          'value' => $jan,
        ],
        [
          'name' => "February",
          'value' => $February,
        ],
        [
          'name' => "March",
          'value' =>  $march  ,
        ],
        [
          'name' => "April",
          'value' => $April ,
        ],
        [
          'name' => "May ",
          'value' => $May ,
        ],
        [
          'name' => "June ",
          'value' =>  $June   ,
        ],
        [
          'name' => "July ",
          'value' => $July ,
        ],
        [
          'name' => "August ",
          'value' => $August ,
        ],
        [
          'name' => "September ",
          'value' =>  $September   ,
        ],
        [
          'name' => "October",
          'value' => $October,
        ],
        [
          'name' => "November",
          'value' => $November,
        ],
        [
          'name' => "December",
          'value' =>  $December  ,
        ],
        
      ];

       return $series;
    }

    public function newproperty(Request $request)
    {


        $th = JWTAuth::authenticate($token = $request->bearerToken());
        $array = json_decode($th, true);

        $user_id= $array['id'];  

        $Property= new Property;

        $Property->user_id=$user_id;
        $idCategorie=$request->json()->get('category');

        $Property->categorie_id=$idCategorie;

        $Property->title=$request->json()->get('title');



        $Property->titleurl=  str_replace(" ", "_", $request->json()->get('title'));
        

        if($request->json()->get('imgURL'))
        {


            $imgUrl = $request->json()->get('imgURL');
           // $fileName = array_pop(explode(DIRECTORY_SEPARATOR, $imgUrl));
             $mimeType = mime_content_type($imgUrl);
            // $bodytag = str_replace("%body%", "black", "<body text='%body%'>");
            $imgext = str_replace("image/", ".", $mimeType);
            $fileName = $Property->titleurl.$imgext ;


            $image = file_get_contents($imgUrl);
            $destinationPath = base_path() . '/public/images/properties/' . $fileName;
 
           file_put_contents($destinationPath, $image);
         //   $attributes['image'] = $fileName;


             $Property->image='properties/' . $fileName;


        //   $file = $request->file('filename');
        //   $path_info=$file->getClientOriginalName();
        //   $path_parts = pathinfo($file->getClientOriginalName());
        //   $ext  =$path_parts['extension']; 
         //  $file->move(public_path().'/images/properties', $request->get('name').".".$ext);
         //  $Property->image=$fileName;

        }

        $Property->description=$request->json()->get('description');
        $Property->price=$request->json()->get('price');
        $Property->area_size=$request->json()->get('area_size');
        $Property->phone=$request->json()->get('phone');
        $Property->address=$request->json()->get('address');
        $Property->near_city=$request->json()->get('near_city');
        $Property->balcony=$request->json()->get('balcony');
        $Property->enable=0;

        if ($request->json()->get('salerent')=="rent" ) {
          $Property->sale=0;
          $Property->rent=1;
        }else{
          $Property->sale=1;
          $Property->rent=0;
        }

        
        $Property->garage=0;


        $Property->date_build = $request->json()->get('date_build');

       
        $Property->save();
        return response()->json(['countcategories' =>$request->json()->get('salerent'),'countusers' => '88','countproducts' => '88','countorders' => '88']);



        return $Property;

    }


    public function updateproperty($id,Request $request)
    {


        $th = JWTAuth::authenticate($token = $request->bearerToken());
        $array = json_decode($th, true);

        $user_id= $array['id'];  

        $Property= Property::find($id);

        $Property->user_id=$user_id;
        $idCategorie=$request->json()->get('category');

        $Property->categorie_id=$idCategorie;

        $Property->title=$request->json()->get('title');
        $Property->titleurl=  str_replace(" ", "_", $request->json()->get('title'));


        if($request->json()->get('imgURL'))
        {


            $imgUrl = $request->json()->get('imgURL');
             $mimeType = mime_content_type($imgUrl);
            $imgext = str_replace("image/", ".", $mimeType);
            $fileName = $Property->titleurl.$imgext ;


            $image = file_get_contents($imgUrl);
            $destinationPath = base_path() . '/public/images/properties/' . $fileName;
 
           file_put_contents($destinationPath, $image);


             $Property->image='properties/' . $fileName;
        }

        $Property->description=$request->json()->get('description');
        $Property->price=$request->json()->get('price');
        $Property->area_size=$request->json()->get('area_size');
        $Property->phone=$request->json()->get('phone');
        $Property->address=$request->json()->get('address');
        $Property->near_city=$request->json()->get('near_city');
        $Property->balcony=$request->json()->get('balcony');
        $Property->enable=$request->json()->get('enable');

        if ($request->json()->get('salerent')=="rent" ) {
          $Property->sale=0;
          $Property->rent=1;
        }else{
          $Property->sale=1;
          $Property->rent=0;
        }

        
        $Property->garage=0;


        $Property->date_build = $request->json()->get('date_build');

       
        $Property->save();
        return response()->json(['countcategories' =>$request->json()->get('salerent'),'countusers' => '88','countproducts' => '88','countorders' => '88']);



        return $Property;

    }


    public function propertybyid($id, Request $request)
    {


        $th = JWTAuth::authenticate($token = $request->bearerToken());
        $array = json_decode($th, true);

        $user_id= $array['id'];  

        $Property=  Property::find($id);


        return $Property;

    }




    public function chartusers(Request $request)
    {
      $year_1=date("Y");
      $year_1=(int)$year_1;
      $year_1=$year_1-1;


  
      return response()->json([
        

                [
                  'name' =>"January",
                  'series' =>    $this->getUsersSeries(1)
                ],
                [
                  'name' =>"February",
                  'series' => $this->getUsersSeries(2)
                ],
                [
                  'name' =>"March",
                  'series' =>    $this->getUsersSeries(3)
                ],
                [
                  'name' =>"April",
                  'series' => $this->getUsersSeries(4)
                ],
                [
                  'name' =>"May",
                  'series' =>    $this->getUsersSeries(5)
                ],
                [
                  'name' =>"July ",
                  'series' => $this->getUsersSeries(6)
                ],
                [
                  'name' =>"January",
                  'series' =>    $this->getUsersSeries(7)
                ],
                [
                  'name' =>"August",
                  'series' => $this->getUsersSeries(8)
                ],
                [
                  'name' =>"September",
                  'series' =>    $this->getUsersSeries(9)
                ],
                [
                  'name' =>"October ",
                  'series' => $this->getUsersSeries(10)
                ],
                [
                  'name' =>"November ",
                  'series' =>    $this->getUsersSeries(11)
                ],
                [
                  'name' =>"December",
                  'series' => $this->getUsersSeries(12)
                ]

      ], 200);


    }

    public function getUsersSeries( $month)

    {

  
      $year_=2020;
      $year_1=2019;
      $year_2=2018;
      $year_3=2017;
      $year_4=2016;


      $year  = DB::select('select id from users  where  month(created_at) = '.$month.' and  year(created_at) ='.$year_);
      $year1 = DB::select('select id from users  where  month(created_at) = '.$month.' and  year(created_at) ='.$year_1.' ');
      $year2 = DB::select('select id from users  where  month(created_at) = '.$month.' and  year(created_at) ='.$year_2.'  ');
      $year3 = DB::select('select id from users  where  month(created_at) = '.$month.' and  year(created_at) ='.$year_3.' ');
      $year4 = DB::select('select id  from users  where  month(created_at) = '.$month.' and  year(created_at) ='.$year_4.' ');
     
 
        $year = count($year);
        $year1 = count($year1);
        $year2 = count($year2);
        $year3 = count($year3);
        $year4 = count($year4);





      if (is_null($year1)) {
        $year1=$year1->value;
      }else {
        $year1 = 0;
      }



      if (is_null($year2)) {
        $year2=$year2->value;
      }else {
        $year2 = 0;
      }


      if (is_null($year3)) {
        $year3=$year3->value;
      }else {
        $year3 = 0;
      }


      if (is_null($year4)) {
        $year4=$year4->value;
      }else {
        $year4=0;
      }
   

      $series = [

        [
          'name' => $year_,
          'value' => $year,
        ],
        [
          'name' => $year_1,
          'value' => $year1,
        ],
        [
          'name' => $year_2,
          'value' => $year2,
        ],
        [
          'name' => $year_3,
          'value' => $year3,
        ],
        [
          'name' => $year_4,
          'value' => $year4,
        ]
        
      
      ];

       return $series;
    }

    public function userspag($page,$count,Request $request)
    {

         $search	=$request->json()->get('name');
         $users = User::with('properties','role')
         ->where('name', 'like', $search.'%')
         ->offset($page)->limit($count)
         ->get();
         $nmbrepage = User::
         where('name', 'like', $search.'%')
         ->count();

         $nmbrepage = $nmbrepage/$count;
         if ( is_float($nmbrepage)) {
          $nmbrepage = round($nmbrepage)+1;
         }
        return response()->json(['users' =>$users,'nmbrepage' => $nmbrepage]);


    }


    
    public function viewspag($page,$count,Request $request)
    {
 
         $search	=$request->json()->get('name');

         $views = View::with("property")
         ->where('country', 'like', $search.'%')
         ->offset($page)->limit($count)->get();
         $nmbrepage = View::where('country', 'like', $search.'%')->count();
         $nmbrepage = $nmbrepage/$count;
         if ( is_float($nmbrepage)) {
          $nmbrepage = round($nmbrepage)+1;
         }


        return response()->json(['views' =>$views,'nmbrepage' => $nmbrepage]);

    }



}
