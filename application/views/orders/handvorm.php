<!-- Orderoverzicht - Handvorm Pagina-->

<!-- Check user rights to view this page -->
<?php if ( $user_access === 1) { ?>

<?php
	$rockerurl = site_url ("assets/images/wait.gif");
?>

<!-- Page heading row -->
<div class="row d-flex justify-content-center border-dark bg-dark py-1 mt-0 mb-2">
	
	<!-- Button trigger modal nieuweorders overzicht -->
	<div class="col my-2 text-center">
		<a class="btn btn-secondary btn-sm" href="#" role="button" data-toggle="modal" data-target="#nieuwe_orders">
			<i class="fas fa-plus"></i> Nieuwe orders - <?php echo $afdeling_name; ?>
		</a>
	</div>
	
	<!-- Button trigger modal nieuweorders overzicht -->
	<div class="col my-2 text-center">
		<a class="btn btn-secondary btn-sm" href="#" role="button" data-toggle="modal" data-target="#nieuwe_mutaties">
			<i class="fas fa-plus"></i> Nieuwe productiemutaties - <?php echo $afdeling_name; ?>
		</a>
	</div>
	
	<div class="col my-2 text-center">
		<a class="btn btn-secondary btn-sm" href="/orders" role="button"><i class="fas fa-arrow-left"></i> Terug</a>
	</div>
	
</div>
<!-- END OF - Page heading row -->


<!-- Modal nieuwe orders -->
<div class="modal fade" id="nieuwe_orders" tabindex="-1" role="dialog" aria-labelledby="nieuwe_orders_label" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="nieuwe_orders_label">Nieuwe orders - <?php echo $afdeling_name; ?> </h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="POST">
				<div class="modal-body">
					<?php 
						//echo "<pre>";
						//print_r($new_orders);
						//echo "</pre>";
					?>
					<div class="row">
						<div class="col">
							<div class="alert alert-info" role="alert">
								Onderstaand overzicht toont alle nieuwe orders die zijn toegevoegd van 72 uur of nieuwer.
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col">		
							<?php if(!empty($new_orders)){ ?>		
								
								<table class="table table-bordered table-hover table-striped table-sm" width="100%">
								
									<thead class="thead-dark">
										<th>Ordernr</th>
										<th>Artikel</th>
										<th>Aantal besteld</th>
										<th>Datum toegevoegd</th>
				
									</thead>
									
									<?php foreach($new_orders as $new_order){ 
										$date_added = new datetime($new_order->added);						
										$date_added = $date_added->format('d-m-Y H:i');
									?>
										
										<tr>
											<td><?php echo $new_order->ordernr; ?></td>
											<td><?php echo $new_order->product; ?></td>			
											<td><?php echo $new_order->aantal_besteld; ?></td>
											<td><?php echo $date_added; ?></td>
										</tr>
									<?php } ?>
									
								</table>
								
							<?php } else{ ?>
							
								<div class="alert alert-info" role="alert">
									Er zijn geen orders van 3 dagen of nieuwer.
								</div>
								
							<?php } ?>
						</div>
					</div>
					
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Sluiten</button>
				
				</div>
			</form>
		</div>
	</div>
</div>


<!-- Modal nieuwe productiemutaties -->
<div class="modal fade" id="nieuwe_mutaties" tabindex="-1" role="dialog" aria-labelledby="nieuwe_mutaties_label" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="nieuwe_mutaties_label">Nieuwe productiemutaties - <?php echo $afdeling_name; ?> </h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="POST">
				<div class="modal-body">
					
					<div class="row">
						<div class="col">
							<div class="alert alert-info" role="alert">
								Onderstaand overzicht toont alle nieuwe productiemutaties die de afgelopen week zijn toegevoegd.
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col">		
							<?php if(!empty($new_mutaties)){ ?>		
								
								<table class="table table-bordered table-hover table-striped table-sm" width="100%">
								
									<thead class="thead-dark">
										<th>Ordernr</th>										
										<th>Omschrijving</th>
										<th>Regel ID</th>
										<th>Gebruiker</th>
										<th>Aantal geproduceerd</th>
										<th>Datum toegevoegd</th>
										<th>Gesynchroniseerd naar King</th>										
									</thead>
									
									<?php foreach($new_mutaties as $new_mutatie){ 
										$date_added = new datetime($new_mutatie->added);						
										$date_added = $date_added->format('d-m-Y H:i');
										
										
									?>
										<?php if ($new_mutatie->aantal_geproduceerd > 0){ ?>
												<tr>
													<td><?php echo $new_mutatie->order_nr; ?></td>													
													<td><?php echo $new_mutatie->product; ?></td>
													<td><?php echo $new_mutatie->orderregel_id; ?></td>
													<td><?php echo $new_mutatie->user; ?></td>			
													<td><?php echo $new_mutatie->aantal_geproduceerd; ?></td>
													<td><?php echo $date_added; ?></td>
													<td><?php echo $new_mutatie->gesynct; ?></td>
												</tr>
										<?php 											
										}
									}										
										?>
									
								</table>
								
							<?php } else{ ?>
							
								<div class="alert alert-info" role="alert">
									Er zijn geen productiemutaties voor afgelopen week.
								</div>
								
							<?php } ?>
						</div>
					</div>
					
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Sluiten</button>
				
				</div>
			</form>
		</div>
	</div>
</div>


<!-- Order table row-->
<table id="order_table" class="table table-bordered table-hover" width="100%">
	<thead class="bg-secondary text-white">
		<tr>
			<td>Volgorde</td>
			<td>Gereed</td>
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
			<td>Prio</td>
			<td>Nog te produceren</td>
			<td>Aantal deze levering</td>
			<td>Aantal reeds geleverd</td>
			<td>Aantal backorder</td>
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
			<td>Referentie</td>
			<td>Orderregel ID</td>
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
		
		table = $('#order_table').DataTable();

		function  myCallbackFunction(updatedCell, updatedRow, oldValue) {			
			// update column #15 (te_produceren)
		
			$.ajax({
				url : "<?php echo site_url('Orders/update_order_row_handvorm') ?>",
				type : 'POST',
				data: {
					"afdeling": 	"Handvorm",
					"new_data":		updatedCell.data,
					"old_data":		oldValue,
					"new_data_row":	updatedRow.data()
				},
				success: function(){
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
			"columns": [2,3,4,13,14,19],
			"allowNulls": {
				"columns": [2,3,4,13,14,19],
				"errorClass": 'error'
			},
			"confirmationButton": { 
				"confirmCss": 'btn btn-primary btn-sm edit_button',
				"cancelCss": 'btn btn-secondary btn-sm edit_button'
			},
			"inputTypes": [
				{
					"column":2, 
					"type": "list",
					"options":[
						<?php foreach($afdeling_list_all as $afdeling){  ?>
							{ "value": "<?php echo $afdeling['afdeling']; ?>", "display": "<?php echo $afdeling['afdeling']; ?>" },
						<?php } ?>	
					]
				},
				{
					"column":3, 
					"type": "list",
					"options":[
						<?php foreach($sub_afdeling_list_all as $subafdeling){  ?>
							{ "value": "<?php echo $subafdeling['sub_afdeling']; ?>", "display": "<?php echo $subafdeling['sub_afdeling']; ?>" },
						<?php } ?>	
					]
				},
				{
					"column":4, 
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
					"column":13, 
					"type":"text", 
					"options":null 
				},
				{
					"column":14, 
					"type":"text", 
					"options":null 
				},
				{
					"column":19, 
					"type":"text", 
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
		gNodes = table.column(13).nodes();
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
					
					vbase = Number(v[0][13]); //Vorige geproduceerd 
					vadd = Number(amount); //Aantal nu geproduceerd
					v[0][13] = vbase + vadd; 
					
					vAantal = Number(v[0][15]); //Nog te produceren
					vGeproduceerd = Number(v[0][13]); //Totaal geproduceerd
					if (vGeproduceerd > vAantal) {
						v[0][13] = v[0][15];
						vGeproduceerd = vAantal;
					}
					if (vGeproduceerd < 0) {
						v[0][13] = 0;
						vGeproduceerd = 0;
					}			
					
					v[0][15] = vAantal - vadd;
					updatedRow.selector.rows.className = updatedRow.selector.rows.className.replace(" bg-success", "").replace(" bg-warning", "");
					if (vGeproduceerd == 0) {
						// zet status ook op klaar.
						v[0][4] = "Is klaar";
						v[0][1] = "<input type=\"checkbox\" checked />";
						updatedRow.selector.rows.className += " bg-success";
					} else {
						v[0][1] = "<input type=\"checkbox\" />";
						if (vGeproduceerd == 0) {
							v[0][4] = "Nieuw";
						} else {
							v[0][4] = "Mee bezig";
							updatedRow.selector.rows.className += " bg-warning";
						}
					}
					table.rows(updatedRow).data(v);
					cell.innerHTML = v[0][13];
					
					$.ajax({
						url : "<?php echo site_url('Orders/update_order_row_handvorm') ?>",
						type : 'POST',
						data: {
							"afdeling": 	"Handvorm",
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
				oldValue = v[0][4];
				v[0][1] = "<input type=\"checkbox\" checked />";
				v[0][4] = "Is klaar";
				v[0][13] = v[0][15];	// zet geproduceerd op aantal.
				v[0][15] = 0;	// zet te_produceren op 0.
				//console.log(v);			
				t.rows(rowobj).data(v);
				$.ajax({
					url : "<?php echo site_url('Orders/update_order_row_handvorm') ?>",
					type : 'POST',
					data: {
						"afdeling": 	"Handvorm",
						"new_data":		"Is klaar",
						"old_data":		oldValue,
						"new_data_row":	v[0]
					},
					success: function() {
						cc = rowobj.className;
						t.rows(rowobj).invalidate().draw();
						rowobj.className = cc;
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
				oldValue = v[0][4];
				v[0][1] = "<input type=\"checkbox\" />";
				v[0][4] = "Nieuw";
				t.rows(rowobj).data(v);
				$.ajax({
					url : "<?php echo site_url('Orders/update_order_row_handvorm') ?>",
					type : 'POST',
					data: {
						"afdeling": 	"Handvorm",
						"new_data":		"Nieuw",
						"old_data":		oldValue,
						"new_data_row":	v[0]
					},
					success: function() {
						t.rows(rowobj).invalidate().draw();
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
		"order": 				[[ 0, "asc" ]], //Sorteer op Volgorde 
		"rowReorder": 			true,
		"columns": 				[				
									{"className": "custom_order"},	// 0  Volgorde
									null,							// 1 	Afvinken
									{ "visible": false },			// 2  Afdeling
									null,				 			// 3  Sub afdeling
									null, 							// 4  Status
									{ "visible": false },			// 5  Debiteurnr
									{ "visible": false },			// 6  Klant
									null, 							// 7  Ordernr.
									{ "visible": false },			// 8  Artikelnr.
									{ "visible": false }, 			// 9  Opbrgroep
									{ "visible": false }, 			// 10 Orderregel soort
									null, 							// 11 Product
									null, 							// 12 Aantal
									null,							// 13 Geproduceerd
									null,							// 14 Prio
									null,							// 15 Nog te produceren
									null,							// 16 Aantal deze levering
									null,							// 17 Aantal reeds geleverd
									null,							// 18 Aantal backorder
									{ "visible": false },			// 19 Uren
									{ "visible": false },			// 20 Bon
									null, 							// 21 Datum klaar
									null, 							// 22 Week klaar
									null, 							// 23 Dag klaar
									null, 							// 24 Leverdatum
									{ "visible": false }, 			// 25 Week nr
									{ "visible": false }, 			// 26 Dag van de week
									{ "visible": false }, 			// 27 Tonen
									{ "visible": false }, 			// 28 Administratie
									{ "visible": false },			// 29 Laatst bijgewerkt
									{ "visible": false },			// 30 Recept URL
									null,							// 31 Referentie
									{ "visible": false },			// 32 Orderregel ID
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
										"afdeling_filter":		'Handvorm',
										"sub_afdeling_filter":		'Smans'										
									}
								}
	});	
	
	//Reorder volgorde table Handvorm
	table.on( 'row-reorder', function ( e, diff, edit ) {
        for ( var i=0, ien=diff.length ; i<ien ; i++ ) {
            var rowData = table.row( diff[i].node ).data();
			$.ajax({
				url : "<?php echo site_url('Orders/update_order_volgorde') ?>",
				type : 'POST',
				data: {
					"id":			rowData['DT_RowId'],
					"volgorde":		diff[i].newData
				},
				success: function(){
					table.ajax.reload(); //Handvorm
				},
				error: function(){
					alert('Fout: Het wijzigen van de orderregel is niet gelukt');
				}
			});	
			
        }
    } );

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