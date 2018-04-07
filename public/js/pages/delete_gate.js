var UIConfirmations = function () {

    var handleSample = function () {

        let $deleteButton = $('#delete_gate_btn');
        let $alertDialog = $('.alert');
        let $messageContainer = $('#error-text');

        $deleteButton.on('confirmed.bs.confirmation', function () {
            $.ajax({
                url: '/manageGate/gate/' + gateId + '/edit/delete',
                dataType: 'json',
                success: function (response) {
                    if (response !== null) {
                        if (response.success === 'yes') {
                            $alertDialog.removeClass('hidden');

                            if ($alertDialog.hasClass('alert-danger'))
                                $alertDialog.removeClass('alert-danger');

                            $alertDialog.addClass('alert-success');
                            $messageContainer.html('Gate deleted successfully.');
                            $deleteButton.html('Deleted');

                            setInterval(function () {
                                location.href = 'http://localhost:8000/manageGates/viewGates'
                            }, 2000);

                        } else if (response.success === 'no') {
                            $('.alert').removeClass('hidden');

                            if ($alertDialog.hasClass('alert-success'))
                                $alertDialog.removeClass('alert-success');

                            $alertDialog.addClass('alert-danger');
                            $messageContainer.html('There is an error deleting the gate.');
                            $deleteButton.html('Delete Gate');
                        }
                    }
                },
                beforeSend: function () {
                    $deleteButton.html('Deleting...');
                }
            })
        });

    };

    return {
        init: function () {
            handleSample();
        }
    };
}();

jQuery(document).ready(function() {
    UIConfirmations.init();
});
