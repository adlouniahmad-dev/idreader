var TableDatatablesEditable = function () {

    var handleTable = function () {

        var table = $('#sample_editable_1');

        var oTable = table.dataTable({

            "lengthMenu": [
                [5, 15, 20, -1],
                [5, 15, 20, "All"]
            ],

            "pageLength": 5,

            "language": {
                "lengthMenu": " _MENU_ records"
            },
            "columnDefs": [{
                'orderable': true,
                'targets': [0]
            }, {
                "searchable": true,
                "targets": [0]
            }],

            ajax: '/shifts/get',
            columns: [
                {data: 'day'},
                {data: 'startTime'},
                {data: 'endTime'},
                {data: 'delete'},
            ],

            "order": [
                [0, "asc"]
            ]
        });

        table.on('click', '.delete', function (e) {
            e.preventDefault();

            if (confirm("Are you sure to delete this row ?\nAny data related to that shift will be lost.") === false) {
                return;
            }

            var nRow = $(this).parents('tr')[0];
            $.ajax({
                url: '/shifts/' + $(this).data('shift') + '/delete',
                type: 'get',
                success: function (response) {
                    if (response.success === true) {
                        oTable.fnDeleteRow(nRow);
                        alert('Deleted successfully.');
                    } else {
                        alert('Error');
                    }
                }
            });

        });

    };

    return {
        init: function () {
            handleTable();
        }
    };
}();

jQuery(document).ready(function () {
    TableDatatablesEditable.init();
});