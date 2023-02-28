
<h3>{{$key}}</h3>
@foreach($logs as $log)
      {{json_decode(json_encode($log))->message}}
      <br>

@endforeach