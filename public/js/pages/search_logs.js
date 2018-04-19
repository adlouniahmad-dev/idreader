let TableDatatablesAjax = function () {

    let handleRecords = function () {

        let grid = new Datatable();
        grid.init({
            src: $("#datatable_ajax"),
            onSuccess: function (grid, response) {},
            onError: function (grid) {},
            onDataLoad: function(grid) {},
            loadingMessage: 'Loading...',
            dataTable: {
                "deferLoading": 0,
                "dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                "ordering": false,
                "bStateSave": true,
                "lengthMenu": [
                    [10, 20, 50, 100, 150, -1],
                    [10, 20, 50, 100, 150, "All"]
                ],
                "pageLength": 10,
                "ajax": {
                    "url": "/api/logs/search",
                    "type": "get"
                },
            }
        });
    };

    return {
        init: function () {
            handleRecords();
        }
    };
}();

jQuery(document).ready(function() {
    TableDatatablesAjax.init();

    let $building = $('select[name="building"]');
    let $office = $('select[name="office"]');
    let $gateEntrance = $('select[name="gate_entrance"]');
    let $gateExit = $('select[name="gate_exit"]');
    let $guardEntrance = $('select[name="entrance_guard"]');
    let $guardExit = $('select[name="exit_guard"]');

    $building.on('change', function () {
        let buildingValue = $(this).val();
        $.ajax({
            url: '/api/logs/search/building/' + buildingValue,
            type: 'get',
            success: function (response) {
                if (response) {
                    $office.html(response.offices);
                    $gateEntrance.html(response.gates);
                    $gateExit.html(response.gates);
                    $guardEntrance.html(response.guards);
                    $guardExit.html(response.guards);
                }
            }
        });
    });

    $gateEntrance.on('change', function () {
        let gateValue = $(this).val();
        $.ajax({
            url: '/api/logs/search/gate/' + gateValue,
            type: 'get',
            success: function (response) {
                if (response)
                    $guardEntrance.html(response.guards);
            }
        })
    });

    $gateExit.on('change', function () {
        let gateValue = $(this).val();
        $.ajax({
            url: '/api/logs/search/gate/' + gateValue,
            type: 'get',
            success: function (response) {
                if (response)
                    $guardExit.html(response.guards);
            }
        })
    })
});
