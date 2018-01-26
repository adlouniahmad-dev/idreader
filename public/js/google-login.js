function onSignIn(googleUser) {

    var id_token = googleUser.getAuthResponse().id_token;
    console.log(id_token);
    $.ajax({
        type: 'POST',
        url: 'http://localhost:8000/checkTheUser',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        contentType: 'application/octet-stream; charset=utf-8',
        data: id_token,
        success: function (response) {
            console.log(response);
            if (response === 'success') {
                alert(response);
            }
            else {
                signOut();
                alert('failed');
            }
        }
    })

}

function signOut() {
    var auth2 = gapi.auth2.getAuthInstance();
    auth2.signOut().then(function () {
        console.log('UserForm signed out.');
    }).then(
        alert('sign out')
    );
}