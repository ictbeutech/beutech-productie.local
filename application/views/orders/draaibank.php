<!-- Orderoverzicht - Draaibank Pagina-->

<!-- Check user rights to view this page -->
<?php if ( $user_access === 1) { ?>

<?php
	$rockerurl = site_url ("assets/images/wait.gif");
?>

<!-- Page heading row -->
<div class="row d-flex justify-content-center border-dark bg-dark py-1 mt-0 mb-2">
	<div class="col my-2 text-center">
		<a class="btn btn-secondary btn-sm" href="/voorraad/<?php echo $afdeling_name; ?>" role="button">
			<i class='fas fa-eye'></i> Bekijk voorraad - <?php echo $afdeling_name; ?>
		</a>
	</div>
	
	<!-- Button trigger modal Mail Order overzicht -->
	<div class="col my-2 text-center">
		<a class="btn btn-secondary btn-sm" href="#" role="button" data-toggle="modal" data-target="#mail_order_overzicht">
			<i class='fas fa-envelope'></i> Mail orderoverzicht - <?php echo $afdeling_name; ?>
		</a>
	</div>
	
	<!-- Button trigger modal  Mail Voorraad overzicht -->
	<div class="col my-2 text-center">
		<a class="btn btn-secondary btn-sm" href="#" role="button" data-toggle="modal" data-target="#mail_voorraad_overzicht">
			<i class='fas fa-envelope'></i> Mail voorraadoverzicht - <?php echo $afdeling_name; ?>
		</a>
	</div>
	
	<div class="col my-2 text-center">
		<a class="btn btn-secondary btn-sm" href="/orders" role="button"><i class="fas fa-arrow-left"></i> Terug</a>
	</div>
	
</div>
<!-- END OF - Page heading row -->

<!-- Modal mail Order overzicht -->
<div class="modal fade" id="mail_order_overzicht" tabindex="-1" role="dialog" aria-labelledby="mail_order_overzicht_label" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="mail_order_overzicht_label">Mail orderoverzicht - <?php echo $afdeling_name; ?> </h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="POST">
				<div class="modal-body">
					<?php 
						//echo "<pre>";
						//print_r($debiteuren_draaibank);
						//echo "</pre>";
					?>
					<div class="row">
						<div class="col">
							<div class="form-group">
								<label for="debiteurSelect"><strong>Selecteer debiteur:</strong></label>
								<select class="form-control" name="debiteur_nr" id="debiteurSelect">
									<option value="" disabled selected>Kies een debiteur.</option>
									<?php foreach($debiteuren_orders_draaibank as $debiteur){ ?>
										<option value="<?php echo $debiteur->debiteurnr; ?>"><?php echo $debiteur->klant; ?></option>
									<?php } ?>
								</select>						
							</div>
							<div id="mailIntroDiv" class="form-group">
								<label for="mailIntro"><strong>Mail aanhef tekst:</strong></label>
								<textarea class="form-control" id="mailIntro" name="mail_intro" rows="10">Geachte heer / mevrouw,

Hierbij ontvangt u het orderoverzicht. Heeft u hier vragen over neem dan contact op met 0521 34 35 36.

Met vriendelijke groet,

Beutech Kunststoffen en Bewerking
Oevers 11
8331 VC Steenwijk</textarea>
							</div>
							
							<div id="debiteur-orders"></div>
							<textarea style="display:none;" class="form-control" id="debiteur-orders_textarea" name="mail_orders" rows=""></textarea>
							<div id="contact-row"></div>
							<div id="cc-orders">
								<br>
								<strong>Mail naar CC:</strong>
								<hr>
								<br>
								<div class="form-check">
									<input type="checkbox" name="mail_cc" class="form-check-input" id="cc_checkbox" value="orders@beutech.nl" checked>
									<label class="form-check-label" for="cc_checkbox">Mail cc naar orders@beutech.nl</label>
								</div>
							</div>
						</div>
					</div>
					
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Sluiten</button>
					<button type="submit" id="submit_mail" name="submit-orders-debiteur" class="btn btn-primary">Verstuur orderoverzicht</button>
				</div>
			</form>
		</div>
	</div>
</div>


<!-- Modal mail Voorraad overzicht -->
<div class="modal fade" id="mail_voorraad_overzicht" tabindex="-1" role="dialog" aria-labelledby="mail_order_overzicht_label" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="mail_order_overzicht_label">Mail voorraadoverzicht - <?php echo $afdeling_name; ?> </h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="POST">
				<div class="modal-body">
					<?php 
						//echo "<pre>";
						//print_r($debiteuren_draaibank);
						//echo "</pre>";
					?>
					<div class="row">
						<div class="col">
							<div class="form-group">
								<label for="debiteurSelect"><strong>Selecteer debiteur:</strong></label>
								<select class="form-control" name="debiteur_nr" id="debiteurSelectVoorraad">
									<option value="" disabled selected>Kies een debiteur.</option>
									<?php foreach($debiteuren_voorraad_draaibank as $debiteur){ ?>
										<option value="<?php echo $debiteur->art_debiteur; ?>"><?php echo $debiteur->klant; ?></option>
									<?php } ?>
								</select>						
							</div>
							<div id="mailIntroDivVoorraad" class="form-group">
								<label for="mailIntro"><strong>Mail aanhef tekst:</strong></label>
								<textarea class="form-control" id="mailIntro" name="mail_intro_voorraad" rows="10">Geachte heer / mevrouw,

Hierbij ontvangt u het voorraadoverzicht. Heeft u hier vragen over neem dan contact op met 0521 34 35 36.

Met vriendelijke groet,

Beutech Kunststoffen en Bewerking
Oevers 11
8331 VC Steenwijk</textarea>
							</div>
							
							<div id="debiteur-voorraad"></div>
							<textarea style="display:none;" class="form-control" id="debiteur-voorraad_textarea" name="mail_voorraad" rows=""></textarea>
							<div id="contact-row-voorraad"></div>							
						</div>
					</div>
					
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Sluiten</button>
					<button type="submit" id="submit_mail_voorraad" name="submit-voorraad-debiteur" class="btn btn-primary">Verstuur voorraadoverzicht</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- DRAAIBANK 1 -->
<div class="row pt-2">
	<div class="col-12 m-0">
		<h5 class="d-inline-block m-0 mr-4"> Draaibank 1:</h5>
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
			<td>Referentie</td>
		</tr>
	</thead>
	<tbody>
		<!-- FILL Table with Datatables Ajax call -->
	</tbody>
</table>
<!-- END OF - Order table row Draaibank 1-->

<!-- DRAAIBANK 2 -->
<div class="row pt-2 pb-2">
	<div class="col-12 m-0">
		<h5 class="d-inline-block m-0 mr-4"> Draaibank 2:</h5>
	</div>
</div>

<!-- Order table row Draaibank 2-->
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
			<td>Referentie</td>
		</tr>
	</thead>
	<tbody>
		<!-- FILL Table with Datatables Ajax call -->
	</tbody>
</table>
<!-- END OF - Order table row Draaibank 2-->

<script>

$(document).ready(function(){

// MAIL ORDEROVERZICHT		
	// Lauch modal mail order overzichten
	$('#mail_order_overzicht').on('shown.bs.modal', function () {
		$('#debiteurSelect').trigger('focus')
	})
	
	$("#mailIntroDiv").hide();
	$("#cc-orders").hide();
	$("#submit_mail").hide();
	$("#custom_mail_form").hide();
		
	//After select change -> Get orders for debiteur/afdeling 
	$("#debiteurSelect").change(function(){
							
		var getDebiteurNr = $(this).val();
		
		if(getDebiteurNr != '')
		{
			$.ajax({
				type: "POST",
				dateType: 'json',
				url: "<?php echo site_url('Orders/get_orderregelsDebiteur') ?>",
				data: {
					debiteurnr: getDebiteurNr,
					afdeling: '<?php echo $afdeling_name; ?>'
				},
				success: function(data){
					$("#debiteur-orders").html(data.order_table);
					$("#debiteur-orders_textarea").html(data.order_table);
					$("#contact-row").html(data.contact_row);
					$("#mailIntroDiv").show();
					$("#cc-orders").show();
					$("#submit_mail").show();
					$("#custom_mail_form").hide();
				}
			});
		}
		else
		{
			$("#debiteur-orders").html('');
			$("#mailIntroDiv").hide();
			$("#cc-orders").hide();
			$("#submit_mail").hide();
			$("#custom_mail_form").hide();
		}
				
	});

	//Custom mail radio
	$(document).on('click', 'input[name=mail_to_radio]', function(){
		if($('input:radio[name=mail_to_radio]:checked').val() == "custom_mail"){
			$("#custom_mail_form").show();
		}else{
			$("#custom_mail_form").hide();
		}
	});
	
// MAIL VOORRAADOVERZICHT
	// Lauch modal mail voorraad overzichten
	$('#mail_voorraad_overzicht').on('shown.bs.modal', function () {
		$('#debiteurSelectVoorraad').trigger('focus')
	})
	
	$("#mailIntroDivVoorraad").hide();
	$("#submit_mail_voorraad").hide();
	$("#custom_mail_form_voorraad").hide();
	
	//After select change -> Get orders for debiteur/afdeling 
	$("#debiteurSelectVoorraad").change(function(){
							
		var getDebiteurNr = $(this).val();
		
		if(getDebiteurNr != '')
		{
			$.ajax({
				type: "POST",
				dateType: 'json',
				url: "<?php echo site_url('Voorraad/get_voorraadDebiteur') ?>",
				data: {
					debiteurnr: getDebiteurNr,
					afdeling: 'art_Draaibank'
				},
				success: function(data){
					$("#debiteur-voorraad").html(data.voorraad_table);
					$("#debiteur-voorraad_textarea").html(data.voorraad_table);
					$("#contact-row-voorraad").html(data.contact_row_voorraad);
					$("#mailIntroDivVoorraad").show();
					$("#submit_mail_voorraad").show();
					$("#custom_mail_form_voorraad").hide();
				}
			});
		}
		else
		{
			$("#debiteur-voorraad").html('');
			$("#mailIntroDivVoorraad").hide();
			$("#submit_mail_voorraad").hide();
			$("#custom_mail_form_voorraad").hide();
		}
				
	});

	//Custom mail radio
	$(document).on('click', 'input[name=mail_to_radio_voorraad]', function(){
		if($('input:radio[name=mail_to_radio_voorraad]:checked').val() == "custom_mail"){
			$("#custom_mail_form_voorraad").show();
		}else{
			$("#custom_mail_form_voorraad").hide();
		}
	});
	
	
	
	//Scripts after ajax complete
	$( document ).ajaxComplete(function() {
		
		//DRAAIBANK 1
		table = $('#order_table').DataTable();
		
		//DRAAIBANK 2
		table_2 = $('#order_table_draaibank_2').DataTable();
		

		function  myCallbackFunction(updatedCell, updatedRow, oldValue, row) {			
					
			$.ajax({
				url : "<?php echo site_url('Orders/update_order_row_draaibank') ?>",
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
						"icon": "/assets/images/calendar.gif" // Optional
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
						"icon": "/assets/images/calendar.gif" // Optional
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
						url : "<?php echo site_url('Orders/update_order_row_draaibank') ?>",
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
						url : "<?php echo site_url('Orders/update_order_row_draaibank') ?>",
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
		
		/*
		
		//DRAAIBANK 1
		// install handler for checkboxes.
		$(".Draaibank1").unbind();
		$(".Draaibank1").click(function(e) {
			alert("test1");
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
				v[0][13] = v[0][12];	// zet geproduceerd op aantal.
				v[0][14] = 0;	// zet te_produceren op 0.
				//console.log(v);			
				t.rows(rowobj).data(v);
				$.ajax({
					url : "<?php echo site_url('Orders/update_order_row_draaibank') ?>",
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
					url : "<?php echo site_url('Orders/update_order_row_draaibank') ?>",
					type : 'POST',
					data: {
						"afdeling": 	"<?php echo $afdeling_name; ?>",
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
		
		//DRAAIBANK 2
		// install handler for checkboxes.
		
		$(".Draaibank2").unbind();
		$(".Draaibank2").click(function(e) {
			alert("test2");
			rowobj = this.parentElement.parentElement;
			isChecked = this.checked;
			if (isChecked) {
				this.parentElement.innerHTML = "<img src=\"<?php echo $rockerurl ?>\" width=16 height=16 />";
				rowobj.className = rowobj.className.replace (" bg-success", "").replace(" bg-warning", "");
				rowobj.className += " bg-success";
				t_2 = $("#order_table_draaibank_2").DataTable();
				v = t_2.rows(rowobj).data();
				oldValue = v[0][4];
				v[0][1] = "<input type=\"checkbox\" checked />";
				v[0][4] = "Is klaar";
				v[0][13] = v[0][12];	// zet geproduceerd op aantal.
				v[0][14] = 0;	// zet te_produceren op 0.
				//console.log(v);			
				t_2.rows(rowobj).data(v);
				$.ajax({
					url : "<?php echo site_url('Orders/update_order_row_draaibank') ?>",
					type : 'POST',
					data: {
						"afdeling": 	"<?php echo $afdeling_name; ?>",
						"new_data":		"Is klaar",
						"old_data":		oldValue,
						"new_data_row":	v[0]
					},
					success: function() {
						cc = rowobj.className;
						t_2.rows(rowobj).invalidate().draw();
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
				t_2 = $("#order_table_draaibank_2").DataTable();
				v = t_2.rows(rowobj).data();
				oldValue = v[0][4];
				v[0][1] = "<input type=\"checkbox\" />";
				v[0][4] = "Nieuw";
				t_2.rows(rowobj).data(v);
				$.ajax({
					url : "<?php echo site_url('Orders/update_order_row_draaibank') ?>",
					type : 'POST',
					data: {
						"afdeling": 	"<?php echo $afdeling_name; ?>",
						"new_data":		"Nieuw",
						"old_data":		oldValue,
						"new_data_row":	v[0]
					},
					success: function() {
						t_2.rows(rowobj).invalidate().draw();
					},
					error: function(){
						alert('Fout: Het wijzigen van de orderregel is niet gelukt');
						document.location.reload(true);
					}
				});
			}
		});
		
		*/
		
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
									null,							// 26 Referentie
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
										"afdeling_filter":		'Draaibank',
										"sub_afdeling_filter":	'Draaibank 1'
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
									null,							// 26 Referentie
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
										"afdeling_filter":		'Draaibank',
										"sub_afdeling_filter":	'Draaibank 2'
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