var TableDatatablesAjax = function () {

    var initPickers = function () {
        //init date pickers
        $('.date-picker').datepicker({
            rtl: App.isRTL(),
            autoclose: true
        });
    };

    var handleDemo = function () {

        var grid = new Datatable();
        var parent = $('#tab_1');
        grid.init({
            src: $(".datatable_ajax", parent),
            onSuccess: function (grid, response) {
                // grid:        grid object
                // response:    json object of server side ajax response
                // execute some code after table records loaded
                $('#notif-active').click();
            },
            onError: function (grid) {
                // execute some code on network or other general error
            },
            onDataLoad: function(grid) {
                // execute some code on ajax data load
            },

            dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options

                // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
                // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/scripts/datatable.js).
                // So when dropdowns used the scrollable div should be removed.
                //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",

                // save datatable state(pagination, sort, etc) in cookie.
                "bStateSave": true,
                // save custom filters to the state
                "fnStateSaveParams":    function ( oSettings, sValue ) {
                    $(".datatable_ajax tr.filter .form-control", parent).each(function() {
                        sValue[$(this).attr('name')] = $(this).val();
                    });

                    return sValue;
                },

                // read the custom filters from saved state and populate the filter inputs
                "fnStateLoadParams" : function ( oSettings, oData ) {
                    //Load custom filters
                    $(".datatable_ajax tr.filter .form-control", parent).each(function() {
                        var element = $(this);
                        if (oData[element.attr('name')]) {
                            element.val( oData[element.attr('name')] );
                        }
                    });

                    return true;
                },

                "lengthMenu": [
                    [5, 10, 15, 20, 30 -1],
                    [5, 10, 15, 20, 30, "All"] // change per page values here
                ],
                "pageLength": 10, // default record count per page
                "ajax": {
                    "url": ""  // ajax source
                },
                "ordering": false,
                "order": [],// set first column as a default sort by asc
                "processing":     "En cours..."
            }
        });

        // handle group actionsubmit button click
        /*   grid.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
         e.preventDefault();
         var action = $(".table-group-action-input", grid.getTableWrapper());
         if (action.val() !== "" && grid.getSelectedRowsCount() > 0) {
         grid.setAjaxParam("customActionType", "group_action");
         grid.setAjaxParam("customActionName", action.val());
         grid.setAjaxParam("id", grid.getSelectedRows());
         grid.getDataTable().ajax.reload();
         grid.clearAjaxParams();
         } else if (action.val() === "") {
         App.alert({
         type: 'danger',
         icon: 'warning',
         message: 'Please select an action',
         container: grid.getTableWrapper(),
         place: 'prepend'
         });
         } else if (grid.getSelectedRowsCount() === 0) {
         App.alert({
         type: 'danger',
         icon: 'warning',
         message: 'No record selected',
         container: grid.getTableWrapper(),
         place: 'prepend'
         });
         }
         });*/

        //grid.setAjaxParam("order", order);
        //grid.getDataTable().ajax.reload();
        //grid.clearAjaxParams();
    };
    var handleDemo2 = function () {

        var grid = new Datatable();
        var parent = $('#tab_2');
        grid.init({
            src: $(".datatable_ajax", parent),
            onSuccess: function (grid, response) {
                // grid:        grid object
                // response:    json object of server side ajax response
                // execute some code after table records loaded
                $('#notif-active').click();
            },
            onError: function (grid) {
                // execute some code on network or other general error
            },
            onDataLoad: function(grid) {
                // execute some code on ajax data load
            },

            dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options

                // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
                // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/scripts/datatable.js).
                // So when dropdowns used the scrollable div should be removed.
                //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",

                // save datatable state(pagination, sort, etc) in cookie.
                "bStateSave": true,
                // save custom filters to the state
                "fnStateSaveParams":    function ( oSettings, sValue ) {
                    $(".datatable_ajax tr.filter .form-control", parent).each(function() {
                        sValue[$(this).attr('name')] = $(this).val();
                    });

                    return sValue;
                },

                // read the custom filters from saved state and populate the filter inputs
                "fnStateLoadParams" : function ( oSettings, oData ) {
                    //Load custom filters
                    $(".datatable_ajax tr.filter .form-control", parent).each(function() {
                        var element = $(this);
                        if (oData[element.attr('name')]) {
                            element.val( oData[element.attr('name')] );
                        }
                    });

                    return true;
                },

                "lengthMenu": [
                    [5, 10, 15, 20, 30 -1],
                    [5, 10, 15, 20, 30, "All"] // change per page values here
                ],
                "pageLength": 10, // default record count per page
                "ajax": {
                    "url": "?q=boutiques"  // ajax source
                },
                "ordering": false,
                "order": [],// set first column as a default sort by asc
                //"loadingRecords": "Loading...",
                "processing":     "Processing..."
            }
        });

        // handle group actionsubmit button click
        /*   grid.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
         e.preventDefault();
         var action = $(".table-group-action-input", grid.getTableWrapper());
         if (action.val() !== "" && grid.getSelectedRowsCount() > 0) {
         grid.setAjaxParam("customActionType", "group_action");
         grid.setAjaxParam("customActionName", action.val());
         grid.setAjaxParam("id", grid.getSelectedRows());
         grid.getDataTable().ajax.reload();
         grid.clearAjaxParams();
         } else if (action.val() === "") {
         App.alert({
         type: 'danger',
         icon: 'warning',
         message: 'Please select an action',
         container: grid.getTableWrapper(),
         place: 'prepend'
         });
         } else if (grid.getSelectedRowsCount() === 0) {
         App.alert({
         type: 'danger',
         icon: 'warning',
         message: 'No record selected',
         container: grid.getTableWrapper(),
         place: 'prepend'
         });
         }
         });*/

        //grid.setAjaxParam("order", order);
        //grid.getDataTable().ajax.reload();
        //grid.clearAjaxParams();
    };

    return {

        //main function to initiate the module
        init: function () {
            initPickers();
            handleDemo();
            handleDemo2();
        }

    };

}();

jQuery(document).ready(function () {
    TableDatatablesAjax.init();
});