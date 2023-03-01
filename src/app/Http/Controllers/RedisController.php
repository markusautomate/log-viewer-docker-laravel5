<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class RedisController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\View\View
	 */
	public function index()
	{
		//
        $log_date = date('Y-m-d', time());

        $keys = Redis::keys('*'.$log_date.'*');
        $apps = [];
        $events = [];

        foreach ($keys as $key)
        {
            $split = explode( ':',$key);

            if (array_search($split[2], $apps)===false)
            {
                array_push($apps, $split[2]);
            }

            if (array_search($split[3], $events)===false)
            {
                array_push($events, $split[3]);
            }
        }

        return view('redis')
            ->with('keys', $keys)
            ->with('apps', $apps)
            ->with('events', $events);
	}


    public function logdate($log_date)
    {
        $keys = Redis::keys('*'.$log_date.'*');
        $apps = [];
        $events = [];

        foreach ($keys as $key)
        {
            $split = explode( ':',$key);

            if (array_search($split[2], $apps)===false)
            {
                array_push($apps, $split[2]);
            }

            if (array_search($split[3], $events)===false)
            {
                array_push($events, $split[3]);
            }
        }

        return view('redis')
            ->with('keys', $keys)
            ->with('apps', $apps)
            ->with('events', $events);
    }

    public function filters(Request $request)
    {

        $log_date = $request->date_log;
        $appId    = $request->appid;
        $event = $request->event;
        $logLevel = $request->level;
        $search   = $request->search;

        $logs = Redis::lrange('log:'.$log_date.':'.$appId.':'.$event, 0,-1);

        return ($logs);

        $data = "";
        foreach ($logs as $log) {
            $obj = json_decode($log);

            $data = $data."<div class='mb-2'>
                     <div class='flex'>
                         <div class='font-semibold w-1/4 px-2 pt-1 rounded log-".strtolower($obj->level_name)."'><p class='text-sm'><i class='fa fa-exclamation-triangle'></i>".$obj->level_name."</p></div>
                         <div class='w-3/4 px-2'>
                             <p style='white-space: pre-wrap'>".$obj->message."</p>
                         </div>
                     </div
                     <p class='text-xs'>".date('Y/m/d h:i:s a', strtotime($obj->datetime))." - ".config('app.timezone')."</p>
                     <hr>
                 </div>";
        }

        $result = [$data];

        return response()->json($result);
    }


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
