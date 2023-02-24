<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Invocation;
use App\Log;
use Illuminate\Http\Request;

class DashboardController extends Controller {

	public function dashboard()
    {

    }

    public function invoke_list()
    {
        $invokes = Invocation::paginate(50);
        return view('invokes.show')->with('invokes',$invokes);
    }

    public function log_list()
    {
        $invokes = Log::where('invoke_id',0)->get();

        return $invokes;
    }

    public function log_delete()
    {
        $invokes = Log::where('invoke_id',0)->delete();

        return "OKAY";
    }


}
