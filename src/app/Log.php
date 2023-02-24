<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model {

	//
    protected $table = 'log';
    public $timestamps = false;

    public function invoker()
    {
        return $this->belongsTo('App\Invocation','invoke_id','id');
    }

}
