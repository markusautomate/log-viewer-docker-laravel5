function  changeDate()
{
    var dateNow = document.getElementById('dateCal').value;
    console.debug(dateNow);
    var url = '/log-date/'+dateNow;
     window.location = url;
}
function updateAppIDfilter(id)
{
    if(document.getElementById('dateCal').value=='')
    {
        alert("First, please choose the date. Thank you!");
        return;
    }

    document.getElementById('searchAppID').value='';
    console.debug("search for " + id);
    var activeEventFilter = document.getElementById("activeEventFilter");
    var activeAppIdFilter = document.getElementById("activeAppIdFilter");
    var button = document.getElementById(id);
    var elems = document.getElementById("appname_filter").querySelectorAll("button");
    [].forEach.call(elems, function(el) {
        el.classList.remove("btn-primary");
        el.classList.remove("btn-secondary");
        el.classList.add("btn-primary");
    });
    var elems1 = document.getElementById("username_filter").querySelectorAll("button");
    [].forEach.call(elems1, function(el1) {
        el1.classList.remove("btn-primary");
        el1.classList.remove("btn-secondary");
        el1.classList.add("btn-primary");
    });

    if (button!=null)
    {
        button.classList.add("btn-secondary");
        button.classList.remove("btn-primary");
    }

    if (id==='reset_appname')
    {
        button.classList.remove("btn-secondary");
        button.classList.add("btn-primary");
        activeAppIdFilter.innerText = '';
        activeAppIdFilter.style.display = "none";
        activeAppIdFilter.value = 0;
        document.getElementById('searchAppID').value = '';
        activeEventFilter.innerText = '';
        activeEventFilter.value = 0;
        activeEventFilter.style.display = "none";
    }
    else if((button!=null) && (button.innerText=== activeAppIdFilter.innerText))
    {
        button.classList.remove("btn-secondary");
        button.classList.add("btn-primary");
        activeAppIdFilter.innerText = '';
        activeAppIdFilter.style.display = "none";
        activeAppIdFilter.value = 0;
        document.getElementById('searchAppID').value = '';
        activeEventFilter.innerText = '';
        activeEventFilter.value = 0;
        activeEventFilter.style.display = "none";
    }
    else
    {
        activeAppIdFilter.innerText = id;
        activeAppIdFilter.value = id;
        activeEventFilter.innerText = '';
        activeEventFilter.value = 0;
        activeEventFilter.style.display = "none";
        activeAppIdFilter.style.display = "block";
    }

}

function updateUserfilter(id)
{
    if(document.getElementById('dateCal').value=='')
    {
        alert("First, please choose the date. Thank you!");
        return;
    }
    var activeEventFilter = document.getElementById("activeEventFilter");
    var button = document.getElementById(id);
    var elems = document.getElementById("username_filter").querySelectorAll("button");
    [].forEach.call(elems, function(el) {
        el.classList.remove("btn-primary");
        el.classList.remove("btn-secondary");
        el.classList.add("btn-primary");
    });

    button.classList.add("btn-secondary");
    button.classList.remove("btn-primary");

    if (button.innerText=== activeEventFilter.innerText)
    {
        button.classList.remove("btn-secondary");
        button.classList.add("btn-primary");
        activeEventFilter.innerText = '';
        activeEventFilter.value = 0;
        activeEventFilter.style.display = "none";
    }
    else
    {
        activeEventFilter.innerText = button.innerText;
        activeEventFilter.value = button.innerText;
        activeEventFilter.style.display = "block";
    }

}

function updateLevelfilter(id)
{
    if(document.getElementById('dateCal').value=='')
    {
        alert("First, please choose the date. Thank you!");
        return;
    }

    var activeLevelFilter = document.getElementById("activeLevelFilter");
    var button = document.getElementById(id);


    if (id==='lvlReset')
    {
        var elems = document.getElementById("level_filter").querySelectorAll("button");
        [].forEach.call(elems, function(el) {
            el.classList.remove("btn-primary");
            el.classList.remove("btn-secondary");
            el.classList.add("btn-primary");
        });
    }
    else if((button!=null) && button.classList.contains("btn-secondary"))
    {
        button.classList.remove("btn-secondary");
        button.classList.add("btn-primary");
    }
    else if((button!=null) && button.classList.contains("btn-primary"))
    {
        button.classList.add("btn-secondary");
        button.classList.remove("btn-primary");
    }else
    {
        activeLevelFilter.innerText = button.innerText;
        activeLevelFilter.value = button.id;
        activeLevelFilter.style.display = "block";
    }

    if (document.getElementById("sids_filter"))
    {
        console.debug("filters sids :" + document.getElementById("sids_filter").innerText)
        sids(document.getElementById("sids_filter").innerText);
    }
    else if(document.getElementById("invoke_id_filter"))
    {
        console.debug("filters invoke ID : "+ document.getElementById("invoke_id_filter").value)
        invoke(document.getElementById("invoke_id_filter").innerText);
    }
    else
    {
        console.debug("filters regular")
        filter();
    }

}




