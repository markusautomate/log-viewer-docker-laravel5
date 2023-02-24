<?php namespace App\Http\Controllers;

use App\Models\Logs;
use Carbon\Carbon;
use App\Invocation;
use App\RecentApp;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;
use Illuminate\Support\Arr;



class HomeController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
        // check if session expired for ajax request
        $this->middleware('ajax-session-expired');

		$this->middleware('auth');
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
        $log_date = date('Y-m-d',time());

        $appnames = RecentApp::where('user_id', Auth::user()->id)
            ->orderBy('created_at','desc')
            ->take(5)
            ->get();

        return view('home')
            ->with('log_date',$log_date)
            ->with('appnames',$appnames);
	}


    public function logdate($log_date){

        $appnames = RecentApp::where('user_id', Auth::user()->id)
            ->orderBy('created_at','desc')
            ->take(5)
            ->get();

        return view('home')
            ->with('log_date',$log_date)
            ->with('appnames',$appnames);
    }

    public function filters(Request $request)
    {
        if ($request->ajax()) {

            $log_date = $request->date_log;
            $appId = $request->appid;
            $username = $request->user;
            $logLevel = $request->level;
            $global = $request->global;
            $search = $request->search;
            $content = $request->contentSearch;
            $page = $request->load;
            $sort = $request->sort;


            if ($username == "0" || $username == "undefined") {
                $username = "";

            }
            if ($content == "unknown" || $content == "undefined") {
                $content = "";

            }
            if ($search == "unknown" || $search == "undefined") {
                $search = "";

            }
            $logLevel = json_decode($logLevel);

            if (count($logLevel) == 0) {
                $logLevel = "";
            }

            $from = Carbon::parse($log_date)
                ->startOfDay()        // 2018-09-29 00:00:00.000000
                ->toDateTimeString(); // 2018-09-29 00:00:00

            $to = Carbon::parse($log_date)
                ->endOfDay()          // 2018-09-29 23:59:59.000000
                ->toDateTimeString(); // 2018-09-29 23:59:59

            try {

                $query = \DB::table('log')
                    ->join('invoke', 'invoke.id', '=', 'log.invoke_id')
                    ->select('log.message','log.level','log.invoke_id','log.time', 'invoke.event','invoke.appName', 'invoke.userName', 'invoke.sid1', 'invoke.sid2')
                    ->where('log.time', '>', strtotime($from))
                    ->where('log.time', '<', strtotime($to));
                if (!$global) {
                    $query = $query->where('invoke.appName', $appId);
                    if ($username != "") {
                        $query = $query->where('invoke.userName', $username);
                    }
                    if ($search != "") {
                        $query = $query->where('invoke.event', 'LIKE', "%" . $search . "%");
                    }
                }
                if ($logLevel != "") {
                    array_push($logLevel,0);
                    $query = $query->whereIn('log.level', $logLevel);
                }
                if ($content != "" && $content != "all") {
                    $query = $query->where('log.message', 'LIKE', "%" . $content . "%");
                }
                if ($sort)
                {
                    $query = $query->orderBy('log.time','desc');
                }
                $timestart = microtime(true);
                if ($page!=0)
                {
                    Paginator::currentPageResolver(function() use ($page) {
                        return $page;
                    });

                    $logs = $query->paginate(30);
                }
                else
                {
                    $logs = $query->paginate(30);
                }
                $time = microtime(true)-$timestart;

//                $view =  view('log_data', compact('logs'))->render();
//                return response()->json($view);
                return response()->json(['success'=>true,'logs'=>$logs->toArray(),'pagination'=>$logs->render(), 'time'=>$time, 'query'=>$query]);
            } catch (Exception $e) {

                return response()->json($e->getMessage());
            }

        }
    }

    public function getUsers(Request $request)
    {

        $log_date = $request->date_log;
        $appName = $request->appName;

        $newRecent = RecentApp::where('user_id', Auth::user()->id)
            ->where('appname',$appName)
            ->first();

        if (!$newRecent)
        {
            $newRecent = new RecentApp();
            $newRecent->user_id = Auth::user()->id;
            $newRecent->appName = $appName;
            $newRecent->save();
        }
        else
        {
            $newRecent->created_at = Carbon::now();
            $newRecent->save();
        }

        $appnames = RecentApp::where('user_id', Auth::user()->id)
            ->orderBy('created_at','desc')
            ->take(5)
            ->get();

        $from = Carbon::createFromFormat('Y-m-d', $log_date)
            ->startOfDay()        // 2018-09-29 00:00:00.000000
            ->timestamp; // 2018-09-29 00:00:00

        $to = Carbon::createFromFormat('Y-m-d', $log_date)
            ->endOfDay()          // 2018-09-29 23:59:59.000000
            ->timestamp; // 2018-09-29 23:59:59

        $users = Invocation::select('userName')->where('appName',$appName)
            ->where('timestamp','>',($from))
            ->where('timestamp','<',($to))
            ->distinct()->get();

        $appnames = $this->array_column( json_decode(json_encode($appnames), true),"appName");
        $userNames = $this->array_column( json_decode(json_encode($users), true),"userName");
        $user = array_unique($userNames);



        return response()->json(['users'=>$user,'appnames'=>$appnames]);
    }

    function array_column(array $input, $columnKey, $indexKey = null) {
        $array = array();
        foreach ($input as $value) {
            if ( !array_key_exists($columnKey, $value)) {
                trigger_error("Key \"$columnKey\" does not exist in array");
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            }
            else {
                if ( !array_key_exists($indexKey, $value)) {
                    trigger_error("Key \"$indexKey\" does not exist in array");
                    return false;
                }
                if ( ! is_scalar($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not contain scalar value");
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }

    public function sids(Request $request)
    {
        $sids = $request->sids;
        $log_date = $request->date_log;
        $logLevel = json_decode($request->level);

        if (count($logLevel)==0)
        {
            $logLevel = "";
        }

        $from = Carbon::parse($log_date)
            ->startOfDay()        // 2018-09-29 00:00:00.000000
            ->toDateTimeString(); // 2018-09-29 00:00:00

        $to = Carbon::parse($log_date)
            ->endOfDay()          // 2018-09-29 23:59:59.000000
            ->toDateTimeString(); // 2018-09-29 23:59:59

        $query = \DB::table('log')
            ->join('invoke', 'invoke.id', '=', 'log.invoke_id')
            ->select('log.message','log.level','log.invoke_id','log.time', 'invoke.event','invoke.appName', 'invoke.userName', 'invoke.sid1', 'invoke.sid2')
            ->where('log.time', '>', strtotime($from))
            ->where('log.time', '<', strtotime($to));

        if ($logLevel!="") {
            array_push($logLevel,0);
            $query = $query->whereIn('log.level', $logLevel);
        }

        $query = $query->where('invoke.sid1',$sids)
            ->orWhere('invoke.sid2',$sids);
        $timestart = microtime(true);
        $logs = $query->paginate(30);

        $time = microtime(true)-$timestart;

//                $view =  view('log_data', compact('logs'))->render();
//                return response()->json($view);
        return response()->json(['success'=>true,'logs'=>$logs->toArray(),'pagination'=>$logs->render(), 'time'=>$time]);

    }

    public function invoke(Request $request)
    {
        $log_date = $request->date_log;
        $id = $request->invoke_id;
        $logLevel = json_decode($request->level);

        if (count($logLevel)==0)
        {
            $logLevel = "";
        }

        $from = Carbon::parse($log_date)
            ->startOfDay()        // 2018-09-29 00:00:00.000000
            ->toDateTimeString(); // 2018-09-29 00:00:00

        $to = Carbon::parse($log_date)
            ->endOfDay()          // 2018-09-29 23:59:59.000000
            ->toDateTimeString(); // 2018-09-29 23:59:59

        $query = \DB::table('log')
            ->join('invoke', 'invoke.id', '=', 'log.invoke_id')
            ->select('log.message','log.level','log.invoke_id','log.time', 'invoke.event','invoke.appName', 'invoke.userName', 'invoke.sid1', 'invoke.sid2')
            ->where('log.time', '>', strtotime($from))
            ->where('log.time', '<', strtotime($to))
            ->where('invoke.id',$id);

        if ($logLevel!="") {
            array_push($logLevel,0);
            $query = $query->whereIn('log.level', $logLevel);
        }

        $timestart = microtime(true);
        $logs = $query->get();

        $time = microtime(true)-$timestart;

//                $view =  view('log_data', compact('logs'))->render();
//                return response()->json($view);
        return response()->json(['success'=>true,'logs'=>$logs, 'time'=>$time]);



    }

    public function graphData(Request $request)
    {

        $log_date = $request->date_log;
        $appName = $request->appName;


        $from = Carbon::createFromFormat('Y-m-d', $log_date)
            ->startOfDay()        // 2018-09-29 00:00:00.000000
            ->timestamp; // 2018-09-29 00:00:00

        $to = Carbon::createFromFormat('Y-m-d', $log_date)
            ->endOfDay()          // 2018-09-29 23:59:59.000000
            ->timestamp; // 2018-09-29 23:59:59


        if ($appName == "all")
        {
            $invocations = \DB::table('invoke')
                ->selectRaw('COUNT(id) count, DATE_FORMAT(FROM_UNIXTIME(timestamp), "%H") hour')
                ->where('timestamp','>',$from)
                ->where('timestamp','<',$to)
                ->groupBy(\DB::raw('DATE_FORMAT(FROM_UNIXTIME(timestamp), "%H")'))
                ->get();
        }
        else
        {
            $invocations = \DB::table('invoke')
                ->selectRaw('COUNT(id) count, DATE_FORMAT(FROM_UNIXTIME(timestamp), "%H") hour')
                ->where('timestamp','>',$from)
                ->where('timestamp','<',$to)
                ->where('appName',$appName)
                ->groupBy(\DB::raw('DATE_FORMAT(FROM_UNIXTIME(timestamp), "%H")'))
                ->get();
        }



        $array=[];

        foreach ($invocations as $invokes)
        {
            $array[$invokes->hour] = $invokes->count;
        }


        $graphData = [];
        $totalInvokes = 0;
        $countHr = 0;
        for ($x = 0; $x <= 23; $x++) {

            $count = 0;
            if (array_key_exists(sprintf('%02d', $x), $array)) $count=$array[sprintf('%02d', $x)];
            $graphData[$x] = $count;

            $totalInvokes = $totalInvokes + $graphData[$x];
            if ( $graphData[$x]>1)
            {
                $countHr++;
            }

        }


        if ($countHr>0) {
            $averageInvoke = $totalInvokes / $countHr;
        }
        else
        {
            $averageInvoke = 0;
        }

        $totalInvoke = number_format($totalInvokes,0,".",",") ;
        $averageInvoke = number_format($averageInvoke,0,".",",") ;

        return response()->json(['graphData'=>$graphData,'invokeRate'=>$averageInvoke,'invokeCount'=>$totalInvoke]);
    }

}
