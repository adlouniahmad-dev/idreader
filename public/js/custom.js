$('#logout').on('click', function () {
    document.location.href = "https://www.google.com/accounts/Logout?continue=https://appengine.google.com/_ah/logout?continue=http://localhost:8000/logout";
});

$(function () {
   if ($('.form-group .required').hasClass('required')) {
       $('.form-group .required').removeClass('required');
   }
});
