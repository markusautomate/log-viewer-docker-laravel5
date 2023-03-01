
logs = [];
filtered_logs = [];
level_filter = [];
var currentPage = 1;
var rowsPerPage = 30;


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

            filter_logs();
            // document.getElementById("log_area").innerHTML = result[4];
            // document.getElementById('username_filter').innerHTML = result[0];
            // document.getElementById('appname_filter').innerHTML = result[2];
            hideLoading();

        }
    });
}


function updateRedisLevelfilter(id)
{
    if (id==='lvlReset')
    {
        var elems = document.getElementById("level_filter").querySelectorAll("button");
        [].forEach.call(elems, function(el) {
            el.classList.remove("btn-primary");
            el.classList.remove("btn-secondary");
            el.classList.add("btn-primary");
        });
        level_filter = [];

        filter_logs()

        return;
    }

    var button = document.getElementById(id);

    if (!level_filter.find(x=>x == id))
    {
        if((button!=null) && button.classList.contains("btn-primary"))
        {
            button.classList.add("btn-secondary");
            button.classList.remove("btn-primary");
        }

        level_filter.push(Number(id));
    } else {
        if((button!=null) && button.classList.contains("btn-secondary"))
        {
            button.classList.remove("btn-secondary");
            button.classList.add("btn-primary");
        }
        level_filter = level_filter.filter(x=>x!=Number(id));
    }

    filter_logs();
}

function updateRedisLevelfilter_old(id)
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
    if (level_filter.length) {
        filtered_logs =  logs.filter( x => level_filter.includes(x.level));
    } else {
        filtered_logs = logs;
    }
    setCurrentPage(1)
}

function displayTableData() {
    var log_area = document.getElementById("log_area");
    var dataHtml = "<h5>Log Count: "+ filtered_logs.length +"</h5>";
    var startIndex = (currentPage - 1) * rowsPerPage;
    var endIndex = startIndex + rowsPerPage;

    for (var i = startIndex; i < endIndex && i < filtered_logs.length; i++) {
        dataHtml +=
            "<div class='mb-2'>"+
            "<div class='flex'>"+
            "<div class='font-semibold w-1/4 px-2 pt-1'><p class='text-sm rounded log-"+(filtered_logs[i].level_name).toLowerCase()+"'><i class='fa fa-exclamation-triangle'></i>"+filtered_logs[i].level_name+"</p>"+
            "<p>User: "+filtered_logs[i].context['user'] +"</p></div>"+
            "<div class='w-3/4 px-2'>"+
            "<p style='white-space: pre-wrap'>"+filtered_logs[i].message+"</p>"+
        "</div>"+
    "</div>"+
    "<p class='text-xs'>"+filtered_logs[i].datetime+"</p>"+
        "<hr></div>";
    }

    log_area.innerHTML = dataHtml;
}

function displayPagination() {
    var pagination = document.getElementById("pagination");
    var totalPages = Math.ceil(filtered_logs.length / rowsPerPage);
    var paginationHtml = "";

    if (!filtered_logs.length) {
        pagination.innerHTML = paginationHtml;
        return;
    }

    if (currentPage === totalPages)
    {
        paginationHtml += "<li class=\"page-item\">\n" +
            "      <a class=\"page-link\" href=\"#\" aria-label=\"Previous\" onclick='setCurrentPage(" + 1 + ")'>\n" +
            "        <span aria-hidden=\"true\">&laquo;</span>\n" +
            "        <span class=\"sr-only\">Previous</span>\n" +
            "      </a>\n" +
            "    </li>\n" +
            "    <li class=\"page-item\"><a class=\"page-link\" href=\"#\" onclick='setCurrentPage(" + (currentPage - 2) + ")'>" + (currentPage - 2) + "</a></li>\n" +
            "    <li class=\"page-item\"><a class=\"page-link\" href=\"#\" onclick='setCurrentPage(" + (currentPage - 1) + ")'>" + (currentPage - 1) + "</a></li>\n" +
            "    <li class=\"page-item\"><a class=\"page-link\" href=\"#\">" + currentPage + "</a></li>\n" +
            "    <li class=\"page-item\">\n" +
            "      <a class=\"page-link disabled\" href=\"#\" aria-label=\"Next\">\n" +
            "        <span aria-hidden=\"true\">&raquo;</span>\n" +
            "        <span class=\"sr-only\">Next</span>\n" +
            "      </a>\n" +
            "    </li>"
    }
    else if (currentPage===1)
    {
        paginationHtml += "<li class=\"page-item\">\n" +
            "      <a class=\"page-link disabled\" href=\"#\" aria-label=\"Previous\">\n" +
            "        <span aria-hidden=\"true\">&laquo;</span>\n" +
            "        <span class=\"sr-only\">Previous</span>\n" +
            "      </a>\n" +
            "    </li>\n" +
            "    <li class=\"page-item\"><a class=\"page-link\" href=\"#\">" + currentPage + "</a></li>\n" +
            "    <li class=\"page-item\"><a class=\"page-link\" href=\"#\" onclick='setCurrentPage(" + (currentPage + 1) + ")'>" + (currentPage + 1) + "</a></li>\n" +
            "    <li class=\"page-item\"><a class=\"page-link\" href=\"#\" onclick='setCurrentPage(" + (currentPage + 2) + ")'>" + (currentPage + 2) + "</a></li>\n" +
            "    <li class=\"page-item\">\n" +
            "      <a class=\"page-link\" href=\"#\" aria-label=\"Next\" onclick='setCurrentPage(" + (totalPages )+ ")'>\n" +
            "        <span aria-hidden=\"true\">&raquo;</span>\n" +
            "        <span class=\"sr-only\">Next</span>\n" +
            "      </a>\n" +
            "    </li>"
    }
    else
    {
        paginationHtml += "<li class=\"page-item\">\n" +
            "      <a class=\"page-link\" href=\"#\" aria-label=\"Previous\" onclick='setCurrentPage(" + 1 + ")'>\n" +
            "        <span aria-hidden=\"true\">&laquo;</span>\n" +
            "        <span class=\"sr-only\">Previous</span>\n" +
            "      </a>\n" +
            "    </li>\n" +
            "    <li class=\"page-item\"><a class=\"page-link\" href=\"#\" onclick='setCurrentPage(" + (currentPage - 1) + ")'>" + (currentPage - 1) + "</a></li>\n" +
            "    <li class=\"page-item\"><a class=\"page-link\" href=\"#\">" + currentPage + "</a></li>\n" +
            "    <li class=\"page-item\"><a class=\"page-link\" href=\"#\" onclick='setCurrentPage(" + (currentPage + 1) + ")'>" + (currentPage + 1) + "</a></li>\n" +
            "    <li class=\"page-item\">\n" +
            "      <a class=\"page-link\" href=\"#\" aria-label=\"Next\"  onclick='setCurrentPage(" + (totalPages )+ ")'>\n" +
            "        <span aria-hidden=\"true\">&raquo;</span>\n" +
            "        <span class=\"sr-only\">Next</span>\n" +
            "      </a>\n" +
            "    </li>"
    }

    pagination.innerHTML = paginationHtml;
}

function setCurrentPage(page) {
    currentPage = page;
    displayTableData();
    displayPagination();
}

