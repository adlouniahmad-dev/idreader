let isFilter = false;

function getVisitors(page, string = '') {

    let url = '';
    if (string) {
        url = '/api/getAllVisitors/' + page + '/' + string;
        isFilter = true;
    }
    else {
        url = '/api/getAllVisitors/' + page;
        isFilter = false;
    }

    $.ajax({
        url: url,
        type: 'get',
        dataType: 'json',
        success: function (data) {
            renderRecords(data.visitors);
            renderTablesInfo(data.totalVisitors);
            renderPagination(data.maxPages, string);
        },
        beforeSend: function () {
            loadingRecords(colspan = 9);
        }
    })
}

function renderRecords(visitors) {
    let $rows = '';
    let $tbody = $('tbody');

    if (visitors.length === 0) {
        noRecords(colspan = 9);
    } else {
        $.each(visitors, function (index, visitor) {
            $rows +=
                '<tr role="row" class="' + checkRowIndex(index) + '">' +
                '<td class="sorting_1">' + visitor.id + '</td>' +
                '<td>' + visitor.firstName + '</td>' +
                '<td>' + visitor.lastName + '</td>' +
                '<td>' + visitor.nationality + '</td>' +
                '<td>' + visitor.documentType + '</td>' +
                '<td>' + visitor.ssn + '</td>' +
                '<td><span class="label label-sm label-' + visitor.span + '">' + visitor.blacklisted + '</span></td>' +
                '<td>' +
                '<a href="/visitor/' + visitor.id + '" class="btn btn-sm btn-outline green margin-bottom-5"><i class="fa fa-search"></i> View</a>' +
                '<a href="/visitor/' + visitor.id + '/settings" class="btn btn-sm btn-outline red margin-bottom-5"><i class="fa fa-edit"></i> Edit</a>' +
                '</td>' +
                '</tr>';
        });
        $tbody.empty();
        $tbody.html($rows);
    }
}

function renderPagination(maxPages, string) {
    let $pagination = $('#all_visitors_paginate').find('.pagination');
    let currentPage = $pagination.twbsPagination('getCurrentPage');

    $pagination.twbsPagination('destroy');
    $pagination.twbsPagination($.extend({}, {
        totalPages: maxPages,
        startPage: currentPage,
        visiblePages: 5,
        initiateStartPageClick: false,
        prev: '<i class="fa fa-angle-left"></i>',
        next: '<i class="fa fa-angle-right"></i>',
        first: '<i class="fa fa-angle-double-left"></i>',
        last: '<i class="fa fa-angle-double-right"></i>',
        onPageClick: function (evt, page) {
            getVisitors(page, string);
        }
    }));
}

$('#search').keyup(function () {
    let string = $(this).val();
    getVisitors(1, string);
});

getVisitors(1);