

(function( $ ) {

	'use strict';
// 	console.log("This is the call");
	var datatableInit = function() {
	    let title = '';
            if ($('.panel-title').length > 0) {
                title = $(".panel-title").text(); // Use panel-title text if it exists
            } else {
                title = $("a.btn").text(); // Use btn text otherwise
            }
            
		
		$('#datatable-default').DataTable({
			dom: 'Blfrtip',
			buttons: [
				{
					extend: 'pdf',
					title: title // Set your custom title here
				},
				{
					extend: 'excel',
					title: title // Set your custom title for Excel
				},
			]
		});
	};
	
	var otherTables = function() {
	   // console.log("function is called well");
	    var title= $(".panel-title").text();
	    $('#datatable-record').DataTable({
			dom: 'Bi',
			buttons: [
				{
					extend: 'pdf',
					title: title // Set your custom title here
				},
				{
					extend: 'excel',
					title: title // Set your custom title for Excel
				},
			],
			info: false,
			lengthMenu: [[-1], ["All"]], // Only show "All" as an option
            pageLength: -1,
            ordering: false,
		});
	}

	$(function() {
		datatableInit();
		otherTables();
	});

}).apply( this, [ jQuery ]);