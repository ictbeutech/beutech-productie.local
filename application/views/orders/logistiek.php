<!-- Orderoverzicht - Logistiek Pagina-->

<!-- Check user rights to view this page -->
<?php if ( $user_access === 1) { ?>

<?php
	$rockerurl = site_url ("assets/images/wait.gif");
?>

<!-- Page heading row -->
<div class="row d-flex justify-content-center border border-dark bg-dark py-1 mt-0 mb-2">
	<div class="col my-2 text-center">
		<a class="btn btn-secondary btn-sm" href="/beutech_productie/orders" role="button"><i class="fas fa-arrow-left"></i>Terug</a>
	</div>
</div>
<!-- END OF - Page heading row -->
<br />

<!-- Order table row-->
<table id="order_table" class="table table-bordered table-hover" width="100%">
	<thead class="bg-secondary text-white">
		<tr>
			<td></td>
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
			<td>Aantal deze levering</td>
			<td>Aantal reeds geleverd</td>
			<td>Aantal backorder</td>
			<td>Gereed</td>
			<td>Te produceren</td>
			<td>Uren</td>
			<td>Bon</td>
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
<!-- END OF - Order table row-->

<script>
$(document).ready(function(){
	//Scripts after ajax complete
	$( document ).ajaxComplete(function() {
		
		var table = $('#order_table').DataTable();

		function  myCallbackFunction(updatedCell, updatedRow, oldValue) {			
			$.ajax({
				url : "<?php echo site_url('Orders/update_order_row_logistiek') ?>",
				type : 'POST',
				data: {
					"afdeling": 	"<?php echo $afdeling_name; ?>",
					"new_data":		updatedCell.data,
					"old_data":		oldValue,
					"new_data_row":	updatedRow.data()
				},
				success: function(){
					document.location.reload(true)
					table.ajax.reload();
				},
				error: function(){
					alert('Fout: Het wijzigen van de orderregel is niet gelukt');
				}
			});
		}
		
		<!-- Check writing rights -->
		<?php if($this->session->userdata('schrijven') == 1){ ?>
		table.MakeCellsEditable({
			"onUpdate": myCallbackFunction,
			"inputCss":'my-input-class',
			"columns": [1,2,3,15,17],
			"allowNulls": {
				"columns": [1,2,3,15,17],
				"errorClass": 'error'
			},
			"confirmationButton": { 
				"confirmCss": 'btn btn-primary btn-sm edit_button',
				"cancelCss": 'btn btn-secondary btn-sm edit_button'
			},
			"inputTypes": [
				{
					"column":1, 
					"type": "list",
					"options":[
						<?php foreach($afdeling_list_all as $afdeling_montage){  ?>
							{ "value": "<?php echo $afdeling_montage['afdeling']; ?>", "display": "<?php echo $afdeling_montage['afdeling']; ?>" },
						<?php } ?>	
					]
				},
				{
					"column":2, 
					"type": "list",
					"options":[
						<?php foreach($sub_afdeling_list_all as $subafdeling){  ?>
							{ "value": "<?php echo $subafdeling['sub_afdeling']; ?>", "display": "<?php echo $subafdeling['sub_afdeling']; ?>" },
						<?php } ?>	
					]
				},
				{
					"column":3, 
					"type": "list",
					"options":[
						{ "value": "Nieuw", "display": "Nieuw" },
						{ "value": "Mee bezig", "display": "Mee bezig" },
						{ "value": "Buitendienst", "display": "Buitendienst" },
						{ "value": "WVB", "display": "WVB" },
						{ "value": "Is klaar", "display": "Is klaar" }
					]
				},
				{
					"column":15, 
					"type":"text",
					"attr": {
						"type": "number"
					},
					"options":null 
				},
				{
					"column":17, 
					"type":"textarea", 
					"options":null 
				}
			]
		});
		<?php } ?>
		
	
		
		// ruim oude (+) knop op.
		$(".add_geproduceerd").remove();
		
		<!-- Check writing rights -->
		<?php if($this->session->userdata('schrijven') == 1){ ?>
		// maak (+) knop voor geproduceerd
		gNodes = table.column(15).nodes();
		for (i = 0; i < gNodes.length; i++) {
			gNodes[i].innerHTML = gNodes[i].innerHTML.replace ("&nbsp;", "");	 // cleanup
			gNodes[i].innerHTML += "&nbsp;<input type=\"button\" value=\"+\" class=\"add_geproduceerd\" />";
		}

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
					
					vbase = Number(v[0][15]);
					vadd = Number(amount);
					v[0][15] = vbase + vadd;
					
					vAantal = Number(v[0][12]);
					vGeproduceerd = Number(v[0][15]);
					if (vGeproduceerd > vAantal) {
						v[0][15] = v[0][12];
						vGeproduceerd = vAantal;
					}
					if (vGeproduceerd < 0) {
						v[0][15] = 0;
						vGeproduceerd = 0;
					}			
					
					v[0][16] = vAantal - vGeproduceerd;
					updatedRow.selector.rows.className = updatedRow.selector.rows.className.replace(" bg-success", "").replace(" bg-warning", "");
					if (vAantal == vGeproduceerd) {
						// zit status ook op klaar.
						v[0][3] = "Is klaar";
						v[0][0] = "<input type=\"checkbox\" checked />";
						updatedRow.selector.rows.className += " bg-success";
					} else {
						v[0][0] = "<input type=\"checkbox\" />";
						if (vGeproduceerd == 0) {
							v[0][3] = "Nieuw";
						} else {
							v[0][3] = "Mee bezig";
							updatedRow.selector.rows.className += " bg-warning";
						}
					}
					table.rows(updatedRow).data(v);
					cell.innerHTML = v[0][15];
					
					$.ajax({
						url : "<?php echo site_url('Orders/update_order_row_logistiek') ?>",
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
							AjaxCallDagenOverzicht();
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
		
		// install handler for checkboxes.
		$(":checkbox").unbind();
		$(":checkbox").click(function(e) {
			rowobj = this.parentElement.parentElement;
			isChecked = this.checked;
			if (isChecked) {
				this.parentElement.innerHTML = "<img src=\"<?php echo $rockerurl ?>\" width=16 height=16 />";
				rowobj.className = rowobj.className.replace (" bg-success", "").replace(" bg-warning", "");
				rowobj.className += " bg-success";
				t = $("#order_table").DataTable();
				v = t.rows(rowobj).data();
				oldValue = v[0][3];
				v[0][0] = "<input type=\"checkbox\" checked />";
				v[0][3] = "Is klaar";
				v[0][15] = v[0][12];	// zet geproduceerd op aantal.
				v[0][16] = 0;	// zet te_produceren op 0.
				//console.log(v);			
				t.rows(rowobj).data(v);
				$.ajax({
					url : "<?php echo site_url('Orders/update_order_row_logistiek') ?>",
					type : 'POST',
					data: {
						"afdeling": 	"<?php echo $afdeling_name; ?>",
						"new_data":		"Is klaar",
						"old_data":		oldValue,
						"new_data_row":	v[0]
					},
					success: function() {
						cc = rowobj.className;
						t.rows(rowobj).invalidate().draw();
						rowobj.className = cc;
						AjaxCallDagenOverzicht();
					},
					error: function(){
						alert('Fout: Het wijzigen van de orderregel is niet gelukt');
						document.location.reload(true);
					}
				});
			} else {
				this.parentElement.innerHTML = "<img src=\"<?php echo $rockerurl ?>\" width=16 height=16 />";
				rowobj.className = rowobj.className.replace (" bg-success", "").replace(" bg-warning", "");
				t = $("#order_table").DataTable();
				v = t.rows(rowobj).data();
				oldValue = v[0][3];
				v[0][0] = "<input type=\"checkbox\" />";
				v[0][3] = "Nieuw";
				t.rows(rowobj).data(v);
				$.ajax({
					url : "<?php echo site_url('Orders/update_order_row_logistiek') ?>",
					type : 'POST',
					data: {
						"afdeling": 	"<?php echo $afdeling_name; ?>",
						"new_data":		"Nieuw",
						"old_data":		oldValue,
						"new_data_row":	v[0]
					},
					success: function() {
						t.rows(rowobj).invalidate().draw();
						AjaxCallDagenOverzicht();
					},
					error: function(){
						alert('Fout: Het wijzigen van de orderregel is niet gelukt');
						document.location.reload(true);
					}
				});
			}
		});
		
		<?php } ?>
	});

		
	//Datatable script
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
		"order": 				[[ 22, "asc" ]], //Sorteer op leverdatum
		"columns": 				[										
									null,					// 0 Afvinken
									null,					// 1  Afdeling
									null,				 	// 2  Sub afdeling
									null, 					// 3  Status
									{ "visible": false },	// 4  Debiteurnr
									{ "visible": false },	// 5  Klant
									null, 					// 6  Ordernr.
									{ "visible": false },	// 7  Artikelnr.
									{ "visible": false }, 	// 8 Opbrgroep
									{ "visible": false }, 	// 9 Orderregel soort
									null, 					// 10 Product
									null, 					// 11 Aantal besteld
									null, 					// 12 Aantal deze levering
									null, 					// 13 Aantal reeds geleverd
									null, 					// 14 Aantal backorder
									null,					// 15 Geproduceerd
									null,					// 16 Te produceren
									{ "visible": false },	// 17 Uren
									{ "visible": false },	// 18 Bon
									null, 					// 19 Datum klaar
									{ "visible": false },	// 20 Week klaar
									null, 					// 21 Dag klaar
									null, 					// 22 Leverdatum
									{ "visible": false }, 	// 23 Week nr
									{ "visible": false }, 	// 24 Dag van de week
									{ "visible": false }, 	// 25 Tonen
									{ "visible": false }, 	// 26 Administratie
									{ "visible": false },	// 27 Laatst bijgewerkt
									{ "visible": false },	// 28 Recept URL
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
										"afdeling_filter":		'<?php echo $afdeling; ?>'
										//"week_klaar":			'<?php echo $_POST["week_nr"] . "," . $_POST["week_nr_2"] . "," . $_POST["week_nr_3"] . "," . $_POST["week_nr_4"]; ?>'
									}
								}
	});
		
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