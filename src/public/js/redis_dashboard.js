
logs = [];
filtered_logs = [];


function  redis_changeDate()
{
    var dateNow = document.getElementById('dateCal').value;
    console.debug(dateNow);
    var url = '/redis/log-date/'+dateNow;
    window.location = url;
}

function updateRedisAppIDfilter(id)
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


function updateRedisEventfilter(id)
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

function redis_filter(){
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
        url : '/redis/filter',
        data: data,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType : 'json',
        success : function (result) {
            logs = [];
            for (var i in result) {
                logs.push(JSON.parse(result[i]))
            }

            console.debug('result: ', data.filter(x=>x.level === 100))
            document.getElementById("log_area").innerHTML = result[4];
            // document.getElementById('username_filter').innerHTML = result[0];
            // document.getElementById('appname_filter').innerHTML = result[2];
            hideLoading();

        }
    });
}

function updateRedisLevelfilter(id)
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
        redis_filter();
    }

}

function filter_logs()
{

}