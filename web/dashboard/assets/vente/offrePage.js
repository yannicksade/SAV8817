/**
 Custom module for you to write your own javascript functions
 **/
var OffrePage = function () {
    //global variables
    //----pages

    //----- modal

    /* var sendFile =  function sendFile(file) {
     var uri = "http://localhost/SAV8817.git/web/app_dev.php/apm/vente/offre/index?id="+29;
     var xhr = new XMLHttpRequest();
     var fd = new FormData();

     xhr.open("POST", uri, true);
     xhr.onreadystatechange = function() {
     if (xhr.readyState === 4 && xhr.status === 200) {
     alert(xhr.responseText); // handle response.
     }
     };
     fd.append('myFile', file);
     // Initiate a multipart/form-data upload
     xhr.send(fd);
     };*/

    /*var modalNotification = function (message) {
        var tmpl = [
            // tabindex is required for focus
            '<div class="modal hide fade" tabindex="-1">',
            '<div class="modal-header">',
            '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>',
            '<h4 class="modal-title">Alerte</h4>',
            '</div>',
            '<div class="modal-body">',
            '<p>' + message + '</p>',
            '</div>',
            '<div class="modal-footer">',
            '<a href="#" data-dismiss="modal" class="btn btn-default">Close</a>',
            '</div>',
            '</div>'
        ].join('');

        $(tmpl).modal('modal');
    };*/
    //---- notification

    return {
        init: function () {

        }
    };
}();

jQuery(document).ready(function () {
    OffrePage.init();
});