function filter(){
    showLoading();
    var date_log = document.getElementById('dateCal').value;
    var appid = document.getElementById("activeAppIdFilter").value;
    console.debug("appid is " + appid);
    var event = document.getElementById("activeEventFilter").value;
    console.debug("user is " + event);
    var level =[];
    console.debug("level is " + level);
    var search = document.getElementById('searchText').value.trim();
    if (search == "") search="unknown"

    if (appid==undefined) appid=0

    var elems = document.getElementById("level_filter").querySelectorAll("button");
    [].forEach.call(elems, function(el) {
        if( el.classList.contains("btn-primary"))
        {
            level.push(el.id)
        }
    });

    var data = {"date_log":date_log,
        "appid":appid,
        "event":event,
        "level":JSON.stringify(level),
        "search":search,
        "load":0}

    $.ajax({
        type : 'POST',
        url : '/filter',
        data: data,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType : 'json',
        success : function (result) {
            document.getElementById("log_area").innerHTML = result[0];
            // document.getElementById('username_filter').innerHTML = result[0];
            // document.getElementById('appname_filter').innerHTML = result[2];
            hideLoading();

        }
    });
}

function loadMore(id){
    var myobj = document.getElementById(id);
    myobj.remove();
    showLoading();
    var date_log = document.getElementById('dateCal').value;
    var appid = document.getElementById("activeAppIdFilter").value;
    var user = document.getElementById("activeUserFilter").value;
    var level =[];
        var search = document.getElementById('searchText').value.trim();
    if (search == "") search="unknown"

    if (appid==undefined) appid=0

    var elems = document.getElementById("level_filter").querySelectorAll("button");
    [].forEach.call(elems, function(el) {
        if( el.classList.contains("btn-primary"))
        {
            level.push(el.id)
        }
    });

    var load = id.split("|")[1]
    console.log(load)
    var data = {"date_log":date_log,
        "appid":appid,
        "user":user,
        "level":JSON.stringify(level),
        "search":search,
        "load":load}

    $.ajax({
        type : 'POST',
        url : '/filter',
        data: data,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType : 'json',
        success : function (result) {
            document.getElementById("log_area").innerHTML += result[1];
            hideLoading();
        }
    });
}

function sids(sids)
{
    showLoading();
    var level =[];
    var elems = document.getElementById("level_filter").querySelectorAll("button");
    [].forEach.call(elems, function(el) {
        if( el.classList.contains("btn-primary"))
        {
            level.push(el.id)
        }
    });

    var data = { "level":JSON.stringify(level), "sids":sids }

    $.ajax({
        type : 'POST',
        url : '/sids',
        dataType : 'json',
        data: data,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success : function (result) {
            document.getElementById("log_area").innerHTML = result[0];
            hideLoading();
                    }
    });

}

function invoke(id)
{
    showLoading();
    var level =[];
    var elems = document.getElementById("level_filter").querySelectorAll("button");
    [].forEach.call(elems, function(el) {
        if( el.classList.contains("btn-primary"))
        {
            level.push(el.id)
        }
    });

    var data = { "level":JSON.stringify(level), "invoke_id":id }

    $.ajax({
        type : 'POST',
        url : '/invoke',
        dataType : 'json',
        data: data,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success : function (result) {
            document.getElementById("log_area").innerHTML = result[0];
            hideLoading();
        }
    });

}

var acc = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < acc.length; i++) {
    acc[i].addEventListener("click", function() {
        this.classList.toggle("active");
        var panel = this.nextElementSibling;
        if (panel.style.display === "block") {
            panel.style.display = "none";
        } else {
            panel.style.display = "block";
        }
    });
}

window.onload = (event) => {
    console.log('page is fully loaded');
    var lastUrl =window.location.href.split('/');
    console.debug(lastUrl[lastUrl.length-1]);
    if (lastUrl[lastUrl.length-1]== 'dashboard')
    {
        var now = new Date().toISOString();
        document.getElementById('dateCal').value = now.split('T')[0];
    }
    else
    {
        document.getElementById('dateCal').value = lastUrl[lastUrl.length-1];
    }

    if (document.getElementById('dateCal').value=="")
    {
        document.getElementById('dateCal').valueAsDate = new Date();
    }

    loadChart();
};

function loadChart()
{
    console.debug("loads chart");
    var ctx = document.getElementById('myChart');

    new Chart(ctx, {
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
}

function updateAppIDchoices()
{
    var searchApp = document.getElementById('searchAppID').value;
    if (searchApp.trim()=='')
    {
        updateAppIDfilter('reset_appname');
    }
    else
    {
        updateAppIDfilter(searchApp);
    }

}

function showLoading() {

    document.querySelector('#loading').style.display = 'block';
}
function hideLoading() {

    document.querySelector('#loading').style.display = 'none';
}



// if (document.addEventListener) {
//     document.addEventListener('contextmenu', function(e) {
//         e.preventDefault();
//         if (window.getSelection()) console.debug( window.getSelection().toString());
//         alert("You've tried to open context menu"); //here you draw your own menu
//     }, false);
// } else {
//     document.attachEvent('oncontextmenu', function() {
//         alert("You've tried to open context menu");
//         window.event.returnValue = false;
//     });
// }
