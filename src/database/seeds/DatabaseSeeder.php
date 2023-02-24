<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		$this->call('InvokeTableSeeder');
	}

}

class InvokeTableSeeder extends Seeder {

    public function run()
    {

        for ($i=0;$i<100000;$i++)
        {
            $invoke = \App\Invocation::create([
            'event'=>"Testing",
            'appName' => "wl116",
           'userName' => "me@markusjavier.com",
            'sid1' => "CA7dcbf6a559817ced1bf0f2c701a754b5",
            'sid2' => "CA47c67305686a8e85d00ed33878094793",
            'timestamp' => \Carbon\Carbon::Now()->timestamp]);

            for ($x=0;$x<100;$x++)
            {
                \App\Log::create(['channel' => 'event',
                    'level'=>'200',
                    'message'=>'POST = Array
                                (
                                    [ApiVersion] => 2010-04-01
                                    [Called] => client:bgl256_sethzephyrgmailcom
                                    [ParentCallSid] => CA7dcbf6a559817ced1bf0f2c701a754b5
                                    [CallStatus] => initiated
                                    [From] => +18059247850
                                    [Direction] => outbound-dial
                                    [Timestamp] => Mon, 31 Jan 2022 21:58:35 +0000
                                    [AccountSid] => ACf4a81e49ef04d5c2f6e95a67d0ccb87f
                                    [CallbackSource] => call-progress-events
                                    [CalledVia] => +18059247850
                                    [Caller] => +18059247850
                                    [SequenceNumber] => 0
                                    [CallSid] => CA47c67305686a8e85d00ed33878094793
                                    [To] => client:bgl256_sethzephyrgmailcom
                                    [ForwardedFrom] => +18059247850
                                )',

                    'invoke_id'=>$invoke->id,
                    'time'=>\Carbon\Carbon::Now()->timestamp]);
            }
            echo $invoke->id."\n";

            }
        }
}
