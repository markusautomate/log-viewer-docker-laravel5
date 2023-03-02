users = [];
logs = [];
filtered_logs = [];
level_filter = [];
userFilter = '';
searchFilter = '';
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


function updateRedisUserfilter(id)
{
    if(document.getElementById('dateCal').value=='')
    {
        alert("First, please choose the date. Thank you!");
        return;
    }

    var activeUserFilterbtn = document.getElementById("activeUserFilter");
    var button = document.getElementById(id);
    var elems = document.getElementById("users_filter").querySelectorAll("button");
    [].forEach.call(elems, function(el) {
        el.classList.remove("btn-primary");
        el.classList.remove("btn-secondary");
        el.classList.add("btn-primary");
    });

    button.classList.add("btn-secondary");
    button.classList.remove("btn-primary");

    if (button.innerText=== activeUserFilterbtn.innerText)
    {
        button.classList.remove("btn-secondary");
        button.classList.add("btn-primary");
        activeUserFilterbtn.innerText = '';
        activeUserFilterbtn.value = 0;
        activeUserFilterbtn.style.display = "none";
        userFilter = '';
    }
    else
    {
        activeUserFilterbtn.innerText = button.innerText;
        activeUserFilterbtn.value = button.innerText;
        activeUserFilterbtn.style.display = "block";
        userFilter = id;
    }

    filter_logs();
}


function addUsers(users)
{
    var users_filter = document.getElementById('users_filter');
    var userHtml = '';
    users.forEach((user)=>{
        userHtml+= "<button id='"+user+"'  onclick=\"updateRedisUserfilter(this.id)\" class=\"btn btn-primary\" style=\"width: 100%\">"+user+"</button>"
    })

    users_filter.innerHTML = userHtml;
}


function updateSearchFilter(){
    searchFilter = document.getElementById('searchText').value;
    filter_logs();
}

function redis_filter(){
    if(document.getElementById('activeAppIdFilter').style.display === "none")
    {
        alert("First, please choose an AppId filter. Thank you!");
        return;
    }

    if(document.getElementById('activeEventFilter').style.display === "none")
    {
        alert("First, please choose an Event filter. Thank you!");
        return;
    }

    resetFilters();

    showLoading();
    var date_log = document.getElementById('dateCal').value;
    var appid = document.getElementById("activeAppIdFilter").value;

    var event = document.getElementById("activeEventFilter").value;

    var level =[];

    var search = "unknown";

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
                log = JSON.parse(result[i])
                logs.push(log)

                if (!users.includes(log.context.user)) {
                    users.push(log.context.user)
                }

            }
            addUsers(users);
            console.debug('users:', users)

            filter_logs();
            // document.getElementById("log_area").innerHTML = result[4];
            // document.getElementById('username_filter').innerHTML = result[0];
            // document.getElementById('appname_filter').innerHTML = result[2];
            hideLoading();

        }
    });
}


function resetFilters() {
    //removing active level filter
    var elems = document.getElementById("level_filter").querySelectorAll("button");
    [].forEach.call(elems, function(el) {
        el.classList.remove("btn-primary");
        el.classList.remove("btn-secondary");
        el.classList.add("btn-primary");
    });
    level_filter = [];

    //remove users filter
    var users_filter = document.getElementById("users_filter");
    users_filter.innerHTML = '';
    users = [];
    userFilter = '';
    document.getElementById('activeUserFilter').value = '';
    document.getElementById('activeUserFilter').style.display = "none";


    // remove search string in the search input
    var search = document.getElementById('searchText');
    search.value = '';
    searchFilter = '';
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


function filter_logs()
{
    filtered_logs = logs;
    // filtering by user
    if (userFilter !=='') {
        filtered_logs =  filtered_logs.filter( x => x.context.user === userFilter);
    }
    // filtering by loglevel
    if (level_filter.length) {
        filtered_logs =  filtered_logs.filter( x => level_filter.includes(x.level));
    }
    // filter by search
    if (searchFilter!=='') {
        filtered_logs =  filtered_logs.filter( x => x.message.includes(searchFilter));
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

