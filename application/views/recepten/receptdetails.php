<!-- Check user rights to view this page -->
<?php if ( $user_access === 1) { ?>
	
<!-- Page heading row -->	
<div class="row d-flex justify-content-center border border-dark bg-dark py-1 mt-0 mb-2">
	<div class="col my-2 text-center">
		<button class="btn btn-secondary btn-sm" onclick="goBack()"><i class="fas fa-arrow-left"></i> Terug</button>
	</div>
</div>
<!-- END OF - Page heading row -->

<!-- Recept details row -->	
<div class="row">
	<div class="col col-lg-2 p-3 border border-light bg-light">
		<?php echo 'Receptcode: <h5><strong>' . $recept_items[0]['recept_code'] . '</strong></h5><hr />' ; ?>
		<?php echo 'Omschrijving: <h5><strong>' . $recept_items[0]['recept_omschr'] . '</strong></h5><hr />' ; ?>
		<?php echo 'Eindproduct: <h5><strong>' . $recept_items[0]['eindproduct_artnr'] . '</strong></h5><hr />' ; ?>
		<?php echo 'Eindproduct aantal: <h5><strong>' . $recept_items[0]['eindproduct_aantal'] . '</strong></h5><hr />' ; ?>
		<?php
		echo "<pre>";
			//print_r($recept_items);
		echo "</pre>";
		?>
	</div>
	<div class="col p-3">
		<table id="order_table" class="table table-bordered table-hover table-sm" width="100%">
			<thead class="bg-secondary text-white">
				<tr>
					<th>Regelnr</th>
					<th>Soort</th>
					<th>Artikelnummer</th>
					<th>Omschrijving</th>
					<th>Aantal</th>
					<th>Verbruik</th>
					<th>Vrije voorraad</th>
					<th>Max aantal productie (eindproduct)</th>
				</tr>
			</thead>
			<tbody>
				<?php 
					foreach($recept_items as $component){
						echo "<tr>";
							echo "<td>" . $component['component_regelnr'] . "</td>";
							echo "<td>" . $component['component_soort'] . "</td>";
							echo "<td>" . $component['component_artnr'] . "</td>";
							echo "<td>" . $component['component_artomschr'] . "</td>";
							echo "<td>" . $component['component_aantal'] . "</td>";
							echo "<td>" . $component['component_verbruik'] . "</td>";
							if($component['component_soort'] == "Tarief"){
								echo "<td>n.v.t.</td>";
								echo "<td>n.v.t.</td>";
							}else{
								echo "<td>" . $component['vrije_voorraad'] . "</td>";
								echo "<td>" . $component['max_aantal_productie'] . "</td>";
							}
						echo "</tr>";
					}
					//Get the lowest value of max_aantal_productie from array
					$numbers = array_column($recept_items, 'max_aantal_productie');
					$min = min($numbers);
					
					//Display max aantal productie for eindproduct
					echo "<tr>";
						echo "<td  colspan='8'>&nbsp;</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td  colspan='6'></td>";
						echo "<td style='font-size: 16px !important'><strong>Max aantal productie eindproduct:</strong></td>";
						echo "<td style='font-size: 16px !important'><strong>" . $min . "</strong></td>";
					echo "</tr>";
				?>
			</tbody>
		</table>	
	</div>
</div>
<!-- END OF - Order details row -->	

<script>
	//Go to previous page
	function goBack() {
		window.history.back();
	}
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