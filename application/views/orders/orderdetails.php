
<!-- Page heading row -->	
<div class="row d-flex justify-content-center border border-dark bg-dark py-1 mt-0 mb-2">
	<div class="col my-2 text-center">
		<button class="btn btn-secondary btn-sm" onclick="goBack()"><i class="fas fa-arrow-left"></i> Terug</button>
	</div>
</div>
<!-- END OF - Page heading row -->

<!-- Order details row -->	
<div class="row">
	<div class="col col-lg-2 p-3 border border-light bg-light">
		<?php echo 'Administratie: <h5><strong>' . $order_item['administratie'] . '</strong></h5><hr />' ; ?>
		<?php echo 'Klant: <h5><strong>' . $order_item['klant'] . '</strong></h5><hr />' ; ?>
		<?php echo 'Ordernummer: <h5><strong>' . $order_item['ordernr'] . '</strong></h5><hr />' ; ?>
		<?php echo 'Leverdatum: <h5><strong>' . system_to_euro_date($order_item['leverdatum']) . '</strong></h5><hr />' ; ?>
	</div>
	<div class="col p-3">
		<table id="order_table" class="table table-bordered table-hover table-sm" width="100%">
			<thead class="bg-secondary text-white">
				<tr>
					<th>Regelnr</th>
					<th>Soort</th>
					<th>Status</th>
					<th>Artikelnr</th>
					<th>Product</th>
					<th>Afdeling</th>
					<th>Subafdeling</th>
				</tr>
			</thead>
			<tbody>
				<!-- FILL Table with Datatables Ajax call -->
			</tbody>
		</table>	
	</div>
</div>
<!-- END OF - Order details row -->	

<!-- Order log row -->
<div class="row my-3">
	<div class="col">
		<button class="btn btn-secondary btn-sm" type="button" data-toggle="collapse" data-target="#collapseLog" aria-expanded="false" aria-controls="collapseLog">
			Toon/verberg Log
		</button>	
	</div>
</div>
<div class="row collapse" id="collapseLog">
	<div class="col p-3">
		<table class="table table-sm border">
			<thead>
				<tr>
					<th class="bg-light"><small>Ordernr</small></th>
					<th class="bg-light"><small>Regelnr</small></th>
					<th class="bg-light"><small>Actie</small></th>
					<th class="bg-light"><small>Details</small></th>
				</tr>
			</thead>
			<tbody>				
				<?php foreach($order_log as $log_row){
					$order_details = unserialize($log_row['order_details']);
					//$regel = $log_row['orderregel_nr'] . "_" . $count;
					//echo "<pre>";
						//print_r($order_details);
					//echo "</pre>";	
					
					echo "<tr>";
						echo "<td><small>" . $log_row['order_nr'] . "</small></td>";
						echo "<td><small>" . $log_row['orderregel_nr'] . "</small></td>";
						echo "<td><small>" . $log_row['action'] . "</small></td>";
						foreach($order_details as $detail){
							echo "<td class='bg-light border'><small>". $detail ."</small></td>";
						}	
					echo "</tr>";
				}?>
			</tbody>
		</table>
	</div>
</div>
<!-- END OF - Order log row -->	

<script>
	//Go to previous page
	function goBack() {
		window.history.back();
	}
	
	//Datatable script
	var table = $('#order_table').DataTable({
		"processing": 			false,
		"serverSide": 			false,
		"responsive":			true,
		"dom":            		'Bl<"search_bar"f>rtip',
		
		<!-- Check writing rights -->
		<?php if($this->session->userdata('schrijven') == 1){ ?>
		"buttons": 				[
									{
										"extend": 		'print',
										"title": 		'',
										"messageTop": 	'<?php echo "<strong>" . $order_item["ordernr"] . "</strong> - " . $order_item["klant"] ." (<i>" . system_to_euro_date($order_item["leverdatum"]) ."</i>)"; ?>',
										"autoPrint":	true,
										"customize"		:
														function ( win ) {
															var last = null;
															var current = null;
															var bod = [];
											 
															var css = '@page { size: landscape; }',
																head = win.document.head || win.document.getElementsByTagName('head')[0],
																style = win.document.createElement('style');
											 
															style.type = 'text/css';
															style.media = 'print';
											 
															if (style.styleSheet)
															{
															  style.styleSheet.cssText = css;
															} 
															else
															{
															  style.appendChild(win.document.createTextNode(css));
															}
											 
															head.appendChild(style);
															$(win.document.body)
															.css( 'font-size', '10pt' )
															$(win.document.body).find( 'table' )
															.addClass( 'compact' )
															.css( 'font-size', 'inherit' );
														}
									},
									"selectAll",
									"selectNone",
									{
														"text": 'Gereed melden',
														"action": function() {
															var row = table.rows('.selected').data();
															var dt_ids = [];
															if(row.length > 0) {
																for(var n = 0; n < row.length; n++) {
																	dt_ids.push(row[n].DT_RowId);
																}
															}										
															
															$.ajax({
																url : "<?php echo site_url('Orders/update_order_rows') ?>",
																type: 'post',
																data: {
																	"selected_rows": dt_ids
																},
																dataType: 'json',
																success: function(returnedData) {																	
																	table.ajax.reload();
																}
															});
															
															
														}
									}
								],
		"columnDefs": 			[ {
									orderable: false,
									className: 'select-checkbox',
									targets:   0
								} ],
		"select": 				{
									style:    'multi',
									selector: 'td:first-child'
								},
		<?php } ?>
		"stateSave": 			true,
		"pageLength" 			: 999999,
		"lengthMenu": 			[[10, 25, 50, 999999], [10, 25, 50, "Alle"]],			
		"order": 				[ 0, "asc" ], //Sorteer op orderregelnr
		"columns": 				[										
									null, 					// 0  Regelnr
									null,					// 1  Soort
									null, 					// 2  Status
									null, 					// 3  Artikelnr
									null, 					// 4  Product
									null,					// 5  Afdeling
									null 					// 6  Subafdeling

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
									"infoFiltered": 	"(Gefilterd uit _MAX_ orderregels)",
									"buttons": 			{
															"selectAll": "Selecteer alles",
															"selectNone": "Selecteer niks"
														},
									"select": 			{
															"rows": {
																_: "%d rijen geselecteerd",
																0: "Klik op de checkbox om rijen te selecteren",
																1: "1 rij geselecteerd"
															}
														}
								},
		"fixedHeader": 			true,						
		"ajax": 				{
									url : "<?php echo site_url('Orders/get_orderregels') ?>",
									type : 'POST',
									data: {
										"ordernr":		'<?php echo $order_item["ordernr"]; ?>'
									}
								}
	});
</script>
	
