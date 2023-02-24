$( document ).ajaxError(function( event, jqxhr, settings, thrownError ) {
    alert("Session expired. You'll be take to the login page");
    location.href = "/auth/login";
});

function  changeDate()
{

        var dateNow = document.getElementById('dateCal').value;
        var date = new Date(dateNow);
        console.log(typeof date);
        if (date instanceof Date && !isNaN(date))
        {
            var url = '/log-date/'+dateNow;
            window.location = url;
        }

}
function updateAppIDfilter(id)
{
    if(document.getElementById('dateCal').value=='')
    {
        alert("First, please choose the date. Thank you!");
        return;
    }
    unsetGlobal()
    document.getElementById('searchAppID').value='';
    console.debug("search for " + id);
    var activeUserFilter = document.getElementById("activeUserFilter");
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
        clearSearch();
        button.classList.remove("btn-secondary");
        button.classList.add("btn-primary");
        activeAppIdFilter.innerText = '';
        activeAppIdFilter.style.display = "none";
        activeAppIdFilter.value = 0;
        document.getElementById('searchAppID').value = '';
        activeUserFilter.innerText = '';
        activeUserFilter.value = 0;
        activeUserFilter.style.display = "none";
        document.getElementById('username_filter').innerHTML = "";
        hideSearchButton()
    }
    else if((button!=null) && (button.innerText=== activeAppIdFilter.innerText))
    {
        button.classList.remove("btn-secondary");
        button.classList.add("btn-primary");
        activeAppIdFilter.innerText = '';
        activeAppIdFilter.style.display = "none";
        activeAppIdFilter.value = 0;
        document.getElementById('searchAppID').value = '';
        activeUserFilter.innerText = '';
        activeUserFilter.value = 0;
        activeUserFilter.style.display = "none";
        document.getElementById('username_filter').innerHTML = "";
        hideSearchButton()
    }
    else
    {
        activeAppIdFilter.innerText = id;
        activeAppIdFilter.value = id;
        activeUserFilter.innerText = '';
        activeUserFilter.value = 0;
        activeUserFilter.style.display = "none";
        activeAppIdFilter.style.display = "block";
        getUsers();
        searchButton()
    }

}

function graphData(appid, date_log)
{
    var data = {"date_log":date_log,
        "appName":appid
    }

    $.ajax({
        type : 'POST',
        url : '/graphdata',
        data: data,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType : 'json',
        success : function (result) {

            graphdata = result['graphData'];
            document.getElementById('invokeCount').innerText = result['invokeCount'];
            document.getElementById('invokeRate').innerText = result['invokeRate'];

            addData(myChart,graphdata);

        }
    });
}

function getUsers()
{
    document.getElementById('loadingMessage').innerText = "Getting App Information...";
    showLoading();
    console.log("getusers");
    document.getElementById('username_filter').innerHTML = "";
    var date_log = document.getElementById('dateCal').value;
    var appid = document.getElementById("activeAppIdFilter").value;

    var data = {"date_log":date_log,
        "appName":appid
    }



    $.ajax({
        type : 'POST',
        url : '/users',
        data: data,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType : 'json',
        success : function (result) {

            users = Object.values(result['users'])
            appnames = Object.values(result['appnames'])


            users.forEach((name, key) => {
                if (name!="" && name!="NoUserID" && name!="InfUserID: %") {
                    document.getElementById('username_filter').innerHTML += "<button id=\"" + name + "\"  onclick=\"updateUserfilter(this.id)\" class=\"btn btn-primary\" style=\"width: 100%\">" + name + "</button>"
                }
            });
            document.getElementById('appname_filter').innerHTML = "";
            appnames.forEach((name, key) => {
                    if (appnames.indexOf(name)==0)
                    {
                        document.getElementById('appname_filter').innerHTML += "<button id=\""+name+"\"  onclick=\"updateAppIDfilter(this.id)\"  class=\"btn btn-secondary\" style=\"width: 100%\">"+name+"</button><br>"
                    }
                    else
                    {
                        document.getElementById('appname_filter').innerHTML += "<button id=\""+name+"\"  onclick=\"updateAppIDfilter(this.id)\"  class=\"btn btn-primary\" style=\"width: 100%\">"+name+"</button><br>"
                    }
            });

            hideLoading();
        }
    });

    graphData(appid,date_log);
}

