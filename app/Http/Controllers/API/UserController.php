<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\User; 
use Illuminate\Support\Facades\Auth; 
use Validator;
class UserController extends Controller 
{
public $successStatus = 200;
/** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function login(){ 
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')-> accessToken; 
            return response()->json(['success' => $success], $this-> successStatus); 
        } 
        else{ 
            return response()->json(['error'=>'Unauthorised'], 401); 
        } 
    }
/** 
     * Register api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function register(Request $request) 
    { 
        $validator = Validator::make($request->all(), [ 
            'name' => 'required', 
            'email' => 'required|email', 
            'password' => 'required', 
            'c_password' => 'required|same:password', 
        ]);
if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
$input = $request->all(); 
        $input['password'] = bcrypt($input['password']); 
        $user = User::create($input); 
        $success['token'] =  $user->createToken('MyApp')-> accessToken; 
        $success['name'] =  $user->name;
return response()->json(['success'=>$success], $this-> successStatus); 
    }
/** 
     * details api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function details() 
    { 
        $user = Auth::user(); 
        return response()->json(['success' => $user], $this-> successStatus); 
    } 
/** 
     * sessions api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function sessions(Request $request) 
    { 
        $validator = Validator::make($request->all(), [ 
            'starting_date'         => 'required', 
            'weekdays'              => 'required', 
            'sessions_per_chapter'  => 'required'
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        } 
        $sessions               = array();
        $no_of_chapters         = 30;
        $input                  = $request->all(); 
        $starting_date          = $input['starting_date'];
        $weekdays               = $input['weekdays'];
        //print_r($weekdays);
        $sessions_per_chapter   = $input['sessions_per_chapter'];
        //solution
        $weekdays=explode(',', $weekdays);
        $total_sessions= $no_of_chapters * $sessions_per_chapter/count($weekdays);//e-g- 180
        //
        $weekday_on_starting_day = date('N', strtotime($starting_date));

        

        for($i=1; $i<=$total_sessions; $i++){
            $pre_week_day=0;
            foreach ($weekdays as  $value) {
                $diff = $value-$pre_week_day;
                $starting_date = date('Y-m-d', strtotime($starting_date. ' + '.$diff.' days'));
                $sessions[]=$starting_date;
                $pre_week_day = $value;
            }
        }
        //echo count($sessions);
        return response()->json(['Sessions'=>$sessions], $this-> successStatus);
    } 
}