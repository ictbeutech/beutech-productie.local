<!-- Orderoverzicht Pagina-->

<!-- Check user rights to view this page -->
<?php if ( $user_access === 1) { ?>

<!-- Page heading row -->
<div class="row d-flex justify-content-center border border-dark bg-dark py-1 mt-0 mb-2">
	
	<!-- Check writing rights -->
	<?php if($this->session->userdata('schrijven') == 1){ ?>
		<div class="col my-2 text-center">
			<form action="" method="POST">		
				<button type="submit" class="btn btn-secondary btn-sm" name="submit-sync-tibuplast">
					<i class="fas fa-sync-alt"></i> Synchroniseer Orders Tibuplast
				</button>
			</form>
		</div>
		<div class="col my-2 text-center">
			<form action="" method="POST">		
				<button type="submit" class="btn btn-secondary btn-sm" name="submit-sync-beutech">
					<i class="fas fa-sync-alt"></i> Synchroniseer Orders Beutech
				</button>
			</form>
		</div>
		<div class="col my-2 text-center">
			<button class="btn btn-secondary btn-sm active-button"></button>
		</div>
		<div class="col my-2 text-center">
			<form action="" method="POST">		
				<button type="submit" class="btn btn-secondary btn-sm" name="submit-truncate-orders">
					<i class="fas fa-trash-alt"></i> Alle orders verwijderen
				</button>
			</form>
		</div>	
	<?php } ?>
</div>
<!-- END OF - Page heading row -->

<!-- Order Table -->
<table id="order_table" class="table table-bordered table-hover table-sm" width="100%">
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
			<td>Aantal</td>
			<td>Geproduceerd</td>
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
<!-- END OF - Order Table -->

<script>
$(document).ready(function(){
		
	//Datatable script
	$('#order_table').DataTable({
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
		"afdeling_filter":		'',
		"pageLength" 			: 25,
		"lengthMenu": 			[[10, 25, 50, 999999], [10, 25, 50, "Alle"]],			
		"order": 				[[ 19, "asc" ],[ 6, "asc" ]], //Sorteer op leverdatum & ordernummer
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
									null, 					// 11 Aantal
									null,					// 12 Geproduceerd
									null,					// 13 Te produceren
									{ "visible": false },	// 14 Uren
									{ "visible": false },	// 15 Bon
									null, 					// 16 Datum klaar
									null, 					// 17 Week klaar
									null, 					// 18 Dag klaar
									null, 					// 19 Leverdatum
									{ "visible": false }, 	// 20 Week nr
									{ "visible": false }, 	// 21 Dag van de week
									{ "visible": false }, 	// 22 Tonen
									{ "visible": false }, 	// 23 Administratie
									{ "visible": false },	// 24 Laatst bijgewerkt
									{ "visible": false },	// 25 Recept URL
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
										"overzicht":		'All'
									}
								}
	});
	
	//Scripts after ajax complete
	$( document ).ajaxComplete(function() {
		//Hide inactive rows	
		$('.hide').hide();
		$('.active-button').html('<i class="fas fa-eye"></i> Toon verborgen regels');
	});
	
	//Show active rows	
	$('.active-button').click(function(){
		$('.hide').toggle();
		$(this).html() === 'Toon alleen actieve regels' ? $(this).html('<i class="fas fa-eye"></i> Toon verborgen regels') : $(this).html('Toon alleen actieve regels');
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