function updateUserfilter(id)
{
    if(document.getElementById('dateCal').value=='')
    {
        alert("First, please choose the date. Thank you!");
        return;
    }
    unsetGlobal();
    var activeUserFilter = document.getElementById("activeUserFilter");
    var button = document.getElementById(id);
    var elems = document.getElementById("username_filter").querySelectorAll("button");
    [].forEach.call(elems, function(el) {
        el.classList.remove("btn-primary");
        el.classList.remove("btn-secondary");
        el.classList.add("btn-primary");
    });

    button.classList.add("btn-secondary");
    button.classList.remove("btn-primary");

    if (button.innerText=== activeUserFilter.innerText)
    {
        button.classList.remove("btn-secondary");
        button.classList.add("btn-primary");
        activeUserFilter.innerText = '';
        activeUserFilter.value = 0;
        activeUserFilter.style.display = "none";
    }
    else
    {
        activeUserFilter.innerText = button.innerText;
        activeUserFilter.value = button.innerText;
        activeUserFilter.style.display = "block";
    }
    searchButton()
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
    // var elems = document.getElementById("level_filter").querySelectorAll("button");
    // [].forEach.call(elems, function(el) {
    //     el.classList.remove("btn-primary");
    //     el.classList.remove("btn-secondary");
    //     el.classList.add("btn-primary");
    // });
    // button.classList.add("btn-secondary");
    // button.classList.remove("btn-primary");

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
        // activeLevelFilter.innerText = '';
        // activeLevelFilter.value = 0;
        // activeLevelFilter.style.display = "none";
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
    }
    searchButton()
}

function searchButton()
{
    let global = document.getElementById('globalToggle').checked;

    if (global)
    {
        document.getElementById('globalSearch').style.display='inline';
        document.getElementById('filter').style.display='none';
    }
    else
    {
        document.getElementById('filter').style.display='block';
        document.getElementById('globalSearch').style.display='none';
    }

}

function hideSearchButton()
{
    document.getElementById('filter').style.display='none';
    document.getElementById('globalSearch').style.display='none';
}


function filter(load = 0){

    let global = document.getElementById('globalToggle').checked;
    let date_log = document.getElementById('dateCal').value;
    let appid = document.getElementById("activeAppIdFilter").value;
    let contentSearch = document.getElementById('searchContent').value.trim();
    if (contentSearch == "") contentSearch="unknown";
    if (!global)
    {
        global = 0;
        if (appid==undefined || appid==0 || appid=="")
        {
            alert("First, please select an AppId first. Thank you!");
            hideLoading();
            return;
        }
    }
    else
    {
        if (contentSearch == "unknown"){
            alert('Please enter a search text. Search for "all" if you want to see all the logs for today.');
            document.getElementById('searchContent').value= "all";
            document.getElementById('searchContent').focus();
            return;
        }
        global = 1;
    }

    document.getElementById('loadingMessage').innerText = "Retrieving Logs...";
    showLoading();

    let user = document.getElementById("activeUserFilter").value;
    let level =[];
    let search = document.getElementById('searchText').value.trim();
    if (search == "") search="unknown";


    let sort = document.getElementById("sort").value;

    let elems = document.getElementById("level_filter").querySelectorAll("button");
    [].forEach.call(elems, function(el) {
        if( el.classList.contains("btn-secondary"))
        {
            level.push(el.id)
        }
    });

    var data = {"date_log":date_log,
        "appid":appid,
        "user":user,
        "level":JSON.stringify(level),
        "search":search,
        "global":global,
        "contentSearch":contentSearch,
        "load":load,
        "sort":sort}

    localStorage['loadPage'] = load;

    $.ajax({
        type : 'POST',
        url : '/filter',
        data: data,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType : 'json',
        success : function (result) {

            document.getElementById("log_area").innerHTML = "";
            // document.getElementById('username_filter').innerHTML = result[0];
            // document.getElementById('appname_filter').innerHTML = result[2];
            console.log(result['logs'].total);

            if( result['logs'].total==0)
            {
                document.getElementById('logCount').innerText= '0';
                document.getElementById("log_area").innerHTML = "<h1 class='text-center'>No data available.</h1>\n" +
                    "                    <p class='text-center'>Please try another filter.</p>";
            }
            else
            {
                document.getElementById('logCount').innerText= result['logs'].total;
                result['logs'].data.forEach(renderLogs);
                document.getElementById("log_area").innerHTML += result['pagination'];
            }

             hideLoading();

        }
    });
    document.getElementById('globalSearch').style.display='none';
    hideSearchButton()
}

function getLoglevel(level)
{
    switch (level){
        case 100:
            return "DEBUG";
            break;
        case 200:
            return "INFO";
            break;
        case 250:
            return "NOTICE";
            break;
        case 300:
            return "WARNING";
            break;
        case 400:
            return "ERROR";
            break;
        case 500:
            return "CRITICAL";
            break;
        case 550:
            return "ALERT";
            break;
        case 600:
            return "EMERGENCY";
            break;
    }
}

function unsetGlobal()
{
    document.getElementById('globalToggle').checked = false;
    document.getElementById('searchContent').value = "";
}

