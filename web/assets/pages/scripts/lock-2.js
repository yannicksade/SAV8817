var Lock = function () {

    return {
        //main function to initiate the module
        init: function () {

            $.backstretch([
                $('#bg-1').val(),
                $('#bg-2').val(),
                $('#bg-3').val(),
                $('#bg-4').val(),
                $('#bg-5').val(),
                $('#bg-6').val()
            ], {
                fade: 1000,
                duration: 8000
            });
        }

    };

}();

jQuery(document).ready(function () {
    Lock.init();
});