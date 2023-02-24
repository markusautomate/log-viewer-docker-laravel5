<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Invocation;
use App\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller {

	public function dashboard()
    {

    }

    public function invoke_list()
    {
        $invokes = DB::table('invoke')
            ->select('event', DB::raw('count(id) as total'))
            ->groupBy('event')
            ->orderBy('total','desc')
            ->get();

        return view('invokes.show')->with('invokes',$invokes);
    }

    public function log_list()
    {   $log_date = "2022/01/25";
        $time = microtime(true);
        $from    = Carbon::parse($log_date)
            ->startOfDay()        // 2018-09-29 00:00:00.000000
            ->toDateTimeString(); // 2018-09-29 00:00:00

        $to      = Carbon::parse($log_date)
            ->endOfDay()          // 2018-09-29 23:59:59.000000
            ->toDateTimeString(); // 2018-09-29 23:59:59

        $logs = Log::whereBetween('time', [
            strtotime($from),strtotime($to)
        ])
            ->where('level',200)
//            ->whereHas('invoker', function($q)
//        {
//            $q->where('appName', '=','wl116');
//
//        })
        ->simplePaginate(100);

        $time = microtime(true)-$time;
        return view('logs.show')->with('logs',$logs)->with('time',$time);
    }

    public function log_delete()
    {
        $invokes = Log::where('invoke_id',0)->delete();

        return "OKAY";
    }


}
