<!-- Orderoverzicht - Draaibank Pagina-->

<!-- Check user rights to view this page -->
<?php if ( $user_access === 1) { ?>

<?php
	$rockerurl = site_url ("assets/images/wait.gif");
?>

<!-- Page heading row -->
<div class="row d-flex justify-content-center border-dark bg-dark py-1 mt-0 mb-2">
	<div class="col my-2 text-center">
		<a class="btn btn-secondary btn-sm" href="/beutech_productie/voorraad/<?php echo $afdeling_name; ?>" role="button">
			<i class='fas fa-eye'></i> Bekijk voorraad - <?php echo $afdeling_name; ?>
		</a>
	</div>
	
	<div class="col my-2 text-center">
		<a class="btn btn-secondary btn-sm" href="/beutech_productie/orders" role="button"><i class="fas fa-arrow-left"></i> Terug</a>
	</div>
	
</div>
<!-- END OF - Page heading row -->


<!-- DRAAIBANK 1 -->
<div class="row pt-2">
	<div class="col-12 m-0">
		<h5 class="d-inline-block m-0 mr-4"> Extrusie:</h5>
	</div>
</div>

<!-- Order table row Draaibank 1-->
<table id="order_table" class="table table-bordered table-hover" width="100%">
	<thead class="bg-secondary text-white">
		<tr>
			<td>Volgorde</td>
			<td>Afdeling</td>
			<td>Sub afdeling</td>
			<td>Status</td>
			<td>Debiteurnr.</td>
			<td>klant</td>
			<td>Ordernr.</td>
			<td>Artikelnr.</td>
			<td>Opbr.groep</td>
			<td>Soort</td>
			<td>Product</td>
			<td>Aantal besteld</td>
			<td>Geproduceerd</td>
			<td>Nog te produceren</td>
			<td>Uren</td>
			<td>Verwachte leverdatum</td>
			<td>Datum klaar</td>
			<td>Week klaar</td>
			<td>Dag klaar</td>
			<td>Leverdatum</td>
			<td>Week</td>
			<td>Dag</td>
			<td>Tonen</td>
			<td>Administratie</td>
			<td>Laatst bijgewerkt</td>
			<td>Recept</td>
		</tr>
	</thead>
	<tbody>
		<!-- FILL Table with Datatables Ajax call -->
	</tbody>
</table>
<!-- END OF - Order table row Draaibank 1-->



<!-- 



<div class="row pt-2 pb-2">
	<div class="col-12 m-0">
		<h5 class="d-inline-block m-0 mr-4"> Extrusie:</h5>
	</div>
</div>


<table id="order_table_draaibank_2" class="table table-bordered table-hover" width="100%">
	<thead class="bg-secondary text-white">
		<tr>
			<td>Volgorde</td>
			<td>Afdeling</td>
			<td>Sub afdeling</td>
			<td>Status</td>
			<td>Debiteurnr.</td>
			<td>klant</td>
			<td>Ordernr.</td>
			<td>Artikelnr.</td>
			<td>Opbr.groep</td>
			<td>Soort</td>
			<td>Product</td>
			<td>Aantal besteld</td>
			<td>Geproduceerd</td>
			<td>Nog te produceren</td>
			<td>Uren</td>
			<td>Verwachte leverdatum</td>
			<td>Datum klaar</td>
			<td>Week klaar</td>
			<td>Dag klaar</td>
			<td>Leverdatum</td>
			<td>Week</td>
			<td>Dag</td>
			<td>Tonen</td>
			<td>Administratie</td>
			<td>Laatst bijgewerkt</td>
			<td>Recept</td>
		</tr>
	</thead>
	<tbody>

	</tbody>
</table>


-->





<script>

$(document).ready(function(){
	
	//Scripts after ajax complete
	$( document ).ajaxComplete(function() {
		
		//DRAAIBANK 1
		table = $('#order_table').DataTable();
		
		//DRAAIBANK 2
		table_2 = $('#order_table_draaibank_2').DataTable();
		

		function  myCallbackFunction(updatedCell, updatedRow, oldValue, row) {			
					
			$.ajax({
				url : "<?php echo site_url('Orders/update_order_row_extrusie') ?>",
				type : 'POST',
				data: {
					"afdeling": 	"<?php echo $afdeling_name; ?>",
					"new_data":		updatedCell.data,
					"old_data":		oldValue,
					"new_data_row":	updatedRow.data()
				},
				success: function(){
					table.ajax.reload(); //DRAAIBANK 1
					table_2.ajax.reload(); //DRAAIBANK 2
				},
				error: function(){
					alert('Fout: Het wijzigen van de orderregel is niet gelukt');
				}
			});
			
		}
		
		//DRAAIBANK 1
		<!-- Check writing rights -->
		<?php if($this->session->userdata('schrijven') == 1){ ?>
		table.MakeCellsEditable({
			"onUpdate": myCallbackFunction,
			"inputCss":'my-input-class',
			"columns": [0,1,2,3,12,14,15],
			"allowNulls": {
				"columns": [0,1,2,3,12,14,15],
				"errorClass": 'error'
			},
			"confirmationButton": { 
				"confirmCss": 'btn btn-primary btn-sm edit_button',
				"cancelCss": 'btn btn-secondary btn-sm edit_button'
			},
			"inputTypes": [
				{
					"column":0, //Volgorde
					"type": "text",
					"options":null
				},
				{
					"column":1, //Afdelinglijst
					"type": "list",
					"options":[
						<?php foreach($afdeling_list_all as $afdeling){  ?>
							{ "value": "<?php echo $afdeling['afdeling']; ?>", "display": "<?php echo $afdeling['afdeling']; ?>" },
						<?php } ?>	
					]
				},
				{
					"column":2, //SubAfdelinglijst
					"type": "list",
					"options":[
						<?php foreach($sub_afdeling_list_all as $subafdeling){  ?>
							{ "value": "<?php echo $subafdeling['sub_afdeling']; ?>", "display": "<?php echo $subafdeling['sub_afdeling']; ?>" },
						<?php } ?>	
					]
				},
				{
					"column":3, //Orderstatus
					"type": "list",
					"options":[
						{ "value": "Nieuw", "display": "Nieuw" },
						{ "value": "Onderdelen aanwezig", "display": "Onderdelen aanwezig" },
						{ "value": "Mee bezig", "display": "Mee bezig" },
						{ "value": "Buitendienst", "display": "Buitendienst" },
						{ "value": "WVB", "display": "WVB" },
						{ "value": "Is klaar", "display": "Is klaar" }
					]
				},
				{
					"column":12, //Geproduceerd
					"type":"text", 
					"options":null 
				},
				{
					"column":14, //Uren
					"type":"text", 
					"options":null 
				},
				{
					"column":15, //Gewenste datum
					"type": "datepicker", 
					"options": {
						"icon": "/beutech_productie/assets/images/calendar.gif" // Optional
					} 
				}
			]
		});
		<?php } ?>
		
		//DRAAIBANK 2
		<!-- Check writing rights -->
		<?php if($this->session->userdata('schrijven') == 1){ ?>
		table_2.MakeCellsEditable({
			"onUpdate": myCallbackFunction,
			"inputCss":'my-input-class',
			"columns": [0,1,2,3,12,14,15],
			"allowNulls": {
				"columns": [0,1,2,3,12,14,15],
				"errorClass": 'error'
			},
			"confirmationButton": { 
				"confirmCss": 'btn btn-primary btn-sm edit_button',
				"cancelCss": 'btn btn-secondary btn-sm edit_button'
			},
			"inputTypes": [
				{
					"column":0, //Volgorde
					"type": "text",
					"options":null
				},
				{
					"column":1, //Afdelinglijst
					"type": "list",
					"options":[
						<?php foreach($afdeling_list_all as $afdeling){  ?>
							{ "value": "<?php echo $afdeling['afdeling']; ?>", "display": "<?php echo $afdeling['afdeling']; ?>" },
						<?php } ?>	
					]
				},
				{
					"column":2, //SubAfdelinglijst
					"type": "list",
					"options":[
						<?php foreach($sub_afdeling_list_all as $subafdeling){  ?>
							{ "value": "<?php echo $subafdeling['sub_afdeling']; ?>", "display": "<?php echo $subafdeling['sub_afdeling']; ?>" },
						<?php } ?>	
					]
				},
				{
					"column":3, //Orderstatus
					"type": "list",
					"options":[
						{ "value": "Nieuw", "display": "Nieuw" },
						{ "value": "Onderdelen aanwezig", "display": "Onderdelen aanwezig" },
						{ "value": "Mee bezig", "display": "Mee bezig" },
						{ "value": "Buitendienst", "display": "Buitendienst" },
						{ "value": "WVB", "display": "WVB" },
						{ "value": "Is klaar", "display": "Is klaar" }
					]
				},
				{
					"column":12, //Geproduceerd
					"type":"text", 
					"options":null 
				},
				{
					"column":14, //Uren
					"type":"text", 
					"options":null 
				},
				{
					"column":15, //Gewenste datum
					"type": "datepicker", 
					"options": {
						"icon": "/beutech_productie/assets/images/calendar.gif" // Optional
					} 
				}
			]
		});
		<?php } ?>
		
		// ruim oude (+) knop op.
		$(".add_geproduceerd").remove();
		$(".add_geproduceerd_2").remove();
		
		<!-- Check writing rights -->
		<?php if($this->session->userdata('schrijven') == 1){ ?>
		//DRAAIBANK 1 - maak (+) knop voor geproduceerd 
		gNodes = table.column(12).nodes();
		for (i = 0; i < gNodes.length; i++) {
			gNodes[i].innerHTML = gNodes[i].innerHTML.replace ("&nbsp;", "");	 // cleanup
			gNodes[i].innerHTML += "&nbsp;<input type=\"button\" value=\"+\" class=\"add_geproduceerd\" />";
		}
		
		//DRAAIBANK 2 - maak (+) knop voor geproduceerd
		gNodes_2 = table_2.column(12).nodes();
		for (i = 0; i < gNodes_2.length; i++) {
			gNodes_2[i].innerHTML = gNodes_2[i].innerHTML.replace ("&nbsp;", "");	 // cleanup
			gNodes_2[i].innerHTML += "&nbsp;<input type=\"button\" value=\"+\" class=\"add_geproduceerd_2\" />";
		}
	
		//DRAAIBANK 1
		function InstallAddHandler() {
			// install handler for geproduceerd (+) knop.
			$(".add_geproduceerd").unbind();
			$(".add_geproduceerd").click(function (e) {
				rowobj = this.parentElement.parentElement;
				cell = this.parentElement;
				amount = prompt ("Hoeveel zijn er geproduceerd?");
				if (amount == null || amount == "") {
				} else {
					
					if (isNaN(amount)) {
						alert ("Geef a.u.b. een getal");
						return;
					}
					
					valNum = Number(amount);
					if (valNum != Math.floor(valNum)) {
						alert ("Geef a.u.b. een geheel getal");
						return;
					}
					
					v = table.rows(rowobj).data();
					
					updatedRow = table.rows(rowobj);
					
					vbase = Number(v[0][12]);
					vadd = Number(amount);
					v[0][12] = vbase + vadd;
					
					vAantal = Number(v[0][11]);
					vGeproduceerd = Number(v[0][12]);
					if (vGeproduceerd > vAantal) {
						v[0][12] = v[0][11];
						vGeproduceerd = vAantal;
					}
					if (vGeproduceerd < 0) {
						v[0][12] = 0;
						vGeproduceerd = 0;
					}			
					
					v[0][13] = vAantal - vGeproduceerd;
					updatedRow.selector.rows.className = updatedRow.selector.rows.className.replace(" bg-success", "").replace(" bg-warning", "");
					if (vAantal == vGeproduceerd) {
						// zet status ook op klaar.
						v[0][3] = "Is klaar";
						//v[0][0] = "<input type=\"checkbox\" checked />";
						updatedRow.selector.rows.className += " bg-success";
					} else {
						//v[0][0] = "<input type=\"checkbox\" />";
						if (vGeproduceerd == 0) {
							v[0][3] = "Nieuw";
						} else {
							v[0][3] = "Mee bezig";
							updatedRow.selector.rows.className += " bg-warning";
						}
					}
					table.rows(updatedRow).data(v);
					cell.innerHTML = v[0][12];
					
					$.ajax({
						url : "<?php echo site_url('Orders/update_order_row_extrusie') ?>",
						type : 'POST',
						data: {
							"afdeling": 	"<?php echo $afdeling_name; ?>",
							"new_data":		vGeproduceerd,
							"old_data":		vbase,
							"new_data_row":	v[0],
							"geproduceerd":	vadd
						},
						success: function() {
							cc = updatedRow.selector.rows.className;
							table.rows(updatedRow).invalidate().draw();
							updatedRow.selector.rows.className = cc;
							cell.innerHTML += "&nbsp;<input type=\"button\" value=\"+\" class=\"add_geproduceerd\" />";
							InstallAddHandler();
						},
						error: function(){
							alert('Fout: Het wijzigen van de orderregel is niet gelukt');
							document.location.reload(true);
						}
					});					
				}
			});			
		}
				
		InstallAddHandler();
				
		//DRAAIBANK 2
		function InstallAddHandler_2() {
			// install handler for geproduceerd (+) knop.
			$(".add_geproduceerd_2").unbind();
			$(".add_geproduceerd_2").click(function (e) {
				rowobj = this.parentElement.parentElement;
				cell = this.parentElement;
				amount = prompt ("Hoeveel zijn er geproduceerd?");
				if (amount == null || amount == "") {
				} else {
					
					if (isNaN(amount)) {
						alert ("Geef a.u.b. een getal");
						return;
					}
					
					valNum = Number(amount);
					if (valNum != Math.floor(valNum)) {
						alert ("Geef a.u.b. een geheel getal");
						return;
					}
					
					v = table_2.rows(rowobj).data();
					
					updatedRow = table_2.rows(rowobj);
					
					vbase = Number(v[0][12]);
					vadd = Number(amount);
					v[0][12] = vbase + vadd;
					
					vAantal = Number(v[0][11]);
					vGeproduceerd = Number(v[0][12]);
					if (vGeproduceerd > vAantal) {
						v[0][12] = v[0][11];
						vGeproduceerd = vAantal;
					}
					if (vGeproduceerd < 0) {
						v[0][12] = 0;
						vGeproduceerd = 0;
					}			
					
					v[0][13] = vAantal - vGeproduceerd;
					updatedRow.selector.rows.className = updatedRow.selector.rows.className.replace(" bg-success", "").replace(" bg-warning", "");
					if (vAantal == vGeproduceerd) {
						// zet status ook op klaar.
						v[0][3] = "Is klaar";
						//v[0][0] = "<input type=\"checkbox\" checked />";
						updatedRow.selector.rows.className += " bg-success";
					} else {
						//v[0][0] = "<input type=\"checkbox\" />";
						if (vGeproduceerd == 0) {
							v[0][3] = "Nieuw";
						} else {
							v[0][3] = "Mee bezig";
							updatedRow.selector.rows.className += " bg-warning";
						}
					}
					table_2.rows(updatedRow).data(v);
					cell.innerHTML = v[0][12];
					
					$.ajax({
						url : "<?php echo site_url('Orders/update_order_row_extrusie') ?>",
						type : 'POST',
						data: {
							"afdeling": 	"<?php echo $afdeling_name; ?>",
							"new_data":		vGeproduceerd,
							"old_data":		vbase,
							"new_data_row":	v[0],
							"geproduceerd":	vadd
						},
						success: function() {
							cc = updatedRow.selector.rows.className;
							table_2.rows(updatedRow).invalidate().draw();
							updatedRow.selector.rows.className = cc;
							cell.innerHTML += "&nbsp;<input type=\"button\" value=\"+\" class=\"add_geproduceerd_2\" />";
							InstallAddHandler_2();
						},
						error: function(){
							alert('Fout: Het wijzigen van de orderregel is niet gelukt');
							document.location.reload(true);
						}
					});					
				}
			});			
		}
				
		InstallAddHandler_2();
				
		<?php } ?>
		
	});
	
	//DRAAIBANK 1
	//Datatable script Draaibank 1
	var table = $('#order_table').DataTable({
		"processing": 			false,
		"serverSide": 			false,
		"responsive":			true,
		"dom":            		'Bl<"search_bar"f>rti<"pagination-sm"p>',
		"buttons": {
			"buttons": [
				'colvis'
			],
			"dom": {
				"button": {
					"tag": "button",
					"className": "btn btn-success btn-sm"
				},
				"buttonLiner": {
					"tag": null
				}
			}
		},
		"stateSave": 			true,
		"pageLength" 			: 999999,
		"lengthMenu": 			[[10, 25, 50, 999999], [10, 25, 50, "Alle"]],			
		"order": 				[[ 0, "asc" ]], //Sorteer op Volgorde 
		"rowReorder": 			true,
		"columns": 				[				
									{"className": "custom_order"},	// 0  Volgorde
									//null,							// 1  Afvinken
									{ "visible": false },			// 1  Afdeling
									null,				 			// 2  Sub afdeling
									null, 							// 3  Status
									{ "visible": false },			// 4  Debiteurnr
									{ "visible": false },			// 5  Klant
									null, 							// 6  Ordernr.
									{ "visible": false },			// 7  Artikelnr.
									{ "visible": false }, 			// 8  Opbrgroep
									{ "visible": false }, 			// 9 Orderregel soort
									null, 							// 10 Product
									null, 							// 11 Aantal
									null,							// 12 Geproduceerd
									null,							// 13 Te produceren
									//null,							// 14 Aantal backorder
									{ "visible": false },			// 14 Uren
									null,							// 15 Gewenste datum
									null, 							// 16 Datum klaar
									null, 							// 17 Week klaar
									null, 							// 18 Dag klaar
									null, 							// 19 Leverdatum
									{ "visible": false }, 			// 20 Week nr
									{ "visible": false }, 			// 21 Dag van de week
									{ "visible": false }, 			// 22 Tonen
									{ "visible": false }, 			// 23 Administratie
									{ "visible": false },			// 24 Laatst bijgewerkt
									null,							// 25 Recept URL
								],
		"language": 			{
									"processing": 	"<div class='alert alert-success m-0 p-3'>Even geduld, de resultaten worden opgehaald</div>",
									"loadingRecords": 	"<div class='alert alert-warning'>Even geduld, de orderregels worden opgehaald</div>",
									"lengthMenu": 		"Toon _MENU_ orders per pagina",
									"zeroRecords": 		"<div class='alert alert-warning'>Geen orderregels gevonden.</div>",
									"info": 			"Toon pagina _PAGE_ van _PAGES_ van totaal _TOTAL_",
									"search": 			"Zoeken:",
									"infoEmpty":    	"Toon 0 tot 0 van 0 orderregels",
									"paginate": 		{
															"first":      	"Eerste",
															"last":       	"Laatste",
															"next":       	"Volgende",
															"previous":   	"Vorige"
														},
									"infoFiltered": 	"(Gefilterd uit _MAX_ orderregels)",
									"buttons": 			{
															"colvis": 		"Toon/verberg kolommen"
														}
								},
		"fixedHeader": 			true,						
		"ajax": 				{
									url : "<?php echo site_url('Orders/orders_list') ?>",
									type : 'POST',
									data: {
										"afdeling_filter":		'Extrusie',
										"sub_afdeling_filter":	''
									}
								}
	});	
	
	//Reorder volgorde table Draaibank 1
	table.on( 'row-reorder', function ( e, diff, edit ) {
        for ( var i=0, ien=diff.length ; i<ien ; i++ ) {
            var rowData = table.row( diff[i].node ).data();
			$.ajax({
				url : "<?php echo site_url('Orders/update_order_volgorde') ?>",
				type : 'POST',
				data: {
					"afdeling": 	"<?php echo $afdeling_name; ?>",
					"id":			rowData['DT_RowId'],
					"volgorde":		diff[i].newData
				},
				success: function(){
					table.ajax.reload(); //DRAAIBANK 1
				},
				error: function(){
					alert('Fout: Het wijzigen van de orderregel is niet gelukt');
				}
			});	
			
        }
    } );	
	
	
	/*
	
	
	
	//DRAAIBANK 2
	//Datatable script Draaibank 2	
		
	var table_2 = $('#order_table_draaibank_2').DataTable({
		"processing": 			false,
		"serverSide": 			false,
		"responsive":			true,
		"dom":            		'Bl<"search_bar"f>rti<"pagination-sm"p>',									
		"buttons": 				{
									"buttons": [
										'colvis',
									],
									"dom": {
										"button": {
											"tag": "button",
											"className": "btn btn-success btn-sm"
										},
										"buttonLiner": {
											"tag": null
										}
									}
								},
		"stateSave": 			true,
		"pageLength" 			: 999999,
		"lengthMenu": 			[[10, 25, 50, 999999], [10, 25, 50, "Alle"]],			
		"order": 				[[ 0, "asc" ]], //Sorteer op Volgorde
		"rowReorder": 			true,
		"columns": 				[				
									{"className": "custom_order"},	// 0  Volgorde
									//null,							// 1  Afvinken
									{ "visible": false },			// 1  Afdeling
									null,				 			// 2  Sub afdeling
									null, 							// 3  Status
									{ "visible": false },			// 4  Debiteurnr
									{ "visible": false },			// 5  Klant
									null, 							// 6  Ordernr.
									{ "visible": false },			// 7  Artikelnr.
									{ "visible": false }, 			// 8  Opbrgroep
									{ "visible": false }, 			// 9 Orderregel soort
									null, 							// 10 Product
									null, 							// 11 Aantal
									null,							// 12 Geproduceerd
									null,							// 13 Te produceren
									//null,							// 14 Aantal backorder
									{ "visible": false },			// 14 Uren
									null,							// 15 Gewenste datum
									null, 							// 16 Datum klaar
									null, 							// 17 Week klaar
									null, 							// 18 Dag klaar
									null, 							// 19 Leverdatum
									{ "visible": false }, 			// 20 Week nr
									{ "visible": false }, 			// 21 Dag van de week
									{ "visible": false }, 			// 22 Tonen
									{ "visible": false }, 			// 23 Administratie
									{ "visible": false },			// 24 Laatst bijgewerkt
									null,							// 25 Recept URL
								],
		"language": 			{
									"processing": 	"<div class='alert alert-success m-0 p-3'>Even geduld, de resultaten worden opgehaald</div>",
									"loadingRecords": 	"<div class='alert alert-warning'>Even geduld, de orderregels worden opgehaald</div>",
									"lengthMenu": 		"Toon _MENU_ orders per pagina",
									"zeroRecords": 		"<div class='alert alert-warning'>Geen orderregels gevonden.</div>",
									"info": 			"Toon pagina _PAGE_ van _PAGES_ van totaal _TOTAL_",
									"search": 			"Zoeken:",
									"infoEmpty":    	"Toon 0 tot 0 van 0 orderregels",
									"paginate": 		{
															"first":      	"Eerste",
															"last":       	"Laatste",
															"next":       	"Volgende",
															"previous":   	"Vorige"
														},
									"infoFiltered": 	"(Gefilterd uit _MAX_ orderregels)",
									"buttons": 			{
															"colvis": 		"Toon/verberg kolommen"
														}
								},
		"fixedHeader": 			true,						
		"ajax": 				{
									url : "<?php echo site_url('Orders/orders_list') ?>",
									type : 'POST',
									data: {
										"afdeling_filter":		'Extrusie',
										"sub_afdeling_filter":	''
									}
								}
	});	
	
	//Reorder volgorde table Draaibank 2
	table_2.on( 'row-reorder', function ( e, diff, edit ) {
        for ( var i=0, ien=diff.length ; i<ien ; i++ ) {
            var rowData = table_2.row( diff[i].node ).data();
			$.ajax({
				url : "<?php echo site_url('Orders/update_order_volgorde') ?>",
				type : 'POST',
				data: {
					"afdeling": 	"<?php echo $afdeling_name; ?>",
					"id":			rowData['DT_RowId'],
					"volgorde":		diff[i].newData
				},
				success: function(){
					table_2.ajax.reload(); //DRAAIBANK 2
				},
				error: function(){
					alert('Fout: Het wijzigen van de orderregel is niet gelukt');
				}
			});	
			
        }
    } );
	
	
	
	*/
	
	
	
	
});

</script>

<!-- Show message if user has no rights to view this page -->	
<?php } else{ ?>
	<div class="row my-3">
		<div class="col text-center">
			<div class="alert alert-danger" role="alert">
				U heeft niet de juiste rechten om deze pagina te bekijken.
			</div>
		</div>	
	</div>
<?php } ?>