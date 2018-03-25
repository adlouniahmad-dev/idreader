var UIConfirmations = function () {

    var handleSample = function () {

        let $deleteButton = $('#delete_account_btn');

        $deleteButton.on('confirmed.bs.confirmation', function () {
            $.ajax({

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
