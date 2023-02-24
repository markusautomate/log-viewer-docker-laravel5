<?php namespace App\Http\Controllers;

use App\Models\Logs;
use Carbon\Carbon;
use App\Invocation;
use App\Log;
use App\RecentApp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


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


        $logs = "";


        $timestart = mktime(0,0,0,date("m",time()),date("d",time()),date("Y",time()));
        $timeend = strtotime("+1 hour", $timestart);
        $graphData = [];
        $graphLabel = [];
        for ($x = 0; $x <= 23; $x++) {

            $graphData[$x] =  Invocation::whereBetween('timestamp', [
                $timestart,
                $timeend
            ])->count();

            $graphLabel[$x] = date("H:i", $timestart);

            $timestart = $timeend;
            $timeend = strtotime("+1 hour", $timestart);
        }


        return view('home')
            ->with('log_date',$log_date)
            ->with('logs',$logs)
            ->with('appnames',$appnames)
            ->with('graphData',$graphData)
            ->with('graphLabel',$graphLabel);
	}

    public function logdate($log_date){
        $myDate = Carbon::createFromFormat('Y-m-d', $log_date);
        $startOfDayTimestamp = $myDate->startOfDay()->timestamp;

        $appnames = RecentApp::where('user_id', Auth::user()->id)
            ->orderBy('created_at','desc')
            ->take(5)
            ->get();

        $logs = "";

        $timestart = $startOfDayTimestamp;
        $timeend = strtotime("+1 hour", $timestart);
        $graphData = [];
        $graphLabel = [];
        for ($x = 0; $x <= 23; $x++) {

            $graphData[$x] =  Invocation::whereBetween('timestamp', [
                $timestart,
                $timeend
            ])->count();

            $graphLabel[$x] = date("H:i", $timestart);

            $timestart = $timeend;
            $timeend = strtotime("+1 hour", $timestart);
        }


        return view('home')
            ->with('log_date',$log_date)
            ->with('logs',$logs)
            ->with('appnames',$appnames)
            ->with('graphData',$graphData)
            ->with('graphLabel',$graphLabel);
    }

    public function filters(Request $request)
    {

        $log_date = $request->date_log;
        $appId = $request->appid;
        $username= $request->user;
        $logLevel= $request->level;
        $search= $request->search;


        if ($username=="0" || $username=="undefined")
        {
            $username = "";

        }
        if ($search=="unknown" || $search=="undefined")
        {
            $search = "";

        }
        $logLevel = json_decode($logLevel);
        if (count($logLevel)>0&&count($logLevel)<9)
        {

        }

        $from    = Carbon::parse($log_date)
            ->startOfDay()        // 2018-09-29 00:00:00.000000
            ->toDateTimeString(); // 2018-09-29 00:00:00

        $to      = Carbon::parse($log_date)
            ->endOfDay()          // 2018-09-29 23:59:59.000000
            ->toDateTimeString(); // 2018-09-29 23:59:59

        if ($username!="" && $search=="")
        {

            $logs = \DB::table('log')
                ->join('invoke', 'invoke.id', '=', 'log.invoke_id')
                ->select('log.*', 'invoke.event', 'invoke.appName', 'invoke.userName', 'invoke.sid1', 'invoke.sid2')
                ->whereBetween('log.time', [
                    strtotime($from),strtotime($to)
                ])
                ->where('invoke.appName',$appId)
                ->where('invoke.userName',$username)
                ->paginate(15);
        }
        elseif ($username!="" && $search!="")
        {

            $logs = \DB::table('log')
                ->join('invoke', 'invoke.id', '=', 'log.invoke_id')
                ->select('log.*', 'invoke.event', 'invoke.appName', 'invoke.userName', 'invoke.sid1', 'invoke.sid2')
                ->whereBetween('log.time', [
                    strtotime($from),strtotime($to)
                ])
                ->where('invoke.appName',$appId)
                ->where('invoke.userName',$username)
                ->where('log.message','LIKE',"%".$search."%")
                ->orWhere('invoke.event','LIKE',"%".$search."%")
                ->paginate(15);
        }
        elseif ($username=="" && $search!="")
        {

            $logs = \DB::table('log')
                ->join('invoke', 'invoke.id', '=', 'log.invoke_id')
                ->select('log.*', 'invoke.event', 'invoke.appName', 'invoke.userName', 'invoke.sid1', 'invoke.sid2')
                ->whereBetween('log.time', [
                    strtotime($from),strtotime($to)
                ])
                ->where('invoke.appName',$appId)
                ->where('log.message','LIKE',"%".$search."%")
                ->orWhere('invoke.event','LIKE',"%".$search."%")
                ->paginate(15);
        }
        else
        {

            $logs = \DB::table('log')
                ->join('invoke', 'invoke.id', '=', 'log.invoke_id')
                ->select('log.*', 'invoke.event', 'invoke.appName', 'invoke.userName', 'invoke.sid1', 'invoke.sid2')
                ->whereBetween('log.time', [
                    strtotime($from),strtotime($to)
                ])
                ->where('invoke.appName',$appId)
                ->paginate(2);
        }


        $data = "";
        foreach ($logs as $log) {
                $data = $data . "<div class='mb-2'>
                     <p><button  class='btn btn-xs btn-primary'  onclick='invoke(this.id)' id='" . $log->invoke_id . "'>[" . sprintf('%05d', $log->invoke_id) . "]</button><strong>" . $log->event . " - " . $log->appName . " - " . $log->userName . ($log->sid1 != "0" ? " - <button  class='btn btn-xs btn-warning'  onclick='sids(this.id)' id='" . $log->sid1 . "'>" . $log->sid1 . "</button>" : "") . ($log->sid2 != "0" ? " - <button  class='btn btn-xs btn-success'  onclick='sids(this.id)' id='" . $log->sid2 . "'>" . $log->sid2 . "</button>" : "") . "</strong></p>
                     <div class='flex'>
                         <div class='font-semibold w-1/4 px-2 pt-1 rounded log-" . strtolower(\Monolog\Logger::getLevelName($log->level)) . "'><p class='text-sm'><i class='fa fa-exclamation-triangle'></i>" . \Monolog\Logger::getLevelName($log->level) . "</p></div>
                         <div class='w-3/4 px-2'>
                             <p style='white-space: pre-wrap'>" . $log->message . "</p>
                         </div>
                     </div
                     <p class='text-xs'>" . date('Y/m/d h:i:s a', $log->time) . " - " . config('app.timezone') . "</p>
                     <hr>
                 </div>";
        }

        $userFilter= "";

        $userNames = $this->array_column( json_decode(json_encode($logs), true),"userName");

        $users = array_unique($userNames);

        foreach ($users as $userID)
        {
            if($username==$userID)
            {
                $userFilter = $userFilter ."<button id='". $userID ."'  onclick='updateUserfilter(this.id)' class='btn btn-secondary' style='width: 100%'>". $userID ."</button>";
            }
            else{
                $userFilter = $userFilter ."<button id='". $userID ."'  onclick='updateUserfilter(this.id)' class='btn btn-primary' style='width: 100%'>". $userID ."</button>";
            }

        }

        $appnames = RecentApp::where('user_id', Auth::user()->id)
            ->orderBy('created_at','desc')
            ->take(5)
            ->get();

        $appIDlist = '';

        foreach ($appnames as $appname)
        {
            if($appname->appName==$appId)
            {
                $appIDlist = $appIDlist ."<button id='". $appname->appName ."'  onclick='updateAppIDfilter(this.id)' class='btn btn-secondary' style='width: 100%'>". $appname->appName ."</button>";
            }
            else{
                $appIDlist = $appIDlist ."<button id='". $appname->appName ."'  onclick='updateAppIDfilter(this.id)' class='btn btn-primary' style='width: 100%'>". $appname->appName ."</button>";
            }

        }
        $myDate = Carbon::createFromFormat('Y-m-d', $log_date);
        $timestart = $myDate->startOfDay()->timestamp;

        $timeend = strtotime("+1 hour", $timestart);
        $graphData = [];
        $graphLabel = [];
        for ($x = 0; $x <= 23; $x++) {

            $graphData[$x] =  Invocation::whereBetween('timestamp', [
                $timestart,
                $timeend
            ])->count();

            $graphLabel[$x] = date("H:i", $timestart);

            $timestart = $timeend;
            $timeend = strtotime("+1 hour", $timestart);
        }

        return view('home2')
            ->with('log_date',$log_date)
            ->with('logs',$logs)
            ->with('users',$users)
            ->with('appnames',$appnames)
            ->with('graphData',$graphData)
            ->with('graphLabel',$graphLabel);

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


    //public function filters($log_date,$appId,$username,$logLevel,$search)
    public function filterTwo(Request $request)
    {

        $log_date = $request->date_log;
        $appId = $request->appid;
        $username= $request->user;
        $logLevel= $request->level;
        $search= $request->search;
        $load = $request->load;




        $from    = Carbon::parse($log_date)
            ->startOfDay()        // 2018-09-29 00:00:00.000000
            ->toDateTimeString(); // 2018-09-29 00:00:00

        $to      = Carbon::parse($log_date)
            ->endOfDay()          // 2018-09-29 23:59:59.000000
            ->toDateTimeString(); // 2018-09-29 23:59:59


//        $logs = Log::all()->filter(function (Log $logs) use ($log_date) {
//            $uploadedAt = Carbon::createFromTimestamp($logs->invoker->timestamp)->format('Y-m-d');
//            return $uploadedAt === $log_date;
//        });


        if ($search=="unknown" || $search=="undefined")
        {
            $search= "";
        }



        if ($appId!="0" && $appId!="undefined")
        {

            $newRecent = RecentApp::where('user_id', Auth::user()->id)
            ->where('appname',$appId)
            ->first();

            if (!$newRecent)
            {
                $newRecent = new RecentApp();
                $newRecent->user_id = Auth::user()->id;
                $newRecent->appName = $appId;
                $newRecent->save();
            }
            else
            {
                $newRecent->created_at = Carbon::now();
                $newRecent->save();
            }
        }

        if ($username!="0" && $username!="undefined")
        {
            $invocations =  Invocation::whereBetween('timestamp', [
                strtotime($from),strtotime($to)
            ])
                ->where('appName',$appId)
                ->where('event','LIKE','%'.$search.'%')
                ->where('userName',$username)->get();

        }
        else
        {
            $invocations =  Invocation::whereBetween('timestamp', [
                strtotime($from),strtotime($to)
            ])
                ->where('event','LIKE','%'.$search.'%')
                ->where('appName',$appId)->get();
        }

        $appnames = RecentApp::where('user_id', Auth::user()->id)
            ->orderBy('created_at','desc')
            ->take(5)
            ->get();

        $appIDlist = '';

        foreach ($appnames as $appname)
        {
            if($appname->appName==$appId)
            {
                $appIDlist = $appIDlist ."<button id='". $appname->appName ."'  onclick='updateAppIDfilter(this.id)' class='btn btn-secondary' style='width: 100%'>". $appname->appName ."</button>";
            }
            else{
                $appIDlist = $appIDlist ."<button id='". $appname->appName ."'  onclick='updateAppIDfilter(this.id)' class='btn btn-primary' style='width: 100%'>". $appname->appName ."</button>";
            }

        }


        $userFilter = '';

        $userNames = $invocations->groupBy('userName');

        $users = array_keys(json_decode($userNames, true));

        foreach ($users as $userID)
        {
            if($username==$userID)
            {
                $userFilter = $userFilter ."<button id='". $userID ."'  onclick='updateUserfilter(this.id)' class='btn btn-secondary' style='width: 100%'>". $userID ."</button>";
            }
            else{
                $userFilter = $userFilter ."<button id='". $userID ."'  onclick='updateUserfilter(this.id)' class='btn btn-primary' style='width: 100%'>". $userID ."</button>";
            }

        }


        $logLevel = json_decode($logLevel);
        $filterLevel = false;
        if (count($logLevel)>0&&count($logLevel)<9)
        {
            $filterLevel = true;
        }

        if ( count($invocations)==0)
        {
            $data = "<h1 class='text-center'>No data available.</h1>
                    <p class='text-center'>Please try another filter.</p>";

            $result = [$userFilter,$data,$appIDlist];

            return response()->json($result);
        }

        if (($appId=="0" || $appId=="undefined") && ($username=="0" || $username=="undefined") && ($logLevel=="0" || $logLevel=="undefined")&& ($search=="unknown" || $search=="undefined"))
        {
            $data = "<h1 class='text-center'>Welcome to TurboDial Log Viewer.</h1>
                    <p class='text-center'>Start by searching for an AppName on the filter panel.</p>";

            $result = [$userFilter,$data,$appIDlist];

            return response()->json($result);
        }
        $inCounter = 0;
        $counter = 1;
        $lastCounter = 10;
        if ($load!=0)
        {
            $counter = 10 * $load +1;
            $lastCounter = $counter + 9;
        }

        $data = "";
        if ($filterLevel)
        {
            foreach ($invocations as $invocation) {
                $logs = $invocation->logs;
                foreach ($logs as $log) {
                    if (!in_array($log->level, $logLevel)) {
                        $inCounter++;
                        if ($inCounter < $counter) {
                            continue;
                        }
                        $data = $data . "<div class='mb-2'>
                     <p><button  class='btn btn-xs btn-primary'  onclick='invoke(this.id)' id='" . $log->invoker->id . "'>[" . sprintf('%05d', $log->invoker->id) . "]</button><strong>" . $log->invoker->event . " - " . $log->invoker->appName . " - " . $log->invoker->userName . ($log->invoker->sid1 != "0" ? " - <button  class='btn btn-xs btn-warning'  onclick='sids(this.id)' id='" . $log->invoker->sid1 . "'>" . $log->invoker->sid1 . "</button>" : "") . ($log->invoker->sid2 != "0" ? " - <button  class='btn btn-xs btn-success'  onclick='sids(this.id)' id='" . $log->invoker->sid2 . "'>" . $log->invoker->sid2 . "</button>" : "") . "</strong></p>
                     <div class='flex'>
                         <div class='font-semibold w-1/4 px-2 pt-1 rounded log-" . strtolower(\Monolog\Logger::getLevelName($log->level)) . "'><p class='text-sm'><i class='fa fa-exclamation-triangle'></i>" . \Monolog\Logger::getLevelName($log->level) . "</p></div>
                         <div class='w-3/4 px-2'>
                             <p style='white-space: pre-wrap'>" . $log->message . "</p>
                         </div>
                     </div
                     <p class='text-xs'>" . date('Y/m/d h:i:s a', $log->time) . " - " . config('app.timezone') . "</p>
                     <hr>
                 </div>";

                        if ($inCounter == $lastCounter) break;
                    }
                }
                if ($inCounter == $lastCounter) break;
            }
            if ($inCounter == $lastCounter) {
                $data = $data . "<button id='load_more|" . ($load + 1) . "' onclick='loadMore(this.id)' class='btn btn-primary btn-sm'>Load More...</button>";
            } else {
                $data = $data . "<p>End of Results.</p>";
            }

            $result = [$userFilter, $data, $appIDlist];

            return response()->json($result);
        }
        else {
            foreach ($invocations as $invocation) {
                $logs = $invocation->logs;
                foreach ($logs as $log) {

                        $inCounter++;
                        if ($inCounter < $counter) {
                            continue;
                        }
                        $data = $data . "<div class='mb-2'>
                     <p><button  class='btn btn-xs btn-primary'  onclick='invoke(this.id)' id='" . $log->invoker->id . "'>[" . sprintf('%05d', $log->invoker->id) . "]</button><strong>" . $log->invoker->event . " - " . $log->invoker->appName . " - " . $log->invoker->userName . ($log->invoker->sid1 != "0" ? " - <button  class='btn btn-xs btn-warning'  onclick='sids(this.id)' id='" . $log->invoker->sid1 . "'>" . $log->invoker->sid1 . "</button>" : "") . ($log->invoker->sid2 != "0" ? " - <button  class='btn btn-xs btn-success'  onclick='sids(this.id)' id='" . $log->invoker->sid2 . "'>" . $log->invoker->sid2 . "</button>" : "") . "</strong></p>
                     <div class='flex'>
                         <div class='font-semibold w-1/4 px-2 pt-1 rounded log-" . strtolower(\Monolog\Logger::getLevelName($log->level)) . "'><p class='text-sm'><i class='fa fa-exclamation-triangle'></i>" . \Monolog\Logger::getLevelName($log->level) . "</p></div>
                         <div class='w-3/4 px-2'>
                             <p style='white-space: pre-wrap'>" . $log->message . "</p>
                         </div>
                     </div
                     <p class='text-xs'>" . date('Y/m/d h:i:s a', $log->time) . " - " . config('app.timezone') . "</p>
                     <hr>
                 </div>";

                        if ($inCounter == $lastCounter) break;

                }
                if ($inCounter == $lastCounter) break;
            }
            if ($inCounter == $lastCounter) {
                $data = $data . "<button id='load_more|" . ($load + 1) . "' onclick='loadMore(this.id)' class='btn btn-primary btn-sm'>Load More...</button>";
            } else {
                $data = $data . "<p>End of Results.</p>";
            }

            $result = [$userFilter, $data, $appIDlist];

            return response()->json($result);
        }

    }

    public function sids(Request $request)
    {
        $sids = $request->sids;


        $invocations = Invocation::where('sid1',$sids)
            ->orWhere('sid2',$sids)
            ->get();

        $logLevel = json_decode($request->level);
        $filterLevel = false;
        if (count($logLevel)>0&&count($logLevel)<9)
        {
            $filterLevel = true;
        }


        if ( count($invocations)==0)
        {
            $data = "<h1 class='text-center'><button onclick='filter()'  class='btn btn-primary'><i class='glyphicon glyphicon-refresh'></i></button>No data available.</h1>
                    <p class='text-center'>Showing logs with Invoke ID:".$sids."</p>";

            $result = [$data];

            return response()->json($result);
        }

        $data = "<h4><button onclick='filter()'  class='btn btn-primary'><i class='glyphicon glyphicon-refresh'></i></button>Showing logs with Sids:<span id='sids_filter' value='".$sids."'>" .$sids."</span></h4>";
        if ($filterLevel)
        {
            foreach ($invocations as $invocation) {
                $logs = $invocation->logs;

                foreach ($logs as $log) {
                    if (!in_array($log->level, $logLevel))
                    {
                        $data = $data . "<div class='mb-2'>
                         <p><button  class='btn btn-xs btn-primary'  onclick='invoke(this.id)' id='" . $log->invoker->id . "'>[" . sprintf('%05d', $log->invoker->id) . "]</button><strong>" . $log->invoker->event . " - " . $log->invoker->appName . " - " . $log->invoker->userName . ($log->invoker->sid1 != "0" ? " - <button  class='btn btn-xs btn-warning'  onclick='sids(this.id)' id='" . $log->invoker->sid1 . "'>" . $log->invoker->sid1 . "</button>" : "") . ($log->invoker->sid2 != "0" ? " - <button  class='btn btn-xs btn-success'  onclick='sids(this.id)' id='" . $log->invoker->sid2 . "'>" . $log->invoker->sid2 . "</button>" : "") . "</strong></p>
                         <div class='flex'>
                             <div class='font-semibold w-1/4 px-2 pt-1 rounded log-" . strtolower(\Monolog\Logger::getLevelName($log->level)) . "'><p class='text-sm'><i class='fa fa-exclamation-triangle'></i>" . \Monolog\Logger::getLevelName($log->level) . "</p></div>
                             <div class='w-3/4 px-2'>
                                 <p style='white-space: pre-wrap'>" . $log->message . "</p>
                             </div>
                         </div
                         <p class='text-xs'>" . date('Y/m/d h:i:s a', $log->time) . " - " . config('app.timezone') . "</p>
                         <hr>
                     </div>";
                    }
                }
            }
            $result = [$data];

            return response()->json($result);
        }
        else
        {
            foreach ($invocations as $invocation) {
                $logs = $invocation->logs;

                foreach ($logs as $log) {

                        $data = $data . "<div class='mb-2'>
                         <p><button  class='btn btn-xs btn-primary'  onclick='invoke(this.id)' id='" . $log->invoker->id . "'>[" . sprintf('%05d', $log->invoker->id) . "]</button><strong>" . $log->invoker->event . " - " . $log->invoker->appName . " - " . $log->invoker->userName . ($log->invoker->sid1 != "0" ? " - <button  class='btn btn-xs btn-warning'  onclick='sids(this.id)' id='" . $log->invoker->sid1 . "'>" . $log->invoker->sid1 . "</button>" : "") . ($log->invoker->sid2 != "0" ? " - <button  class='btn btn-xs btn-success'  onclick='sids(this.id)' id='" . $log->invoker->sid2 . "'>" . $log->invoker->sid2 . "</button>" : "") . "</strong></p>
                         <div class='flex'>
                             <div class='font-semibold w-1/4 px-2 pt-1 rounded log-" . strtolower(\Monolog\Logger::getLevelName($log->level)) . "'><p class='text-sm'><i class='fa fa-exclamation-triangle'></i>" . \Monolog\Logger::getLevelName($log->level) . "</p></div>
                             <div class='w-3/4 px-2'>
                                 <p style='white-space: pre-wrap'>" . $log->message . "</p>
                             </div>
                         </div
                         <p class='text-xs'>" . date('Y/m/d h:i:s a', $log->time) . " - " . config('app.timezone') . "</p>
                         <hr>
                     </div>";

                }
            }
            $result = [$data];

            return response()->json($result);
        }

    }

    public function invoke(Request $request)
    {
        $id = $request->invoke_id;

        $invocations = Invocation::find($id);

        $logLevel = json_decode($request->level);
        $filterLevel = false;
        if (count($logLevel)>0&&count($logLevel)<9)
        {
            $filterLevel = true;
        }

        if ( count($invocations)==0)
        {
            $data = "<h1 class='text-center'><button onclick='filter()'  class='btn btn-primary'><i class='glyphicon glyphicon-refresh'></i></button>No data available.</h1>
                    <p class='text-center'>Showing logs with Invoke ID:".sprintf('%05d', $id)."</p>";

            $result = [$data];

            return response()->json($result);
        }

        $data = "<h4><button onclick='filter()'  class='btn btn-primary'><i class='glyphicon glyphicon-refresh'></i></button>Showing logs with Invoke ID:<span id='invoke_id_filter' value='".$id."'>" .$id."</span></h4>";
        $logs = $invocations->logs;
        if ($filterLevel)
        {
            foreach ($logs as $log)
            {
                if (!in_array($log->level, $logLevel)) {
                    $data = $data . "<div class='mb-2'>
                     <p><button  class='btn btn-xs btn-primary'  onclick='invoke(this.id)' id='" . $log->invoker->id . "'>[" . sprintf('%05d', $log->invoker->id) . "]</button><strong>" . $log->invoker->event . " - " . $log->invoker->appName . " - " . $log->invoker->userName . ($log->invoker->sid1 != "0" ? " - <button  class='btn btn-xs btn-warning'  onclick='sids(this.id)' id='" . $log->invoker->sid1 . "'>" . $log->invoker->sid1 . "</button>" : "") . ($log->invoker->sid2 != "0" ? " - <button  class='btn btn-xs btn-success'  onclick='sids(this.id)' id='" . $log->invoker->sid2 . "'>" . $log->invoker->sid2 . "</button>" : "") . "</strong></p>
                     <div class='flex'>
                         <div class='font-semibold w-1/4 px-2 pt-1 rounded log-" . strtolower(\Monolog\Logger::getLevelName($log->level)) . "'><p class='text-sm'><i class='fa fa-exclamation-triangle'></i>" . \Monolog\Logger::getLevelName($log->level) . "</p></div>
                         <div class='w-3/4 px-2'>
                             <p style='white-space: pre-wrap'>" . $log->message . "</p>
                         </div>
                     </div
                     <p class='text-xs'>" . date('Y/m/d h:i:s a', $log->time) . " - " . config('app.timezone') . "</p>
                     <hr>
                 </div>";
                }
            }

            $result = [$data];

            return response()->json($result);
        }
        else
        {
            foreach ($logs as $log)
            {

                    $data = $data . "<div class='mb-2'>
                     <p><button  class='btn btn-xs btn-primary'  onclick='invoke(this.id)' id='" . $log->invoker->id . "'>[" . sprintf('%05d', $log->invoker->id) . "]</button><strong>" . $log->invoker->event . " - " . $log->invoker->appName . " - " . $log->invoker->userName . ($log->invoker->sid1 != "0" ? " - <button  class='btn btn-xs btn-warning'  onclick='sids(this.id)' id='" . $log->invoker->sid1 . "'>" . $log->invoker->sid1 . "</button>" : "") . ($log->invoker->sid2 != "0" ? " - <button  class='btn btn-xs btn-success'  onclick='sids(this.id)' id='" . $log->invoker->sid2 . "'>" . $log->invoker->sid2 . "</button>" : "") . "</strong></p>
                     <div class='flex'>
                         <div class='font-semibold w-1/4 px-2 pt-1 rounded log-" . strtolower(\Monolog\Logger::getLevelName($log->level)) . "'><p class='text-sm'><i class='fa fa-exclamation-triangle'></i>" . \Monolog\Logger::getLevelName($log->level) . "</p></div>
                         <div class='w-3/4 px-2'>
                             <p style='white-space: pre-wrap'>" . $log->message . "</p>
                         </div>
                     </div
                     <p class='text-xs'>" . date('Y/m/d h:i:s a', $log->time) . " - " . config('app.timezone') . "</p>
                     <hr>
                 </div>";

            }

            $result = [$data];

            return response()->json($result);
        }

    }

}
