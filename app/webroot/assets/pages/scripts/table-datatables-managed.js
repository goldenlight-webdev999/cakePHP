var TableDatatablesManaged = function () {

    var initTable1 = function () {

        var table = $('#datatable_index');

        // begin first table
       table.dataTable({

            // Internationalisation. For more info refer to http://datatables.net/manual/i18n
            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
                },
                "emptyTable": "No data available in table",
                "info": "Showing _START_ to _END_ of _TOTAL_ records",
                "infoEmpty": "No records found",
                "infoFiltered": "(filtered from _MAX_ records on page)",
                "lengthMenu": "Show _MENU_",
                "search": "Page Search:",
                "zeroRecords": "No matching records found",
                "paginate": {
                    "previous":"Prev",
                    "next": "Next",
                    "last": "Last",
                    "first": "First"
                }
            },
            bInfo: false,
            buttons: [
                //{ extend: 'print', className: 'btn dark btn-outline' },
                { extend: 'copy', className: 'btn blue ', exportOptions:{columns: ':visible:not(:eq(-1))' }},
                { extend: 'print', className: 'btn green-meadow ', title: '', exportOptions:{columns: ':visible:not(:eq(-1))' } },
                { extend: 'excel', className: 'btn yellow  ', exportOptions:{columns: ':visible:not(:eq(-1))' } ,  customizeData: function ( data ) {
                for (var i=0; i<data.body.length; i++){
                    for (var j=0; j<data.body[i].length; j++ ){
                        data.body[i][j] = '\u200C' + data.body[i][j];
                    }
                }
            } },
                { extend: 'colvis', className: 'btn purple-plum ', text: 'Columns'}
            ],
            
            // Or you can use remote translation file
            //"language": {
            //   url: '//cdn.datatables.net/plug-ins/3cfcc339e89/i18n/Portuguese.json'
            //},

            // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
            // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js). 
            // So when dropdowns used the scrollable div should be removed. 
            //"dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
            
            "dom": "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable
            "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
            "colReorder": false,
            "columnDefs": [ {
                "targets": 0,
                "orderable": true,
                "searchable": true
            }],

            "lengthMenu": [
                [5, 10, 20, 50, -1],
                [5, 10, 20, 50, "All"] // change per page values here
            ],
            // set the initial value
            "pageLength": 20,    
            "bPaginate": false,
            "pagingType": "bootstrap_full_number",
            "columnDefs": [{  // set default column settings
                'orderable': true,
                'targets': [0]
            }, 
            {
                "searchable": false,
                "targets": [-1]
            },
            {
                "orderable": false,
                "targets": [-1]
            },
            {
                "searchable": true,
                "targets": [0]
            }],
            "order": [
                [0, "asc"]
            ] // set first column as a default sort by asc
        });
        
        
        var tableWrapper = jQuery('#datatable_index_wrapper');

        table.find('.group-checkable').change(function () {
            var set = jQuery(this).attr("data-set");
            var checked = jQuery(this).is(":checked");
            jQuery(set).each(function () {
                if (checked) {
                    $(this).prop("checked", true);
                    $(this).parents('tr').addClass("active");
                } else {
                    $(this).prop("checked", false);
                    $(this).parents('tr').removeClass("active");
                }
            });
            jQuery.uniform.update(set);
        });

        table.on('change', 'tbody tr .checkboxes', function () {
            $(this).parents('tr').toggleClass("active");
        });
    };
    
    
    var initTable2 = function () {

        var table = $('#datatable_contactindex');

        // begin first table
       table.dataTable({

            // Internationalisation. For more info refer to http://datatables.net/manual/i18n
            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
                },
                "emptyTable": "No data available in table",
                "info": "Showing _START_ to _END_ of _TOTAL_ records",
                "infoEmpty": "No records found",
                "infoFiltered": "(filtered from _MAX_ records on page)",
                "lengthMenu": "Show _MENU_",
                "search": "Page Search:",
                "zeroRecords": "No matching records found",
                "paginate": {
                    "previous":"Prev",
                    "next": "Next",
                    "last": "Last",
                    "first": "First"
                }
            },
            bInfo: false,
            buttons: [
                //{ extend: 'print', className: 'btn dark btn-outline' },
                { extend: 'copy', className: 'btn blue ', exportOptions:{columns: ':visible:not(:eq(-1))' }},
                { extend: 'print', className: 'btn green-meadow ', title: '', exportOptions:{columns: ':visible:not(:eq(-1))' } },
                { extend: 'excel', className: 'btn yellow  ', exportOptions:{columns: ':visible:not(:eq(-1))' },  customizeData: function ( data ) {
                for (var i=0; i<data.body.length; i++){
                    for (var j=0; j<data.body[i].length; j++ ){
                        data.body[i][j] = '\u200C' + data.body[i][j];
                    }
                }
            } },
                { extend: 'colvis', className: 'btn purple-plum ', text: 'Columns'}
            ],
            
            // Or you can use remote translation file
            //"language": {
            //   url: '//cdn.datatables.net/plug-ins/3cfcc339e89/i18n/Portuguese.json'
            //},

            // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
            // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js). 
            // So when dropdowns used the scrollable div should be removed. 
            //"dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
            
            "dom": "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable
            "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
            "colReorder": false,
            "columnDefs": [ {
                "targets": [5,6],
                "visible": false
            }],

            "lengthMenu": [
                [5, 10, 20, 50, -1],
                [5, 10, 20, 50, "All"] // change per page values here
            ],
            // set the initial value
            "pageLength": 20,    
            "bPaginate": false,
            "pagingType": "bootstrap_full_number",
            "columnDefs": [{  // set default column settings
                'orderable': false,
                'targets': [0]
            }, 
            {
                "searchable": false,
                "targets": [-1]
            },
            {
                "orderable": false,
                "targets": [-1]
            },
            {
                "searchable": false,
                "targets": [0]
            }, 
            {
                //"targets": [3,5,6,7,11],
                "targets": [3,5,6,10,11,12,13,14,15,16],
                "visible": false
            }],
            "order": [
                //
                [13, "desc"]
            ] // set first column as a default sort by asc
        });
        
        
        var tableWrapper = jQuery('#datatable_contactindex_wrapper');

        table.find('.group-checkable').change(function () {
            var set = jQuery(this).attr("data-set");
            var checked = jQuery(this).is(":checked");
            jQuery(set).each(function () {
                if (checked) {
                    $(this).prop("checked", true);
                    $(this).parents('tr').addClass("active");
                } else {
                    $(this).prop("checked", false);
                    $(this).parents('tr').removeClass("active");
                }
            });
            jQuery.uniform.update(set);
        });

        table.on('change', 'tbody tr .checkboxes', function () {
            $(this).parents('tr').toggleClass("active");
        });
    };
    
    
   
    var initTable3 = function () {

        var table = $('#datatable_receiptsnumbersindex');
        var index = $(table).find('th:last').index();

        // begin first table
       table.dataTable({

            // Internationalisation. For more info refer to http://datatables.net/manual/i18n
            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
                },
                "emptyTable": "No data available in table",
                "info": "Showing _START_ to _END_ of _TOTAL_ records",
                "infoEmpty": "No records found",
                "infoFiltered": "(filtered from _MAX_ records on page)",
                "lengthMenu": "Show _MENU_",
                "search": "Page Search:",
                "zeroRecords": "No matching records found",
                "paginate": {
                    "previous":"Prev",
                    "next": "Next",
                    "last": "Last",
                    "first": "First"
                }
            },
            bInfo: false,
            buttons: [
                //{ extend: 'print', className: 'btn dark btn-outline' },
                { extend: 'copy', className: 'btn blue '},
                { extend: 'print', className: 'btn green-meadow ', title: '' },
                { extend: 'excel', className: 'btn yellow ',  customizeData: function ( data ) {
                for (var i=0; i<data.body.length; i++){
                    for (var j=0; j<data.body[i].length; j++ ){
                        data.body[i][j] = '\u200C' + data.body[i][j];
                    }
                }
            } },
                { extend: 'colvis', className: 'btn purple-plum ', text: 'Columns'}
            ],
            
            // Or you can use remote translation file
            //"language": {
            //   url: '//cdn.datatables.net/plug-ins/3cfcc339e89/i18n/Portuguese.json'
            //},

            // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
            // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js). 
            // So when dropdowns used the scrollable div should be removed. 
            //"dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
            
            "dom": "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable
            "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
            "colReorder": false,
            
            "lengthMenu": [
                [5, 10, 20, 50, -1],
                [5, 10, 20, 50, "All"] // change per page values here
            ],
            // set the initial value
            "pageLength": 20,    
            "bPaginate": false,
            "pagingType": "bootstrap_full_number",
            "order": [
                [index, "desc"]
            ] // set first column as a default sort by asc
        });
        
        
        var tableWrapper = jQuery('#datatable_receiptsnumbersindex_wrapper');

        table.find('.group-checkable').change(function () {
            var set = jQuery(this).attr("data-set");
            var checked = jQuery(this).is(":checked");
            jQuery(set).each(function () {
                if (checked) {
                    $(this).prop("checked", true);
                    $(this).parents('tr').addClass("active");
                } else {
                    $(this).prop("checked", false);
                    $(this).parents('tr').removeClass("active");
                }
            });
            jQuery.uniform.update(set);
        });

        table.on('change', 'tbody tr .checkboxes', function () {
            $(this).parents('tr').toggleClass("active");
        });
    };


        var initTable4 = function () {

        var table = $('#datatable_apptindex');

        // begin first table
       table.dataTable({

            // Internationalisation. For more info refer to http://datatables.net/manual/i18n
            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
                },
                "emptyTable": "No data available in table",
                "info": "Showing _START_ to _END_ of _TOTAL_ records",
                "infoEmpty": "No records found",
                "infoFiltered": "(filtered from _MAX_ records on page)",
                "lengthMenu": "Show _MENU_",
                "search": "Page Search:",
                "zeroRecords": "No matching records found",
                "paginate": {
                    "previous":"Prev",
                    "next": "Next",
                    "last": "Last",
                    "first": "First"
                }
            },
            bInfo: false,
            buttons: [
                //{ extend: 'print', className: 'btn dark btn-outline' },
                { extend: 'copy', className: 'btn blue ', exportOptions:{columns: ':visible:not(:eq(-1))' }},
                { extend: 'print', className: 'btn green-meadow ', title: '', exportOptions:{columns: ':visible:not(:eq(-1))' } },
                { extend: 'excel', className: 'btn yellow  ', exportOptions:{columns: ':visible:not(:eq(-1))' },  customizeData: function ( data ) {
                for (var i=0; i<data.body.length; i++){
                    for (var j=0; j<data.body[i].length; j++ ){
                        data.body[i][j] = '\u200C' + data.body[i][j];
                    }
                }
            } },
                { extend: 'colvis', className: 'btn purple-plum ', text: 'Columns'}
            ],
            
            // Or you can use remote translation file
            //"language": {
            //   url: '//cdn.datatables.net/plug-ins/3cfcc339e89/i18n/Portuguese.json'
            //},

            // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
            // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js). 
            // So when dropdowns used the scrollable div should be removed. 
            //"dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
            
            "dom": "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable
            "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
            "colReorder": false,
            "columnDefs": [ {
                "targets": [5,6],
                "visible": false
            }],

            "lengthMenu": [
                [5, 10, 20, 50, -1],
                [5, 10, 20, 50, "All"] // change per page values here
            ],
            // set the initial value
            "pageLength": 20,    
            "bPaginate": false,
            "pagingType": "bootstrap_full_number",
             "columnDefs": [{  // set default column settings
                'orderable': true,
                'targets': [0]
            }, 
            {
                "searchable": false,
                "targets": [-1]
            },
            {
                "orderable": false,
                "targets": [-1]
            },
            {
                "searchable": true,
                "targets": [0]
            },
            {
                "targets": [6],
                "visible": false
            }],
            "order": [
                [5, "asc"]
            ] // set first column as a default sort by asc
        });
        
        
        var tableWrapper = jQuery('#datatable_apptindex_wrapper');

        table.find('.group-checkable').change(function () {
            var set = jQuery(this).attr("data-set");
            var checked = jQuery(this).is(":checked");
            jQuery(set).each(function () {
                if (checked) {
                    $(this).prop("checked", true);
                    $(this).parents('tr').addClass("active");
                } else {
                    $(this).prop("checked", false);
                    $(this).parents('tr').removeClass("active");
                }
            });
            jQuery.uniform.update(set);
        });

        table.on('change', 'tbody tr .checkboxes', function () {
            $(this).parents('tr').toggleClass("active");
        });
    };
    
    
    var initTable5 = function () {

        var table = $('#datatable_groupsmsoutbox');

        // begin first table
       table.dataTable({

            // Internationalisation. For more info refer to http://datatables.net/manual/i18n
            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
                },
                "emptyTable": "No data available in table",
                "info": "Showing _START_ to _END_ of _TOTAL_ records",
                "infoEmpty": "No records found",
                "infoFiltered": "(filtered from _MAX_ records on page)",
                "lengthMenu": "Show _MENU_",
                "search": "Page Search:",
                "zeroRecords": "No matching records found",
                "paginate": {
                    "previous":"Prev",
                    "next": "Next",
                    "last": "Last",
                    "first": "First"
                }
            },
            bInfo: false,
            buttons: [
                //{ extend: 'print', className: 'btn dark btn-outline' },
                { extend: 'copy', className: 'btn blue ', exportOptions:{columns: ':visible:not(:eq(-1))' }},
                { extend: 'print', className: 'btn green-meadow ', title: '', exportOptions:{columns: ':visible:not(:eq(-1))' } },
                { extend: 'excel', className: 'btn yellow  ', exportOptions:{columns: ':visible:not(:eq(-1))' } ,  customizeData: function ( data ) {
                for (var i=0; i<data.body.length; i++){
                    for (var j=0; j<data.body[i].length; j++ ){
                        data.body[i][j] = '\u200C' + data.body[i][j];
                    }
                }
            } },
                { extend: 'colvis', className: 'btn purple-plum ', text: 'Columns'}
            ],
            
            // Or you can use remote translation file
            //"language": {
            //   url: '//cdn.datatables.net/plug-ins/3cfcc339e89/i18n/Portuguese.json'
            //},

            // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
            // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js). 
            // So when dropdowns used the scrollable div should be removed. 
            //"dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
            
            "dom": "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable
            "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
            "colReorder": false,
            "columnDefs": [ {
                "targets": 0,
                "orderable": true,
                "searchable": true
            }],

            "lengthMenu": [
                [5, 10, 20, 50, -1],
                [5, 10, 20, 50, "All"] // change per page values here
            ],
            // set the initial value
            "pageLength": 20,    
            "bPaginate": false,
            "pagingType": "bootstrap_full_number",
            "columnDefs": [{  // set default column settings
                'orderable': true,
                'targets': [0]
            }, 
            {
                "searchable": false,
                "targets": [-1]
            },
            {
                "orderable": false,
                "targets": [-1]
            },
            {
                "searchable": true,
                "targets": [0]
            }],
            "order": [
                [2, "desc"]
            ] // set first column as a default sort by asc
        });
        
        
        var tableWrapper = jQuery('#datatable_groupsmsoutbox_wrapper');

        table.find('.group-checkable').change(function () {
            var set = jQuery(this).attr("data-set");
            var checked = jQuery(this).is(":checked");
            jQuery(set).each(function () {
                if (checked) {
                    $(this).prop("checked", true);
                    $(this).parents('tr').addClass("active");
                } else {
                    $(this).prop("checked", false);
                    $(this).parents('tr').removeClass("active");
                }
            });
            jQuery.uniform.update(set);
        });

        table.on('change', 'tbody tr .checkboxes', function () {
            $(this).parents('tr').toggleClass("active");
        });
    };
    
    var initTable6 = function () {

        var table = $('#datatable_fax');
        var index = $(table).find('th:first').index();

        // begin first table
       table.dataTable({

            // Internationalisation. For more info refer to http://datatables.net/manual/i18n
            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
                },
                "emptyTable": "No data available in table",
                "info": "Showing _START_ to _END_ of _TOTAL_ records",
                "infoEmpty": "No records found",
                "infoFiltered": "(filtered from _MAX_ records on page)",
                "lengthMenu": "Show _MENU_",
                "search": "Page Search:",
                "zeroRecords": "No matching records found",
                "paginate": {
                    "previous":"Prev",
                    "next": "Next",
                    "last": "Last",
                    "first": "First"
                }
            },
            bInfo: false,
            buttons: [
                //{ extend: 'print', className: 'btn dark btn-outline' },
                { extend: 'copy', className: 'btn blue '},
                { extend: 'print', className: 'btn green-meadow ', title: '' },
                { extend: 'excel', className: 'btn yellow ',  customizeData: function ( data ) {
                for (var i=0; i<data.body.length; i++){
                    for (var j=0; j<data.body[i].length; j++ ){
                        data.body[i][j] = '\u200C' + data.body[i][j];
                    }
                }
            } },
                { extend: 'colvis', className: 'btn purple-plum ', text: 'Columns'}
            ],
            
            // Or you can use remote translation file
            //"language": {
            //   url: '//cdn.datatables.net/plug-ins/3cfcc339e89/i18n/Portuguese.json'
            //},

            // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
            // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js). 
            // So when dropdowns used the scrollable div should be removed. 
            //"dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
            
            "dom": "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable
            "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
            "colReorder": false,
            
            "lengthMenu": [
                [5, 10, 20, 50, -1],
                [5, 10, 20, 50, "All"] // change per page values here
            ],
            // set the initial value
            "pageLength": 20,    
            "bPaginate": false,
            "pagingType": "bootstrap_full_number",
            "columnDefs": [{
                "searchable": false,
                "targets": [-1]
            },
            {
                "orderable": false,
                "targets": [-1]
            }],
            "order": [
                [index, "desc"]
            ] // set first column as a default sort by asc
        });
        
        
        var tableWrapper = jQuery('#datatable_fax');

        table.find('.group-checkable').change(function () {
            var set = jQuery(this).attr("data-set");
            var checked = jQuery(this).is(":checked");
            jQuery(set).each(function () {
                if (checked) {
                    $(this).prop("checked", true);
                    $(this).parents('tr').addClass("active");
                } else {
                    $(this).prop("checked", false);
                    $(this).parents('tr').removeClass("active");
                }
            });
            jQuery.uniform.update(set);
        });

        table.on('change', 'tbody tr .checkboxes', function () {
            $(this).parents('tr').toggleClass("active");
        });
    };

	var initTable7 = function () {

        var table = $('#datatable_multicontest_Subscribers');
        var index = $(table).find('th:first').index();

        // begin first table
       table.dataTable({

            // Internationalisation. For more info refer to http://datatables.net/manual/i18n
            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
                },
                "emptyTable": "No data available in table",
                "info": "Showing _START_ to _END_ of _TOTAL_ records",
                "infoEmpty": "No records found",
                "infoFiltered": "(filtered from _MAX_ records on page)",
                "lengthMenu": "Show _MENU_",
                "search": "Page Search:",
                "zeroRecords": "No matching records found",
                "paginate": {
                    "previous":"Prev",
                    "next": "Next",
                    "last": "Last",
                    "first": "First"
                }
            },
            bInfo: false,
            buttons: [
                //{ extend: 'print', className: 'btn dark btn-outline' },
                { extend: 'copy', className: 'btn blue '},
                { extend: 'print', className: 'btn green-meadow ', title: '' },
                { extend: 'excel', className: 'btn yellow ',  customizeData: function ( data ) {
                for (var i=0; i<data.body.length; i++){
                    for (var j=0; j<data.body[i].length; j++ ){
                        data.body[i][j] = '\u200C' + data.body[i][j];
                    }
                }
            } },
                { extend: 'colvis', className: 'btn purple-plum ', text: 'Columns'}
            ],
            
            // Or you can use remote translation file
            //"language": {
            //   url: '//cdn.datatables.net/plug-ins/3cfcc339e89/i18n/Portuguese.json'
            //},

            // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
            // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js). 
            // So when dropdowns used the scrollable div should be removed. 
            //"dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
            
            "dom": "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable
            "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
            "colReorder": false,
            
            "lengthMenu": [
                [5, 10, 20, 50, -1],
                [5, 10, 20, 50, "All"] // change per page values here
            ],
            // set the initial value
            "pageLength": 20,    
            "bPaginate": false,
            "pagingType": "bootstrap_full_number",
            "columnDefs": [{
                "searchable": false,
                "targets": [-1]
            },
            {
                "orderable": false,
                "targets": [-1]
            }],
            "order": [
                [index, "desc"]
            ] // set first column as a default sort by asc
        });
        
        
        var tableWrapper = jQuery('#datatable_multicontest_Subscribers');

        table.find('.group-checkable').change(function () {
            var set = jQuery(this).attr("data-set");
            var checked = jQuery(this).is(":checked");
            jQuery(set).each(function () {
                if (checked) {
                    $(this).prop("checked", true);
                    $(this).parents('tr').addClass("active");
                } else {
                    $(this).prop("checked", false);
                    $(this).parents('tr').removeClass("active");
                }
            });
            jQuery.uniform.update(set);
        });

        table.on('change', 'tbody tr .checkboxes', function () {
            $(this).parents('tr').toggleClass("active");
        });
    };
	var initTable8 = function () {

        var table = $('#datatable_MultiContest');
        var index = $(table).find('th:first').index();

        // begin first table
       table.dataTable({

            // Internationalisation. For more info refer to http://datatables.net/manual/i18n
            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
                },
                "emptyTable": "No data available in table",
                "info": "Showing _START_ to _END_ of _TOTAL_ records",
                "infoEmpty": "No records found",
                "infoFiltered": "(filtered from _MAX_ records on page)",
                "lengthMenu": "Show _MENU_",
                "search": "Page Search:",
                "zeroRecords": "No matching records found",
                "paginate": {
                    "previous":"Prev",
                    "next": "Next",
                    "last": "Last",
                    "first": "First"
                }
            },
            bInfo: false,
            buttons: [
                //{ extend: 'print', className: 'btn dark btn-outline' },
                { extend: 'copy', className: 'btn blue '},
                { extend: 'print', className: 'btn green-meadow ', title: '' },
                { extend: 'excel', className: 'btn yellow ',  customizeData: function ( data ) {
                for (var i=0; i<data.body.length; i++){
                    for (var j=0; j<data.body[i].length; j++ ){
                        data.body[i][j] = '\u200C' + data.body[i][j];
                    }
                }
            } },
                { extend: 'colvis', className: 'btn purple-plum ', text: 'Columns'}
            ],
            
            // Or you can use remote translation file
            //"language": {
            //   url: '//cdn.datatables.net/plug-ins/3cfcc339e89/i18n/Portuguese.json'
            //},

            // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
            // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js). 
            // So when dropdowns used the scrollable div should be removed. 
            //"dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
            
            "dom": "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable
            "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
            "colReorder": false,
            
            "lengthMenu": [
                [5, 10, 20, 50, -1],
                [5, 10, 20, 50, "All"] // change per page values here
            ],
            // set the initial value
            "pageLength": 20,    
            "bPaginate": false,
            "pagingType": "bootstrap_full_number",
            "columnDefs": [{
                "searchable": false,
                "targets": [-1]
            },
            {
                "orderable": false,
                "targets": [-1]
            }],
            "order": [
                [index, "desc"]
            ] // set first column as a default sort by asc
        });
        
        
        var tableWrapper = jQuery('#datatable_MultiContest');

        table.find('.group-checkable').change(function () {
            var set = jQuery(this).attr("data-set");
            var checked = jQuery(this).is(":checked");
            jQuery(set).each(function () {
                if (checked) {
                    $(this).prop("checked", true);
                    $(this).parents('tr').addClass("active");
                } else {
                    $(this).prop("checked", false);
                    $(this).parents('tr').removeClass("active");
                }
            });
            jQuery.uniform.update(set);
        });

        table.on('change', 'tbody tr .checkboxes', function () {
            $(this).parents('tr').toggleClass("active");
        });
    };
    var initTable9 = function () {

        var table = $('#datatable_Mobilecoupons');
        var index = $(table).find('th:first').index();

        // begin first table
       table.dataTable({

            // Internationalisation. For more info refer to http://datatables.net/manual/i18n
            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
                },
                "emptyTable": "No data available in table",
                "info": "Showing _START_ to _END_ of _TOTAL_ records",
                "infoEmpty": "No records found",
                "infoFiltered": "(filtered from _MAX_ records on page)",
                "lengthMenu": "Show _MENU_",
                "search": "Page Search:",
                "zeroRecords": "No matching records found",
                "paginate": {
                    "previous":"Prev",
                    "next": "Next",
                    "last": "Last",
                    "first": "First"
                }
            },
            bInfo: false,
            buttons: [
                //{ extend: 'print', className: 'btn dark btn-outline' },
                { extend: 'copy', className: 'btn blue '},
                { extend: 'print', className: 'btn green-meadow ', title: '' },
                { extend: 'excel', className: 'btn yellow ',  customizeData: function ( data ) {
                for (var i=0; i<data.body.length; i++){
                    for (var j=0; j<data.body[i].length; j++ ){
                        data.body[i][j] = '\u200C' + data.body[i][j];
                    }
                }
            } },
                { extend: 'colvis', className: 'btn purple-plum ', text: 'Columns'}
            ],
            
            // Or you can use remote translation file
            //"language": {
            //   url: '//cdn.datatables.net/plug-ins/3cfcc339e89/i18n/Portuguese.json'
            //},

            // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
            // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js). 
            // So when dropdowns used the scrollable div should be removed. 
            //"dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
            
            "dom": "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable
            "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
            "colReorder": false,
            
            "lengthMenu": [
                [5, 10, 20, 50, -1],
                [5, 10, 20, 50, "All"] // change per page values here
            ],
            // set the initial value
            "pageLength": 20,    
            "bPaginate": false,
            "pagingType": "bootstrap_full_number",
            "columnDefs": [{
                "searchable": false,
                "targets": [-1]
            },
            {
                "orderable": false,
                "targets": [-1]
            }],
            "order": [
                [index, "desc"]
            ] // set first column as a default sort by asc
        });
        
        
        var tableWrapper = jQuery('#datatable_MultiContest');

        table.find('.group-checkable').change(function () {
            var set = jQuery(this).attr("data-set");
            var checked = jQuery(this).is(":checked");
            jQuery(set).each(function () {
                if (checked) {
                    $(this).prop("checked", true);
                    $(this).parents('tr').addClass("active");
                } else {
                    $(this).prop("checked", false);
                    $(this).parents('tr').removeClass("active");
                }
            });
            jQuery.uniform.update(set);
        });

        table.on('change', 'tbody tr .checkboxes', function () {
            $(this).parents('tr').toggleClass("active");
        });
    };
    return {

        //main function to initiate the module
        init: function () {
            if (!jQuery().dataTable) {
                return;
            }

            initTable1();
            initTable2();
            initTable3();
            initTable4();
            initTable5();
            initTable6();
            initTable7();
            initTable8();
            initTable9();
            
        }

    };

}();

if (App.isAngularJsApp() === false) { 
    jQuery(document).ready(function() {
        TableDatatablesManaged.init();
    });
}