let $visitorList = $('.visitor-list');
let $name = $('#visitorName');
let $nationality = $('#nationality');
let $idInfo = $('#idInfo');
let $blacklist = $('#blacklisted');
let $timeEntered = $('#timeEntered');
let $estimatedTime = $('#estimatedTime');
let $doneVisitContainer = $('#visit_done');

function getVisitors() {
    let url = $visitorList.children().size() === 0 ? '/visits/today' : ('/visits/today/new/' + $('.visitor-list li:nth-last-child(2)').data('logid'));
    ajaxVisitorLogs(url);
}

function ajaxVisitorLogs(url) {
    $.ajax({
        url: url,
        dataType: 'json',
        type: 'get',
        success: function (data) {
            if (data.empty === undefined) {
                if (data.visitors.length !== 0)
                    renderVisitors(data.visitors);
            }
        }
    })
}

function renderVisitors(visitors) {
    let $row = '';
    $.each(visitors, function (index, visitor) {
        $row +=
            '<li data-logid="' + visitor.logId + '">' +
            '<a href="#"> ' + visitor.visitorName +
            '<div class="date">' + visitor.timeEntered + '</div>' +
            '</a>' +
            '</li>' +
            '<li class="divider"></li>'
    });

    $visitorList.append($($row));
    renderCountVisitors();
}

function renderCountVisitors() {
    if ($visitorList.children().size() === 0)
        $('#nb_visitors').html('0');
    else
        $('#nb_visitors').html(($('.visitor-list').children().size() / 2));
}

$($visitorList).on('click', 'li', function (e) {
    if (!$(this).hasClass('active')) {
        e.preventDefault();
        let logId = $(this).data('logid');
        getVisitorAndLogInfo(logId, this);
    }
});

function getVisitorAndLogInfo(logId, li) {
    $.ajax({
        url: '/visits/info/' + logId,
        dataType: 'json',
        type: 'get',
        success: function (data) {
            if (data.success === undefined) {
                renderLogAndVisitorInfo(data.visitor, data.log, logId);
                $('.loading-visitor-info').addClass('hidden');
            }
        },
        beforeSend: function () {
            toggleActiveButton(li);
            $('.loading-visitor-info').removeClass('hidden');
        }
    });
}

function renderLogAndVisitorInfo(visitor, log, logId) {

    $name.html(visitor.fullName);
    $nationality.html(visitor.nationality);
    $idInfo.html(visitor.idInfo);

    let blacklisted = visitor.blacklisted === 'no' ? '<span class="label label-sm label-success" id="blacklisted">No</span>'
        : '<span class="label label-sm label-danger" id="blacklisted">Yes</span>';

    $blacklist.html(blacklisted);
    $timeEntered.html(log.timeEnteredBuilding);
    $estimatedTime.html(log.EstimatedTime);
    $doneVisitContainer.html('<button data-logid="' + logId + '" class="btn green reply-btn" id="done_visit_btn">Visit Done</button>');
}

function initializeVisitorInfo() {
    $name.html('Visitor Name');
    $nationality.html('');
    $idInfo.html('');
    $blacklist.html('');
    $timeEntered.html('');
    $estimatedTime.html('');
    $doneVisitContainer.html('');
}

function removeVisitFromDOM() {
    let $visitorList = $('.visitor-list li.active');
    $visitorList.next().remove();
    $visitorList.remove();
}

function toggleActiveButton(li) {
    $('.visitor-list li.active').removeClass('active');
    $(li).addClass('active');
}

$doneVisitContainer.on('click', '#done_visit_btn', function () {
    $.ajax({
        url: '/visits/done/' + $(this).data('logid'),
        type: 'put',
        success: function (response) {
            if (response.success === true) {
                $('.loading-visitor-info').addClass('hidden');
                removeVisitFromDOM();
                initializeVisitorInfo();
                renderCountVisitors();
            }
        },
        beforeSend: function () {
            $('.loading-visitor-info').removeClass('hidden');
        }
    })
});

getVisitors();

setInterval(function () {
    getVisitors()
}, 5000);

////////////////////////////////////////////////////////////////////

let $totalVisitorsPerDay = $('#totalVisitorsPerDay');
function totalVisitorsPerDay() {
    $.ajax({
        url: '/visits/getTotalVisitsPerDay',
        dataType: 'json',
        type: 'get',
        success: function (response) {
            if ($totalVisitorsPerDay.attr('data-value') !== response.count) {
                $totalVisitorsPerDay.attr('data-value', response.count);
                $totalVisitorsPerDay.counterUp({
                    'time': 400,
                    'delay': 10
                });
            }
        }
    })
}


let $doneVisitorsPerDay = $('#doneVisitorsPerDay');
function doneVisitorsPerDay() {
    $.ajax({
        url: '/visits/doneVisitorsPerDay',
        dataType: 'json',
        type: 'get',
        success: function (response) {
            if ($doneVisitorsPerDay.attr('data-value') !== response.count) {
                $doneVisitorsPerDay.attr('data-value', response.count);
                $doneVisitorsPerDay.counterUp({
                    'time': 400,
                    'delay': 10
                });
            }
        }
    })
}


let $totalVisitors = $('#totalVisitors');
function totalVisitors() {
    $.ajax({
        url: '/visits/getCountOfTotalVisits',
        dataType: 'json',
        type: 'get',
        success: function (response) {
            if ($totalVisitors.attr('data-value') !== response.count) {
                $totalVisitors.attr('data-value', response.count);
                $totalVisitors.counterUp({
                    'time': 400,
                    'delay': 10
                });
            }
        }
    })
}

totalVisitorsPerDay();
doneVisitorsPerDay();
totalVisitors();

setInterval(function () {
    totalVisitorsPerDay();
    doneVisitorsPerDay();
    totalVisitors();
}, 5000);