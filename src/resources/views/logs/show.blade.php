<html>
<head>
    <title>TurboDial Log Viewer</title>

    <link href='//fonts.googleapis.com/css?family=Lato:100' rel='stylesheet' type='text/css'>
    <link href="{{ asset('/css/app.css?ver=2') }}" rel="stylesheet">
    <link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>
</head>
<body>
<div id="app" class="container">
    <div class="content">
        <div class="row">
            <h1>{{$time}}</h1>
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Event</th>
                    <th scope="col">AppName</th>
                    <th scope="col">User</th>
                    <th scope="col">Level</th>
                    <th scope="col">Message</th>
                </tr>
                </thead>
                <tbody>
                @foreach($logs as $log)
                    <tr>
                        <th scope="row">{{$log->id}}</th>
                        <td>{{$log->invoker->event}}</td>
                        <td>{{$log->invoker->appName}}</td>
                        <td>{{$log->invoker->userName}}</td>
                        <td>{{\Monolog\Logger::getLevelName($log->level)}}</td>
                        <td>{{$log->message}}</td>
                        <td>{{ date('m/d/Y g:i:s A',$log->time)}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {!! $logs->render() !!}
        </div>
    </div>
</div>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>
</body>
</html>

