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
class PublicauthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
    */
    public function __construct()
    {

        
    }

    public function properties($page,$count,Request $request)
    {
         $title	=$request->json()->get('title');
         $category	=$request->json()->get('category');
         $rent	=0;
         if ($request->json()->get('rent')==true) {
            $rent=1;
         }
         $sale	=0;
         if ($request->json()->get('sale')==true) {
            $sale=1;
         }
         $near_city	=$request->json()->get('city');
         if (is_null($near_city)) {
            $near_city	="";

         }   
         if (is_null($category)) {
            $category	=0;

         }   

         if ($sale==$rent) {

            if ($category>0) {
               //return response()->json(['category' =>$category,'near_city' => $near_city]);

                $properties = Property::with("categorie","user")
                ->where([['title', 'like', $title.'%'] ,['near_city', 'like', '%'.$near_city.'%'] ,['categorie_id', '=', $category] ])
                ->offset($page)->limit($count)->get();
                $nmbrepage = Property::
                  where([['title', 'like', $title.'%'] ,['near_city', 'like', '%'.$near_city.'%'] ,['categorie_id', '=', $category] ])
                ->count();

            }else {

                $properties = Property::with("categorie","user")
                ->where([['title', 'like', $title.'%'] ,['near_city', 'like', '%'.$near_city.'%']   ])
                ->offset($page)->limit($count)->get();
                $nmbrepage = Property::
                 where([['title', 'like', $title.'%'] ,['near_city', 'like', '%'.$near_city.'%']   ])
                ->count();
            }
         }else{
            if ($category>0) {
                $properties = Property::with("categorie","user")
                ->where([['title', 'like', $title.'%'] ,['near_city', 'like','%'.$near_city.'%']  ,['sale', '=', $sale],['rent', '=', $rent] , ['categorie_id', '=', $category] ])
                ->offset($page)->limit($count)->get();
                $nmbrepage = Property::
                 where([['title', 'like', $title.'%'] ,['near_city', 'like', '%'.$near_city.'%']  ,['sale', '=', $sale],['rent', '=', $rent] , ['categorie_id', '=', $category] ])
                ->count();
            }else {
                $properties = Property::with("categorie","user")
                ->where([['title', 'like', $title.'%'] ,['near_city', 'like', '%'.$near_city.'%']  ,['sale', '=', $sale],['rent', '=', $rent]  ])
                ->offset($page)->limit($count)->get();
                $nmbrepage = Property::
                  where([['title', 'like', $title.'%'] ,['near_city', 'like', '%'.$near_city.'%']  ,['sale', '=', $sale],['rent', '=', $rent]  ])
                ->count();

            }

         }

         $nmbrepage = $nmbrepage/$count;
         if ( is_float($nmbrepage)) {
          $nmbrepage = round($nmbrepage)+1;
         }


        return response()->json(['properties' =>$properties,'nmbrepage' => $nmbrepage]);

    }


    public function getproperty($titleurl)
    {

      $Property = Property::with("categorie")->where('titleurl' , '=',$titleurl)->first();;
      $View= new View;
      $View->country	='';
      $View->property_id=$Property->id;

      $View->save();
      return  $Property ;
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
    }

    public function cities()
    {
      $data = DB::table('properties')
      ->select(DB::raw('count(*) as city_count , near_city	'))
      ->groupBy('near_city')
      ->orderBy('city_count', 'desc')
      ->get();
      $cities=[];
      for ($i=0; $i < count($data); $i++) { 
        array_push($cities, ['name'  => $data[$i]->near_city , 'count'  => $data[$i]->city_count ]);
      }
      return $cities ;
    }


    public function popularproperties()
    {
      $data = DB::table('views')
      ->select(DB::raw('count(*) as property_count , property_id'))
      ->groupBy('property_id')
      ->orderBy('property_count', 'desc')
      ->get();
      $views=[];
      for ($i=0; $i < count($data); $i++) { 
         $Property=Property::find($data[$i]->property_id);

        array_push($views, ['title'  => $Property->title ,'titleurl'  => $Property->titleurl ,'image'  => Property::find($data[$i]->property_id)->image , 'views'  => $data[$i]->property_count ]);
      }
      return $views ;
    
    }
    public function recentsproperties()
    {
      $data = Property::
       with("categorie")
      ->orderBy('created_at', 'desc')
      ->limit(5)
      ->get();
      return $data ;

   }



}
