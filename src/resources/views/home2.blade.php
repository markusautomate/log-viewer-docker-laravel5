@extends('app')

@section('content')
	<img id="loading" src="../image/loading.gif" width="150" height="150">
	<div class="container">
		<div class="row">
			<div class="col-md-10 col-md-offset-1">
				<div class="panel panel-default">
					<div class="panel-heading">Log Viewer</div>
					<div class="panel-body">
						<div class="row">
							<input class="col-lg-3" onchange="changeDate()" id="dateCal" type="date">
							<p class="col-lg-3 filters" style="display: none;" value="0" id="activeAppIdFilter"></p>
							<p class="col-lg-3 filters" style="display: none;"  value="0" id="activeUserFilter"></p>
							<p class="col-lg-3 filters" style="display: none;"  value="0" id="activeLevelFilter"></p>
						</div>

						<div>
							<canvas id="myChart" width="100%" height="200xp" ></canvas>
						</div>

					</div>
				</div>
			</div>
			<div class="col-md-10 col-md-offset-1">
				<div id="level_filter"  class="row panel text-center">
					<button onclick="updateLevelfilter(this.id)" id="200" class="btn btn-primary" >Info</button>
					<button onclick="updateLevelfilter(this.id)" id="100" class="btn btn-primary">Debug</button>
					<button onclick="updateLevelfilter(this.id)" id="250" class="btn btn-primary">Notice</button>
					<button onclick="updateLevelfilter(this.id)" id="300" class="btn btn-primary">Warning</button>
					<button onclick="updateLevelfilter(this.id)" id="400" class="btn btn-primary">Error</button>
					<button onclick="updateLevelfilter(this.id)" id="500" class="btn btn-primary">Critical</button>
					<button onclick="updateLevelfilter(this.id)" id="550" class="btn btn-primary">Alert</button>
					<button onclick="updateLevelfilter(this.id)" id="600" class="btn btn-primary">Emergency</button>
					<button onclick="updateLevelfilter(this.id)" id="lvlReset" class="btn btn-primary"><i class="glyphicon glyphicon-refresh"></i></button>
				</div>
			</div>
			<div class="col-md-3 col-md-offset-1">
				<div class="panel panel-default">
					<div class="panel-heading">
						<button id="reset_appname" class="btn btn-primary" onclick="updateAppIDfilter(this.id)"><i class="glyphicon glyphicon-refresh"></i></button>
						Filter
					</div>
					<div class="panel-body" >
						<div style="max-height: 600px; overflow-y: auto; overflow-x: hidden">
							<button class="accordion">AppID</button>
							<div class="panel" >
								<input id="searchAppID" class="form-control" onchange="updateAppIDchoices()" type="text">
								<p>Recently searched apps:</p>
								<div id="appname_filter">
									@foreach($appnames as $appname)
										<button id="{{$appname->appName}}"  onclick="updateAppIDfilter(this.id)"  class="btn btn-primary" style="width: 100%">{{$appname->appName}}</button><br>
									@endforeach
								</div>

							</div>
							<hr>
							<button class="accordion">Users</button>
							<div id="username_filter" class="panel">
								@foreach($users as $user)
									<button id="{{$userID}}"  onclick="updateUserfilter(this.id)" class="btn btn-secondary" style="width: 100%">{{$userID}} </button>
								@endforeach
							</div>
							<hr>
							<form action="/filters" method="POST">\
								@csrf
								<input type="hidden" id="appname" value="">
								<input type="hidden" id="username" value="">
								<input type="hidden" id="search" value="">

							</form>
							<button id="filter"  onclick="filter()"  class="btn btn-primary" style="width: 100%">Search</button><br>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-7 ">
				<div class="panel panel-default">
					<div class="panel-heading">Logs</div>
					<div class="panel-body">
						<div class="text-right"><input onchange="filter()" type="text"  id="searchText"></div>
						<div class="log_area" id="log_area">
							@foreach($logs as $log)
								<div class="mb-2">
									<p><button  class="btn btn-xs btn-primary"  onclick="invoke(this.id)" id="{{$log->invoke_id}}">[{{sprintf('%05d', $log->invoke_id)}}]</button><strong> {{$log->event}} -{{$log->appName }} - {{$log->userName}}
											@if($log->sid1 != "0") <button  class="btn btn-xs btn-warning"  onclick="sids(this.id)" id="{{$log->sid1}}">{{$log->sid1}}</button> @endif
											@if($log->sid2 != "0") <button  class="btn btn-xs btn-success"  onclick="sids(this.id)" id="{{$log->sid2}}">{{$log->sid2}}</button>@endif</strong></p>
									<div class="flex">
										<div class="font-semibold w-1/4 px-2 pt-1 rounded log-{{strtolower(\Monolog\Logger::getLevelName($log->level))}}"><p class="text-sm"><i class="fa fa-exclamation-triangle"></i>{{\Monolog\Logger::getLevelName($log->level)}}</p></div>
										<div class="w-3/4 px-2">
											<p style="white-space: pre-wrap"> {{$log->message}}</p>
										</div>
									</div>
									<p class="text-xs"> {{date('Y/m/d h:i:s a', $log->time)}}   -  {{config('app.timezone')}}  </p>
									<hr>
								</div>
							@endforeach
							{!! $logs->render() !!}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		var label =JSON.parse('{!! json_encode($graphLabel) !!}');
		var graphdata = JSON.parse('{!! json_encode($graphData) !!}');
	</script>
@endsection