function getBlacklist(string = '') {

    let url = '/api/getBlacklist' + (string === '' ? '' : '/' + string);

    $.ajax({
        url: url,
        type: 'get',
        dataType: 'json',
        success: function (data) {
            renderRecords(data.blacklist);
            renderTablesInfo(data.totalVisitors);
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
                '</td>' +
                '</tr>';
        });
        $tbody.empty();
        $tbody.html($rows);
    }
}

$('#search').keyup(function () {
    let string = $(this).val();
    getBlacklist(string);
});

getBlacklist('');