function getBlacklist(page = 1, string = '') {

    let url = '/api/getBlacklist/' + page + (string === '' ? '' : '/' + string);

    $.ajax({
        url: url,
        type: 'get',
        dataType: 'json',
        success: function (data) {
            renderRecords(data.blacklist);
            renderTablesInfo(data.totalVisitors);
            renderPagination(data.maxPages, string);
        },
        beforeSend: function () {
            loadingRecords(colspan = 7);
        }
    })
}

function renderRecords(visitors) {
    let $rows = '';
    let $tbody = $('tbody');

    if (visitors.length === 0) {
        noRecords(colspan = 7);
    } else {
        $.each(visitors, function (index, visitor) {
            $rows +=
                '<tr role="row" class="' + checkRowIndex(index) + '">' +
                '<td class="sorting_1">' + visitor.id + '</td>' +
                '<td>' + visitor.visitor.firstName + '</td>' +
                '<td>' + visitor.visitor.middleName + '</td>' +
                '<td>' + visitor.visitor.lastName + '</td>' +
                '<td>' + visitor.visitor.nationality + '</td>' +
                '<td>' + visitor.dateAddedToBlacklist + '</td>' +
                '<td>' +
                '<a href="/visitor/' + visitor.visitor.id + '" class="btn btn-sm btn-outline green margin-bottom-5"><i class="fa fa-search"></i> View</a>' +
                '<a href="/visitor/' + visitor.visitor.id + '/settings" class="btn btn-sm btn-outline red margin-bottom-5"><i class="fa fa-edit"></i> Edit</a>' +
                '<a href="/visitor/' + visitor.visitor.id + '/settings#blacklist" class="btn btn-sm red margin-bottom-5 removeBtn"><i class="fa fa-remove"></i>  Remove</a>' +
                '</td>' +
                '</tr>';
        });
        $tbody.empty();
        $tbody.html($rows);
    }
}

function renderPagination(maxPages, string) {
    let $pagination = $('#all_blacklist_paginate').find('.pagination');
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
            getUsers(page, string);
        }
    }));
}

$('#search').keyup(function () {
    let string = $(this).val();
    getBlacklist(1, string);
});

getBlacklist(1);