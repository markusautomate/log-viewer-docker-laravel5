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


						</div>
						<hr>
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

						<h1 class="text-center">Welcome to TurboDial Log Viewer.</h1>
						<p class="text-center">Start by searching for an AppName on the filter panel.</p>
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
