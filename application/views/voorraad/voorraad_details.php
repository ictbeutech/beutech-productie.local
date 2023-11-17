<!-- Check user rights to view this page -->
<?php if ( $user_access === 1) { ?>

	
<!-- Page heading row -->	
<div class="row d-flex justify-content-center border border-dark bg-dark py-1 mt-0 mb-2">
	<div class="col my-2 text-center">
		<button class="btn btn-secondary btn-sm" onclick="goBack()"><i class="fas fa-arrow-left"></i> Terug</button>
	</div>
</div>
<!-- END OF - Page heading row -->

<!-- Voorraad details row -->	
<div class="row">
	<div class="col p-3">
		<?php if(!empty($voorraad_artikelen)){ ?>
			<table id="voorraad_table" class="table table-bordered table-hover table-sm" width="100%">
				<thead class="bg-secondary text-white">
					<tr>
						<th>Artikelnummer</th>
						<th>Artikel status</th>
						<th>Omschrijving</th>
						<th>Vrije voorraad</th>
						<th>In bestelling</th>
						<th>Gereserveerd</th>
						<th>Afdeling</th>
						<th>Debiteur</th>
					</tr>
				</thead>
				<tbody>
					<!-- FILL Table with Datatables Ajax call -->
				</tbody>
			</table>	
		<?php }else{
			echo "Er is nog geen voorraad beschikbaar voor deze afdeling.";
		} ?>	
	</div>
</div>
<!-- END OF - Order details row -->	


<script>
	//Go to previous page
	function goBack() {
		window.history.back();
	}
	
	//Datatable script
	var table = $('#voorraad_table').DataTable({
		"processing": 			false,
		"serverSide": 			false,
		"responsive":			true,
		"dom":            		'l<"search_bar"f>rtip',
		"stateSave": 			true,
		"pageLength" 			: 999999,
		"lengthMenu": 			[[10, 25, 50, 999999], [10, 25, 50, "Alle"]],			
		"order": 				[ 0, "asc" ], //Sorteer op orderregelnr
		"columns": 				[										
									null, 					// 0  Artikelnummer
									null,					// 1  Artikelstatus
									null,					// 2  Omschrijving
									null, 					// 3  Vrij voorrad
									null, 					// 4  In bestelling
									null, 					// 5  Gereserveerd
									null,					// 6  Afdeling
									null					// 7  Debiteur
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
									url : "<?php echo site_url('Voorraad/get_voorraad') ?>",
									type : 'POST',
									data: {
										"afdeling":		'<?php echo $afdeling; ?>'
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