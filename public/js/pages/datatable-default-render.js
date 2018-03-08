function checkRowIndex(index) {
    return index % 2 === 0 ? 'even' : 'odd';
}

function renderTablesInfo(totalUsers) {
    let $tablesInfo = $('#all_users_info');
    let info = 'Total: ' + totalUsers + ' entries';
    $tablesInfo.empty();
    $tablesInfo.html(info);
}

function loadingUsers(colspan) {
    $('tbody').html(
        '<tr class="odd">' +
        '<td valign="top" colspan="' + colspan + '" class="dataTables_empty">Loading...</td>' +
        '</tr>'
    )
}

function noRecords(colspan) {
    $('tbody').html(
        '<tr class="odd">' +
        '<td valign="top" colspan="' + colspan + '" class="dataTables_empty">No Records</td>' +
        '</tr>'
    )
}