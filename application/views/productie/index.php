<!-- Producite mutaties Pagina-->

<!-- Check user rights to view this page -->
<?php if ( $user_access === 1) { ?>

<!-- Page heading row -->
<div class="row d-flex justify-content-center border border-dark bg-dark py-1 mt-0 mb-2">
	
	<!-- Check writing rights -->
	<?php if($this->session->userdata('schrijven') == 1){ ?>
		<div class="col my-2 text-center">
			<form action="" method="POST">		
				<button type="submit" class="btn btn-secondary btn-sm" name="submit-sync-productie-mutaties-beutech">
					<i class="fas fa-sync-alt"></i> Synchroniseer Productie Mutaties Beutech
				</button>
			</form>
		</div>
	<?php } ?>
	
</div>
<!-- END OF - Page heading row -->


<!-- Productie mutaties details row -->	
<div class="row">
	<div class="col p-3">
			<table id="productie_mutatie_table" class="table table-bordered table-hover table-sm" width="100%">
				<thead class="bg-secondary text-white">
					<tr>
						<th>Afdeling</th>
						<th>Ordernummer</th>						
						<th>Aantal geproduceerd</th>
						<th>Datum geproduceerd</th>
						<th>Gebruiker</th>					
						<th>Gesynchroniseerd</th>
						<th>Administratie</th>
						<th>Orderregel ID</th>	
						<th>Status/error</th>
						<th>Orderlock status</th>
					</tr>
				</thead>
				<tbody>
					<!-- FILL Table with Datatables Ajax call -->
				</tbody>
			</table>		
	</div>
</div>
<!-- END OF - Productie mutaties details row -->	


<script>
	//Go to previous page
	function goBack() {
		window.history.back();
	}
	
	//Datatable script
	var table = $('#productie_mutatie_table').DataTable({
		"processing": 			false,
		"serverSide": 			false,
		"responsive":			true,
		"dom":            		'l<"search_bar"f>rtip',
		"stateSave": 			false,
		"pageLength" 			: 999999,
		"lengthMenu": 			[[10, 25, 50, 999999], [10, 25, 50, "Alle"]],			
		"order": 				[ 3, "desc" ], //Sorteer op orderregelnr
		"columns": 				[										
									null, 					// 0  Afdeling
									null,					// 1  Ordernummer
									null,					// 2  Aantal geproduceerd
									null, 					// 3  Datum geproduceerd
									null, 					// 4  Gebruiker
									null, 					// 5  Gesynchroniseerd
									null,					// 6  Administratie
									null,					// 7  Orderregel ID
									null,					// 7  status error
									null					// 7  status lock
								],
		"language": 			{
									"processing": 		"<div class='alert alert-success m-0 p-3'>Even geduld, de resultaten worden opgehaald</div>",
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
									"infoFiltered": 	"(Gefilterd uit _MAX_ orderregels)"
								},
		"fixedHeader": 			true,						
		"ajax": 				{
									url : "<?php echo site_url('Productie/get_productie_mutaties') ?>",
									type : 'POST',
									data: {
										"afdeling":		'alles'
									}
								}
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