<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Invocation extends Model {

	//
    protected $table = 'invoke';
    public $timestamps = false;

    public function logs()
    {
        return $this->hasMany('App\Log','invoke_id','id');
    }

}
