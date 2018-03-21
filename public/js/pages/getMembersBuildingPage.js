function getMembers(buildingId, page) {

    let url = '/api/membersBuilding/' + buildingId + '/' + page;

    $.ajax({
        url: url,
        type: 'get',
        dataType: 'json',
        success: function (data) {
            renderMembersRecords(data.users);
            renderPaginationMembers(data.maxPages);
        },
        beforeSend: function () {
            $('#members').html('Loading...');
        }
    })
}

function renderMembersRecords(members) {
    let $rows = '';
    let $memberContainer = $('#members');

    if (members.length === 0) {
        $memberContainer.html('No Members.');
    } else {
        $.each(members, function (index, member) {
            $rows +=
                '<li class="mt-list-item">' +
                '<div class="list-icon-container"><i class="icon-user"></i></div>' +
                '<div class="list-item-content">' +
                '<h3 class="text-capitalize">' +
                '<a href="/member/' + member.id + '">' + member.name + '</a>' +
                '</h3>' +
                '</div>' +
                '</li>';
        });
        $memberContainer.empty();
        $memberContainer.html($rows);
    }
}

function renderPaginationMembers(maxPages) {
    let $paginationMembers = $('#pagination-members');
    let currentPage = $paginationMembers.twbsPagination('getCurrentPage');

    $paginationMembers.twbsPagination('destroy');
    $paginationMembers.twbsPagination($.extend({}, {
        totalPages: maxPages,
        startPage: currentPage,
        visiblePages: 1,
        initiateStartPageClick: false,
        prev: '<i class="fa fa-angle-left"></i>',
        next: '<i class="fa fa-angle-right"></i>',
        first: '<i class="fa fa-angle-double-left"></i>',
        last: '<i class="fa fa-angle-double-right"></i>',
        onPageClick: function (evt, page) {
            getMembers(building, page);
        }
    }));
}

getMembers(building, 1);