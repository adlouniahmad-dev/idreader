var UIConfirmations = function () {

    var handleSample = function () {

        let $deleteButton = $('#delete_account_btn');
        let $alertDialog = $('#remove-alert');
        let $messageContainer = $('#error-text-remove');

        $deleteButton.on('confirmed.bs.confirmation', function () {
            $.ajax({
                url: '/visitor/' + visitorId + '/settings/delete',
                dataType: 'json',
                success: function (response) {
                    if (response !== null) {
                        if (response.success === 'yes') {
                            $alertDialog.removeClass('hidden');

                            if ($alertDialog.hasClass('alert-danger'))
                                $alertDialog.removeClass('alert-danger');

                            $alertDialog.addClass('alert-success');
                            $messageContainer.html('Account deleted successfully.');
                            $deleteButton.html('Deleted');

                            setInterval(function () {
                                location.href = 'http://localhost:8000/visitors'
                            }, 2000);

                        } else if (response.success === 'no') {
                            $('.alert').removeClass('hidden');

                            if ($alertDialog.hasClass('alert-success'))
                                $alertDialog.removeClass('alert-success');

                            $alertDialog.addClass('alert-danger');
                            $messageContainer.html('There is an error deleting the account.');
                            $deleteButton.html('Delete Account');
                        }
                    }
                },
                beforeSend: function () {
                    $deleteButton.html('Deleting...');
                }
            })
        });

        $deleteButton.unbined('confirmed.bs.confirmation');
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
