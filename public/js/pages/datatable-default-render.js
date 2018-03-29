function checkRowIndex(index) {
    return index % 2 === 0 ? 'even' : 'odd';
}

function renderTablesInfo(totalRecords) {
    let $tablesInfo = $('#records_info');
    let info = 'Total: ' + totalRecords + ' entries';
    $tablesInfo.empty();
    $tablesInfo.html(info);
}

function loadingRecords(colspan) {
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