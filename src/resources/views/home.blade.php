@extends('app')

@section('content')
	<div id="loading" class="text-center">
		<img  src="../image/loading.gif" width="150" height="150">
		<p id="loadingMessage">Message here!</p>
	</div>

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
					<div class="row">
						<div class="col-xs-4 text-center"><div>Chart Timezone: <p>-5:00 (est)</p></div></div>
						<div class="col-xs-4 text-center"><div>Total Invocations: <p id="invokeCount"></p></div></div>
						<div class="col-xs-4 text-center"><div>Average Hourly Invocations: <p id="invokeRate"></p></div> </div>
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
					<button id="filter"  onclick="filter()"  class="btn btn-warning" style="display: none; width: 100%; margin-top: 5px; margin-bottom: 15px;">Search</button>
					<div style="max-height: 600px; overflow-y: auto; overflow-x: hidden">
						<button class="accordion">Event Filter</button>
						<div class="panel" >
							<input onchange="changeSearch()" class="form-control" type="text"  id="searchText" list="eventName" placeholder="Event Keyword">
							<datalist id="eventName">
								<option value="Inbound SMS">
								<option value="Outbound SMS">
								<option value="Call Inbound">
								<option value="Outbound Call">
								<option value="Client Logger">
								<option value="Store Note">
								<option value="Dynamic Dial">
								<option value="SMS Send Form Campaign">
								<option value="Hot Lead Alerts">
								<option value="AutoAnswer">
								<option value="Time of Day Forwarding">
								<option value="Start New Contact">
							</datalist>
							<button onclick="clearSearch()" id="cancelEvent"  class="btn btn-primary" style="width: 100%; display: none"></button>
						</div>
						<hr>
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
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-7 ">
			<div class="panel panel-default">
				<div id="log-heading" class="panel-heading">Logs</div>
				<div class="panel-body">
					<div class="text-center bg-info" style="padding: 5px;">
							<label for="sort">Global Search:</label>
							<label class="switch">
								<input onchange="searchButton()" id="globalToggle" type="checkbox">
								<span class="slider round"></span>
							</label>
							<input type="text" onchange="searchContentChange()" id="searchContent" placeholder="e.g Phone Number">
							<button onclick="filter()" id="globalSearch" class="btn btn-warning btn-sm" style="display: none;">Global Search</button>
					</div>
					<div class="row" style="padding-top: 5px;">
						<div class="col-md-6">
							<label for="sort">Sort By</label>
							<select onchange="filter()" name="sort" id="sort">
								<option value="0">Oldest First</option>
								<option value="1">Newest First</option>
							</select>
						</div>
						<div class="text-right col-md-6"><p>Total Logs: <span id="logCount">0</span></p></div>
					</div>
					<div class="log_area" id="log_area">

					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	var label = ['00:00','01:00','02:00','03:00','04:00','05:00','06:00','07:00','08:00',
		'09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00',
		'19:00','20:00','21:00','22:00','23:00'];
	var graphdata =0;

	const ctx = document.getElementById('myChart');
	console.debug("loads chart");
	const myChart = new Chart(ctx, {
		type: 'bar',
		data: {
			labels: label,
			datasets: [{
				label: 'Invocations',
				data: graphdata,
				backgroundColor: [
					'rgba(75, 192, 192, 0.2)'
				],
				borderColor: [
					'rgba(75, 192, 192, 1)'
				],
				borderWidth: 1
			}]
		},
		options: {
			maintainAspectRatio: false,
			scales: {
				y: {
					beginAtZero: true
				}
			},
			plugins: {
				legend: {
					display: false
				}
			}
		}

	});

	function clickHandler(click){
		const point = myChart.getElementsAtEventForMode(click, 'nearest', { intersect:true }, true);
		if (point.length){
			const firstPoint = point[0];
			const value = myChart.data.labels[firstPoint.index];
			var filter = value.split(":");
			filter= parseInt(filter)+1;
			var timeFilter = " " + value + "-" + filter + ":00";
			document.getElementById('log-heading').innerText = "Logs " + timeFilter;
			console.log(value);
		}
	}

	// function removeData(chart) {
	// 	chart.data.labels.pop();
	// 	chart.data.datasets.forEach((dataset) => {
	// 		dataset.data.pop();
	// 	});
	// 	chart.update();
	// }

	function addData(chart, data) {
		chart.data.datasets[0].data = data;
		console.log(data);
		chart.update();
	}

	ctx.onclick = clickHandler;
</script>
@endsection
