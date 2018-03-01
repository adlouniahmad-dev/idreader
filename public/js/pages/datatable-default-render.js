function checkRowIndex(index) {
    return index % 2 === 0 ? 'even' : 'odd';
}

function renderTablesInfo(totalUsers, currentPage, maxPages, totalUsersReturned) {
    let $tablesInfo = $('#all_users_info');
    let from, to;
    from = currentPage === 1 ? (totalUsersReturned - 9) : ((currentPage - 1) * 10 + 1);
    to = (currentPage - 1) * 10 + totalUsersReturned;
    let info = 'Showing ' + from + ' to ' + to + ' of ' + totalUsers + ' entries';

    $tablesInfo.empty();
    $tablesInfo.html(info);
}

function loadingUsers() {
    $('tbody').html(
        '<tr class="odd">' +
        '<td valign="top" colspan="7" class="dataTables_empty">Loading...</td>' +
        '</tr>'
    )
}

function noRecords() {
    $('tbody').html(
        '<tr class="odd">' +
        '<td valign="top" colspan="7" class="dataTables_empty">No Records</td>' +
        '</tr>'
    )
}