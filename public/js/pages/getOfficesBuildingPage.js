function getOffices(buildingId, page) {

    let url = '/api/offices/' + buildingId + '/' + page;

    $.ajax({
        url: url,
        type: 'get',
        dataType: 'json',
        success: function (data) {
            renderOfficesRecords(data.offices);
            renderPaginationOffices(data.maxPages);
        },
        beforeSend: function () {
            $('#offices').html('Loading...');
        }
    })
}

function renderOfficesRecords(offices) {
    let $rows = '';
    let $officeContainer = $('#offices');

    if (offices.length === 0) {
        $officeContainer.html('No Offices');
    } else {
        $.each(offices, function (index, office) {
            $rows +=
                '<li>' +
                '<a href="/manageOffices/office/' + office.id + '">' + office.officeNumber + '</a>' +
                '</li>';
        });
        $officeContainer.empty();
        $officeContainer.html($rows);
    }
}

function renderPaginationOffices(maxPages) {
    let $paginationOffices = $('#pagination-offices');
    let currentPage = $paginationOffices.twbsPagination('getCurrentPage');

    $paginationOffices.twbsPagination('destroy');
    $paginationOffices.twbsPagination($.extend({}, {
        totalPages: maxPages,
        startPage: currentPage,
        visiblePages: 1,
        initiateStartPageClick: false,
        prev: '<i class="fa fa-angle-left"></i>',
        next: '<i class="fa fa-angle-right"></i>',
        first: null,
        last: null,
        onPageClick: function (evt, page) {
            getOffices(building, page);
        }
    }));
}

getOffices(building, 1);