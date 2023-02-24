@extends('app')

@section('content')
    <div id="loading"></div>
    <div class="container">
        <div class="row">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Event</th>
                    <th scope="col">AppName</th>
                    <th scope="col">User</th>
                </tr>
                </thead>
                <tbody>
                @foreach($invokes as $invoke)
                <tr>
                    <th scope="row">{{$invoke->id}}</th>
                    <td>{{$invoke->event}}</td>
                    <td>{{$invoke->appName}}</td>
                    <td>{{$invoke->userName}}</td>
                    <td>{{ date('m/d/Y g:i:s A',$invoke->timestamp)}}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
            {!! $invokes->render() !!}
        </div>
    </div>

@endsection
