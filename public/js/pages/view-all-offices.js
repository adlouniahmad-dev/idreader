let isFilter = false;

function getOffices(page, string = '') {

    let url = '';
    if (string) {
        url = '/api/office/getAllOffices/' + page + '/' + string;
        isFilter = true;
    }
    else {
        url = '/api/office/getAllOffices/' + page;
        isFilter = false;
    }

    $.ajax({
        url: url,
        type: 'get',
        dataType: 'json',
        success: function (data) {
            renderRecords(data.offices);
            renderTablesInfo(data.totalOffices);
            renderPagination(data.maxPages, string);
        },
        beforeSend: function () {
            loadingRecords(colspan = 7);
        }
    })
}

function renderRecords(offices) {
    let $rows = '';
    let $tbody = $('tbody');

    if (offices.length === 0) {
        noRecords(colspan = 7);
    } else {
        $.each(offices, function (index, office) {
            $rows +=
                '<tr role="row" class="' + checkRowIndex(index) + '">' +
                '<td class="sorting_1">' + office.id + '</td>' +
                '<td>' + office.officeNb + '</td>' +
                '<td' + (office.member.name === '' ? ' class="bg-grey"' : '') + '>' + office.member.name + '</td>' +
                '<td>' + office.building + '</td>' +
                '<td>' + office.floorNb + '</td>' +
                '<td>' + office.dateCreated + '</td>' +
                '<td>' +
                '<a href="/manageOffices/office/' + office.id + '" class="btn btn-sm btn-outline green margin-bottom-5"><i class="fa fa-search"></i> View</a>' +
                '<a href="/manageOffices/office/' + office.id + '/edit" class="btn btn-sm btn-outline red margin-bottom-5"><i class="fa fa-edit"></i> Edit</a>' +
                '</td>' +
                '</tr>';
        });
        $tbody.empty();
        $tbody.html($rows);
    }
}

function renderPagination(maxPages, string) {
    let $pagination = $('#all_offices_paginate').find('.pagination');
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
            getOffices(page, string);
        }
    }));
}

$('#search').keyup(function () {
    let string = $(this).val();
    getOffices(1, string);
});

getOffices(1);