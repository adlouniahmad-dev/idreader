$('#logout').on('click', function () {
    document.location.href = "https://www.google.com/accounts/Logout?continue=https://appengine.google.com/_ah/logout?continue=http://localhost:8000/logout";
});

$(document).ready(function () {
    var date = new Date();
    var day, month, year;

    if (date.getMonth() + 1 < 10)
        month = '0' + (date.getMonth() + 1);
    else
        month = date.getMonth() + 1;

    if (date.getDate() < 10)
        day = '0' + date.getDate();
    else
        day = date.getDate();

    year = date.getFullYear();

    var todayDate = year + '-' + month + '-' + day;
    var todayMonth = year + '-' + month;

    $('#gateDate').val(todayDate).attr('max', todayDate);
    $('#monthDate').val(todayMonth).attr('max', todayMonth);
});
