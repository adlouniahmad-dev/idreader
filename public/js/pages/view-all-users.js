let firstLoad = true;
function getUsers(page) {
    $.ajax({
        url: '/api/getAllUsers/' + page,
        type: 'get',
        dataType: 'json',
        success: function (data) {
            renderUsersRecords(data.users);
            if (firstLoad) {
                renderPagination(data.totalUsers, data.maxPages);
                firstLoad = false;
            }
            renderTablesInfo(data.totalUsers, data.currentPage, data.maxPages, data.totalUsersReturned);
        },
        beforeSend: function () {
            loadingUsers();
        }
    })
}

function renderUsersRecords(users) {
    let $rows = '';
    let $tbody = $('tbody');

    if (users.length === 0) {
        noRecords();
    } else {
        $.each(users, function (index, user) {
            $rows +=
                '<tr role="row" class="' + checkRowIndex(index) + '">' +
                '<td class="sorting_1">' + user.id + '</td>' +
                '<td>' + user.name + '</td>' +
                '<td><a href="mailto:' + user.gmail + '">' + user.gmail + '</a></td>' +
                '<td>' + user.dob + '</td>' +
                '<td>' + user.role + '</td>' +
                '<td>' + user.dateCreated + '</td>' +
                '<td><a href="/member/' + user.id + '">View</a> | <a href="/member/' + user.id + '/edit">Edit</a></td>';
        });
        $tbody.empty();
        $tbody.html($rows);
    }
}

function renderPagination(totalUsers, maxPages) {
    let $pagination = $('#all_users_paginate').find('.pagination');
    $pagination.empty();
    $pagination.twbsPagination({
        totalPages: maxPages,
        visiblePages: 5,
        prev: '<i class="fa fa-angle-left"></i>',
        next: '<i class="fa fa-angle-right"></i>',
        first: '<i class="fa fa-angle-double-left"></i>',
        last: '<i class="fa fa-angle-double-right"></i>',
        onPageClick: function (event, page) {
            getUsers(page);
        }
    })
}

getUsers(1);