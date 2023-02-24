<div>
    @foreach($logs as $log)
    <div class="mb-2">
        <p><button  class="btn btn-xs btn-primary"  onclick="invoke(this.id)" id="{{ $log->invoke_id }}">{{ $log->invoke_id }}</button><strong> {{ $log->event }} - {{ $log->appName }} - {{ $log->userName }}
            @if($log->sid1!=0)
                <button class="btn btn-xs btn-warning" onClick="sids(this.id)" id="{{ $log->sid1 }}">{{ $log->sid1 }}</button>
            @endif
                @if($log->sid2!=0)
                <button class="btn btn-xs btn-warning" onClick="sids(this.id)" id="{{ $log->sid2 }}">{{ $log->sid2 }}</button>
            @endif
            </strong>
        </p>
        <div class="flex">
            <div class="font-semibold w-1/4 px-2 pt-1 rounded log-{{ strtolower(\Monolog\Logger::getLevelName($log->level)) }}"><p class="text-sm"><i class="fa fa-exclamation-triangle"></i>{{\Monolog\Logger::getLevelName($log->level)}}</p></div>
            <div class="w-3/4 px-2">
                <p style="white-space: pre-wrap">{{$log->message}}</p>
            </div>
        </div>
        <p class='text-xs'> {{date('Y-m-d H:i:sP',$log->time)}} </p>
    </div>
        <hr>
    @endforeach
    {!! $logs->render() !!}
</div>