function renderLogs(arr)
{
        var date = new Date(arr['time']* 1000)

        text =  "<div class=\"mb-2\">\n" +
        "<p><button  class=\"btn btn-xs btn-primary\"  onclick=\"invoke(this.id)\" id=\""+arr['invoke_id']+"\">["+arr['invoke_id']+"]</button><strong> "+arr['event']+" -"+arr['appName']+" - "+arr['userName']+"\n"
        if (arr['sid1']!=0)
        {
            text += "- <button class=\"btn btn-xs btn-warning\" onClick=\"sids(this.id)\"\n" +
            "                    id=\""+arr['sid1']+"\">"+arr['sid1']+"</button>"
        }
        if (arr['sid2']!=0)
        {
            text += "- <button class=\"btn btn-xs btn-success\" onClick=\"sids(this.id)\"\n" +
                "                    id=\""+arr['sid2']+"\">"+arr['sid2']+"</button>"
        }
        text += "<div class=\"flex\">\n" +
        "<div class=\"font-semibold w-1/4 px-2 pt-1 rounded log-"+ getLoglevel(arr['level']).toLowerCase() +"\"><p class=\"text-sm\"><i class=\"fa fa-exclamation-triangle\"></i>"+getLoglevel(arr['level']) +"</p></div>\n" +
        "<div class=\"w-3/4 px-2\">\n" +
        "<p style=\"white-space: pre-wrap\"> "+arr['message']+"</p>\n" +
        "</div>\n" +
        "</div>"+
            "<p class='text-xs'>"+date.toLocaleString('en-US', { timeZone: 'America/New_York' })+" − 5:00 </p>\n" +
            "<hr>"

    document.getElementById("log_area").innerHTML += text;
    return console.log(arr['appName']);
}


function sids(sids)
{
    showLoading();
    var date_log = document.getElementById('dateCal').value;
    var level =[];
    var elems = document.getElementById("level_filter").querySelectorAll("button");
    [].forEach.call(elems, function(el) {
        if( el.classList.contains("btn-secondary"))
        {
            level.push(el.id)
        }
    });

    var data = { "level":JSON.stringify(level),
                "sids":sids,
                "date_log":date_log}

    var loadPage = localStorage['loadPage']

    $.ajax({
        type : 'POST',
        url : '/sids',
        dataType : 'json',
        data: data,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success : function (result) {
            document.getElementById("log_area").innerHTML = "<h4><button onclick='filter("+loadPage+")'  class='btn btn-primary'><i class='glyphicon glyphicon-arrow-left'></i></button>Showing logs for SIDS:<span id='sids_filter' value='" +sids+ "'>"+sids+"</span>></h4>";

            if(result['logs'].total==0)
            {
                document.getElementById("log_area").innerHTML = "<h1 class='text-center'>No data available.</h1>\n" +
                    "                    <p class='text-center'>Please try another filter.</p>";
            }
            else
            {
                result['logs'].data.forEach(renderLogs);
            }
            hideLoading();
        }
    });

}

function invoke(id)
{
    showLoading();
    var date_log = document.getElementById('dateCal').value;
    var level =[];
    var elems = document.getElementById("level_filter").querySelectorAll("button");
    [].forEach.call(elems, function(el) {
        if( el.classList.contains("btn-secondary"))
        {
            level.push(el.id)
        }
    });

    var data = { "level":JSON.stringify(level), "invoke_id":id,"date_log":date_log }

    var loadPage = localStorage['loadPage']

    $.ajax({
        type : 'POST',
        url : '/invoke',
        dataType : 'json',
        data: data,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success : function (result) {
            document.getElementById("log_area").innerHTML = "<h4><button onclick='filter("+loadPage+")'  class='btn btn-primary'><i class='glyphicon glyphicon-arrow-left'></i></button>Showing logs with Invoke ID:<span id='invoke_id_filter' value='"+id+"'>"+id+"</span></h4>";

            if(result['logs'].length==0)
            {
                document.getElementById("log_area").innerHTML = "<h1 class='text-center'>No data available.</h1>\n" +
                    "                    <p class='text-center'>Please try another filter.</p>";
            }
            else
            {
                result['logs'].forEach(renderLogs);
            }
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

    graphData("all",document.getElementById('dateCal').value)
     // loadChart();
};






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

$(document).ready(function (){
    $(document).on('click','.pagination a',function(event){
        event.preventDefault();
        event.stopImmediatePropagation();
        var page = $(this).attr('href').split('page=')[1];
        filter(page);
    })
})

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

function clearSearch()
{
    console.log("clearing search input.")
    var text = document.getElementById('searchText');
    document.getElementById("cancelEvent").style.display = "none";
    text.value="";

}

function changeSearch()
{
    var eventFilter = document.getElementById("cancelEvent");
    var search = document.getElementById("searchText");

    if (search.value!="")
    {
        eventFilter.innerText = search.value;
        eventFilter.style.display = "block";
        searchButton()
    }
    else
    {
        eventFilter.innerText= "";
        eventFilter.style.display = "none";
    }
}



function searchContentChange()
{
    var header = document.getElementById("log-heading");
    var search = document.getElementById("searchContent");

    var global = document.getElementById('globalToggle').checked;


    if (search.value!="")
    {
        if (global)
        {
            document.getElementById('globalSearch').style.display='inline';
            document.getElementById('filter').style.display='none';
        }
        else
        {
            document.getElementById('globalSearch').style.display='none';
            document.getElementById('filter').style.display='block';
        }
        header.innerText = "Logs Content Search: " + search.value;

    }
    else
    {
        header.innerText = "Logs";
    }
}