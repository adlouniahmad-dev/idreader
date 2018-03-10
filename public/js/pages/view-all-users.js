let isFilter = false;

function getUsers(page, string = '') {

    let url = '';
    if (string) {
        url = '/api/getAllUsers/' + page + '/' + string;
        isFilter = true;
    }
    else {
        url = '/api/getAllUsers/' + page;
        isFilter = false;
    }

    $.ajax({
        url: url,
        type: 'get',
        dataType: 'json',
        success: function (data) {
            renderUsersRecords(data.users);
            renderTablesInfo(data.totalUsers);
            renderPagination(data.maxPages, string);
        },
        beforeSend: function () {
            loadingUsers(8);
        }
    })
}

function renderUsersRecords(users) {
    let $rows = '';
    let $tbody = $('tbody');

    if (users.length === 0) {
        noRecords(8);
    } else {
        $.each(users, function (index, user) {
            $rows +=
                '<tr role="row" class="' + checkRowIndex(index) + '">' +
                '<td class="sorting_1">' + user.id + '</td>' +
                '<td>' + user.givenName + '</td>' +
                '<td>' + user.familyName + '</td>' +
                '<td><a href="mailto:' + user.gmail + '">' + user.gmail + '</a></td>' +
                '<td>' + user.dob + '</td>' +
                '<td>' + user.role + '</td>' +
                '<td>' + user.dateCreated + '</td>' +
                '<td><a href="/member/' + user.id + '">View</a> | <a href="/member/' + user.id + '/edit">Edit</a></td>' +
                '</tr>';
        });
        $tbody.empty();
        $tbody.html($rows);
    }
}

function renderPagination(maxPages, string) {
    let $pagination = $('#all_users_paginate').find('.pagination');
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

$('#search_users').keyup(function () {
    let string = $(this).val();
    getUsers(1, string);
});

getUsers(1);