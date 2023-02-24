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
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">Event</th>
                    <th scope="col">Count</th>
                </tr>
                </thead>
                <tbody>
                @foreach($invokes as $invoke)
                    <tr>
                        <td>{{$invoke->event}}</td>
                        <td>{{$invoke->total}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
{{--            {!! $invokes->render() !!}--}}
        </div>
    </div>
</div>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>
</body>
</html>

