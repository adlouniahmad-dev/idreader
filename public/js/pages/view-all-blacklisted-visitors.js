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
                '<a href="/visitor/' + visitor.visitor.id + '/settings#blacklist" class="btn btn-sm red margin-bottom-5 removeBtn"><i class="fa fa-remove"></i>  Remove</a>' +
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

var UIConfirmations = function () {

    var handleSample = function () {

        $('table').on('confirmed.bs.confirmation', '.removeBtn', function () {
            // add_remove_blacklist(this, 'remove', visitorId);
            alert();
        });
    };

    return {
        init: function () {
            handleSample();
        }
    };
}();

function add_remove_blacklist(button, option, visitorId) {

    let url = '/api/blacklist/' + option + '/' + visitorId;
    let $alertDialog = $('.alert');
    let $messageContainer = $('#error-text');

    $.ajax({
        url: url,
        dataType: 'json',
        success: function (response) {
            if (response !== null) {
                if (response.success === 'yes') {
                    $alertDialog.removeClass('hidden');

                    if ($alertDialog.hasClass('alert-danger'))
                        $alertDialog.removeClass('alert-danger');

                    $alertDialog.addClass('alert-success');

                    let message = option === 'add' ? 'Added to blacklist.' : 'Removed from blacklist';
                    $messageContainer.html(message);

                    let textButton = option === 'add' ? 'Added' : 'Removed';
                    $(button).html(textButton);

                    location.href = 'http://localhost:8000/visitor/' + visitorId + '/settings#blacklist';
                    setInterval(function () {
                        location.reload();
                    }, 1000);

                } else if (response.success === 'no') {

                    $alertDialog.removeClass('hidden');

                    if ($alertDialog.hasClass('alert-success'))
                        $alertDialog.removeClass('alert-success');

                    $alertDialog.addClass('alert-danger');
                    $messageContainer.html('There is an error deleting the account.');

                    let textButton = option === 'add' ? 'Add' : 'Remove';
                    $(button).html(textButton);
                }
            }
        },
        beforeSend: function () {
            let textButton = option === 'add' ? 'Add' : 'Remove';
            $(button).html(textButton);
        }
    })

}

jQuery(document).ready(function () {
    UIConfirmations.init();
});
