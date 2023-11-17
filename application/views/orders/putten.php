<!-- Orderoverzicht - Putten Pagina-->

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
	<div class="col my-2 text-center">
		<button class="btn btn-secondary btn-sm" type="button" data-toggle="collapse" data-target=".multi-collapse" aria-expanded="false" aria-controls="multiCollapse_week1 multiCollapse_week2 multiCollapse_week3 multiCollapse_week4">Verberg/Toon alle week overzichten</button>
	</div>
	<div class="col my-2 text-center">
		<button class="btn btn-secondary btn-sm" type="button" data-toggle="collapse" data-target="#multiCollapse_week1" aria-expanded="false" aria-controls="multiCollapse_week1">Verberg/Toon week <?php echo $_POST['week_nr']; ?></button>
	</div>
	<div class="col my-2 text-center">
		<button class="btn btn-secondary btn-sm" type="button" data-toggle="collapse" data-target="#multiCollapse_week2" aria-expanded="false" aria-controls="multiCollapse_week2">Verberg/Toon week <?php echo $_POST['week_nr_2']; ?></button>
	</div>
	<div class="col my-2 text-center">
		<button class="btn btn-secondary btn-sm" type="button" data-toggle="collapse" data-target="#multiCollapse_week3" aria-expanded="false" aria-controls="multiCollapse_week3">Verberg/Toon week <?php echo $_POST['week_nr_3']; ?></button>
	</div>
	<div class="col my-2 text-center">
		<button class="btn btn-secondary btn-sm" type="button" data-toggle="collapse" data-target="#multiCollapse_week4" aria-expanded="false" aria-controls="multiCollapse_week4">Verberg/Toon week <?php echo $_POST['week_nr_4']; ?></button>
	</div>	
</div>
<!-- END OF - Page heading row -->
<br />
<!-- Show Week 1 overzicht-->
<div class="row collapse multi-collapse show m-0" id="multiCollapse_week1">
	<div class="col-12 m-0">
		<h5 class="d-inline-block m-0 mr-4"> <?php echo "Weeknummer: " . $_POST['week_nr']; ?> </h5>
		
		<!-- AJAX form beschikbare uren week 1-->
		<form class="d-inline-block" id="beschikbare_uren">
			<!-- Check writing rights -->
			<?php if($this->session->userdata('schrijven') == 1){ ?>
				<button class="btn btn-primary btn-sm" type="submit">Update uren week  <?php echo $_POST['week_nr']; ?></button>
			<?php } ?>		
			<?php $year = date('Y');?>
			<input id="year" name="year" type="hidden" value="<?php echo $year; ?>" />
			<input id="afdeling" name="afdeling" type="hidden" value="<?php echo $afdeling; ?>" />
	</div>

	<!-- Dag overzicht week 1 -->
	<?php foreach($sub_afdelingen_list_1 as $day){ ?>

		<!-- Set variables for AJAX Post -->
		<input id="id" name="id" type="hidden" value="<?php echo isset($uren_overzicht_1['id']) ? $uren_overzicht_1['id'] : ''; ?>" />
		<input id="week" name="week" type="hidden" value="<?php echo $_POST['week_nr']; ?>" />
		<?php 
			$dag_naam = $day['dag'];
			$uren_dag = isset($uren_overzicht_1['uren_' . $dag_naam]) ? $uren_overzicht_1['uren_' . $dag_naam] : ''; 
			if(empty($uren_dag)){
				$uren_dag = 0;
			}
		?>
		<div class="col-12 col-sm-6 col-xl p-1 pr-lg-3 pl-lg-3">
			<table class="table table-hover table-sm m-0">
				<thead class="thead bg-secondary text-white">
					<tr>
						<th scope="col"></th>
						<th scope="col"><i class="fas fa-calendar-day"></i> <?php echo $day['dag']; ?> <small><?php echo $day['datum_klaar']; ?></small></th>
						<th scope="col" class="text-right"><i class="fas fa-tally"></i></th>
						<th scope="col" class="text-right"><i class="fas fa-user-clock"></i></th>		
					</tr>
				</thead>
				<tbody>
					<?php 
						$aantal = 0;
						foreach($day['sub_afdelingen_list'] as $sub_afdelingen){ 
							$aantal = $aantal + $sub_afdelingen['te_produceren'];
							$subafdelingen = array_column($day['opmerkingen'], 'sub_afdeling');
					?>	
						
						<tr>
							<td>
								<!-- Modal opmerkingen -->
								<?php 
									if(isset($day['opmerkingen']) && !empty($day['opmerkingen']) && in_array($sub_afdelingen['sub_afdeling'],$subafdelingen)){	
										$modal_id = strip_tags($sub_afdelingen['sub_afdeling']);
										$modal_id = preg_replace("/[^A-Za-z0-9 ]/", '', $modal_id);
										$modal_id = str_replace(' ', '', $modal_id);
								?> 
								
								<!-- Button trigger modal opmerkingen -->
								<button type="button" class="btn btn-info btn-sm rounded-circle" style="padding: .0rem .24rem;" data-toggle="modal" data-target="#<?php echo $modal_id . $day['datum_klaar']; ?>">
									<i class="fas fa-info-circle"></i>
								</button>

					
								<div class="modal fade" id="<?php echo $modal_id . $day['datum_klaar']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
									<div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
										<div class="modal-content">
											<div class="modal-header">	
												<h5 class="modal-title" id="exampleModalScrollableTitle">Opmerkingen - <?php echo $sub_afdelingen['sub_afdeling']; ?></h5>
												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
													<span aria-hidden="true">&times;</span>
												</button>
											</div>
											<div class="modal-body">
												<div class="alert alert-primary" role="alert">
													<strong><?php echo $day['dag']; ?> - <?php echo $day['datum_klaar']; ?></strong>
												</div>
												<?php
												if(isset($day['opmerkingen'])){
													foreach($day['opmerkingen'] as $opmerking){ 
														if($opmerking['bon'] != "-" && $opmerking['bon'] != "" && $opmerking['sub_afdeling'] == $sub_afdelingen['sub_afdeling']){ ?>
														<div class="alert alert-success" role="alert">
															<?php 
																echo "<strong>Ordernr: </strong>" . $opmerking['ordernr'] . "<br />";
																echo "<strong>Artikelnr: </strong>" . $opmerking['artikelnr'] . "<br />";
																echo "<strong>Opmerking: </strong><br />" . $opmerking['bon'] . "<br />";
															?>
														</div>
														<?php }
													}
												}
												?>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-dismiss="modal">Sluiten</button>
											</div>
										</div>
									</div>
								</div>
								
								<?php } ?>
								<!-- END OF - Modal opmerkingen -->
								
							</td>
							<td>							
								<?php echo $sub_afdelingen['sub_afdeling']; ?> 			
							</td>
							<td class="text-right border-right"><?php echo $sub_afdelingen['te_produceren']; ?></td>
							<td class="text-right"><?php echo $sub_afdelingen['productie_uren']; ?></td>
						</tr>
					<?php } ?>
				</tbody>	
				<tfoot>
					<tr class="bg-light">	
						<td></td>
						<td><br />Totaal productie uren:</td>
						<td class="border-right"></td>
						<td class="text-right"><br /><strong><?php echo $day['aantal_uren']; ?></strong></td>
					</tr>
					<tr class="bg-light">	
						<td></td>
						<td>Totaal beschikbare uren:</td>
						<td class="border-right"></td>
						<td class="text-right" style="padding: 0 !important;"><strong><input class="text-right" id="uren" name="uren_<?php echo $day['dag']; ?>" type="text" value="<?php echo $uren_dag; ?>" maxlength="5" size="5" /></strong></td>
					</tr>
					<tr class="bg-light">	
						<td></td>
						<td>Totaal uren:</td>
						<td class="border-right"></td>
						<?php 
							$totaal_uren_week1 = $uren_dag - $day['aantal_uren'];
							if($totaal_uren_week1 > 0){
								echo '<td class="text-right bg-success text-white">';
							}elseif($totaal_uren_week1 == 0){
								echo '<td class="text-right bg-warning text-white">';
							}else{
								echo '<td class="text-right bg-danger text-white">';
							}
						?>
							<strong><?php echo $totaal_uren_week1; ?></strong>
							</td>
					</tr>
					<tr class="bg-light">
						<td></td>
						<td><strong>Nog te produceren <?php echo $day['dag']; ?>:</strong></td>
						<td class="text-right border-right"><strong><?php echo $aantal; ?></strong></td>
						<td></td>
					</tr>
				</tfoot>	
			</table>
		</div>
	<?php } ?>	
	<!-- END OF - Dag overzicht week 1 -->
	
	</form> <!-- END OF - AJAX form beschikbare uren -->
	
</div>
<!-- END OF - Show Week 1 overzicht -->
<br />
<!-- Show Week 2 overzicht-->
<div class="row collapse multi-collapse show m-0" id="multiCollapse_week2">
	<div class="col-12 m-0">
		<h5 class="d-inline-block m-0 mr-4"> <?php echo "Weeknummer: " . $_POST['week_nr_2']; ?> </h5>
		
		<!-- AJAX form beschikbare uren week 1-->
		<form class="d-inline-block" id="beschikbare_uren">
			<!-- Check writing rights -->
			<?php if($this->session->userdata('schrijven') == 1){ ?>
				<button class="btn btn-primary btn-sm" type="submit">Update uren week <?php echo $_POST['week_nr_2']; ?></button> 
			<?php } ?>		
			<?php $year = date('Y');?>
			<input id="year" name="year" type="hidden" value="<?php echo $year; ?>" />
			<input id="afdeling" name="afdeling" type="hidden" value="<?php echo $afdeling; ?>" />
		
	</div>
	
	<!-- Dag overzicht week 2 -->
	<?php foreach($sub_afdelingen_list_2 as $day){ ?>
		
		<!-- Set variables for AJAX Post -->
		<input id="id" name="id" type="hidden" value="<?php echo isset($uren_overzicht_2['id']) ? $uren_overzicht_2['id'] : ''; ?>" />
		<input id="week" name="week" type="hidden" value="<?php echo $_POST['week_nr_2']; ?>" />
		<?php 
			$dag_naam = $day['dag'];
			$uren_dag = isset($uren_overzicht_2['uren_' . $dag_naam]) ? $uren_overzicht_2['uren_' . $dag_naam] : ''; 
			if(empty($uren_dag)){
				$uren_dag = 0;
			}
		?>
	
		<div class="col-12 col-sm-6 col-xl p-1 pr-lg-3 pl-lg-3">
			<table class="table table-hover table-sm m-0">
				<thead class="thead bg-secondary text-white">
					<tr>
						<th></th>
						<th scope="col"><i class="fas fa-calendar-day"></i> <?php echo $day['dag']; ?>  <small><?php echo $day['datum_klaar']; ?></small></th>
						<th scope="col" class="text-right"><i class="fas fa-tally"></i></th>
						<th scope="col" class="text-right"><i class="fas fa-user-clock"></i></th>		
					</tr>
				</thead>
								<tbody>
					<?php 
						$aantal = 0;
						foreach($day['sub_afdelingen_list'] as $sub_afdelingen){ 
							$aantal = $aantal + $sub_afdelingen['te_produceren'];
							$subafdelingen = array_column($day['opmerkingen'], 'sub_afdeling');
					?>	
						<tr>
							<td>
							
								<!-- Modal opmerkingen -->
								<?php 
									if(isset($day['opmerkingen']) && !empty($day['opmerkingen']) && in_array($sub_afdelingen['sub_afdeling'],$subafdelingen)){	
										$modal_id = strip_tags($sub_afdelingen['sub_afdeling']);
										$modal_id = preg_replace("/[^A-Za-z0-9 ]/", '', $modal_id);
										$modal_id = str_replace(' ', '', $modal_id);
								?> 
								
								<!-- Button trigger modal opmerkingen -->
								<button type="button" class="btn btn-info btn-sm rounded-circle" style="padding: .0rem .24rem;" data-toggle="modal" data-target="#<?php echo $modal_id . $day['datum_klaar']; ?>">
									<i class="fas fa-info-circle"></i>
								</button>

								<div class="modal fade" id="<?php echo $modal_id . $day['datum_klaar']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
									<div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
										<div class="modal-content">
											<div class="modal-header">	
												<h5 class="modal-title" id="exampleModalScrollableTitle">Opmerkingen - <?php echo $sub_afdelingen['sub_afdeling']; ?></h5>
												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
													<span aria-hidden="true">&times;</span>
												</button>
											</div>
											<div class="modal-body">
												<div class="alert alert-primary" role="alert">
													<strong><?php echo $day['dag']; ?> - <?php echo $day['datum_klaar']; ?></strong>
												</div>
												<?php
												if(isset($day['opmerkingen'])){
													foreach($day['opmerkingen'] as $opmerking){ 
														if($opmerking['bon'] != "-" && $opmerking['bon'] != "" && $opmerking['sub_afdeling'] == $sub_afdelingen['sub_afdeling']){ ?>
														<div class="alert alert-success" role="alert">
															<?php 
																echo "<strong>Ordernr: </strong>" . $opmerking['ordernr'] . "<br />";
																echo "<strong>Artikelnr: </strong>" . $opmerking['artikelnr'] . "<br />";
																echo "<strong>Opmerking: </strong><br />" . $opmerking['bon'] . "<br />";
															?>
														</div>
														<?php }
													}
												}
												?>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-dismiss="modal">Sluiten</button>
											</div>
										</div>
									</div>
								</div>
								
								<?php } ?> <!-- END OF - Modal opmerkingen -->
								
							</td>
							<td>							
								<?php echo $sub_afdelingen['sub_afdeling']; ?> 			
							</td>
							<td class="text-right border-right"><?php echo $sub_afdelingen['te_produceren']; ?></td>
							<td class="text-right"><?php echo $sub_afdelingen['productie_uren']; ?></td>
						</tr>
					<?php } ?>
				</tbody>	
				<tfoot>
					<tr class="bg-light">
						<td></td>					
						<td><br />Totaal productie uren:</td>
						<td class="border-right"></td>
						<td class="text-right"><br /><strong><?php echo $day['aantal_uren']; ?></strong></td>
					</tr>
					<tr class="bg-light">	
						<td></td>
						<td>Totaal beschikbare uren:</td>
						<td class="border-right"></td>
						<td class="text-right" style="padding: 0 !important;"><strong><input class="text-right" id="uren" name="uren_<?php echo $day['dag']; ?>" type="text" value="<?php echo $uren_dag; ?>" maxlength="5" size="5" /></strong></td>
					</tr>
					<tr class="bg-light">	
						<td></td>
						<td>Totaal uren:</td>
						<td class="border-right"></td>
						<?php 
							$totaal_uren_week2 = $uren_dag - $day['aantal_uren'];
							if($totaal_uren_week2 > 0){
								echo '<td class="text-right bg-success text-white">';
							}elseif($totaal_uren_week2 == 0){
								echo '<td class="text-right bg-warning text-white">';
							}else{
								echo '<td class="text-right bg-danger text-white">';
							}
						?>
							<strong><?php echo $totaal_uren_week2; ?></strong>
							</td>
					</tr>
					<tr class="bg-light">
						<td></td>
						<td><strong>Nog te produceren <?php echo $day['dag']; ?>:</strong></td>
						<td class="text-right border-right"><strong><?php echo $aantal; ?></strong></td>
						<td></td>
					</tr>
					
				</tfoot>	
			</table>
		</div>
	<?php } ?>	
	<!-- END OF - Dag overzicht week 2 -->
	
	</form> <!-- END OF - AJAX form beschikbare uren -->
	
</div>
<!-- END OF - Show Week 2 overzicht -->
<br />
<!-- Show Week 3 overzicht-->
<div class="row collapse multi-collapse show m-0" id="multiCollapse_week3">
	<div class="col-12 m-0">
		<h5 class="d-inline-block m-0 mr-4"> <?php echo "Weeknummer: " . $_POST['week_nr_3']; ?> </h5>
		
		<!-- AJAX form beschikbare uren week 1-->
		<form class="d-inline-block" id="beschikbare_uren">
			<!-- Check writing rights -->
			<?php if($this->session->userdata('schrijven') == 1){ ?>
				<button class="btn btn-primary btn-sm" type="submit">Update uren week <?php echo $_POST['week_nr_3']; ?></button> 
			<?php } ?>		
			<?php $year = date('Y');?>
			<input id="year" name="year" type="hidden" value="<?php echo $year; ?>" />
			<input id="afdeling" name="afdeling" type="hidden" value="<?php echo $afdeling; ?>" />
		
	</div>
	
	<!-- Dag overzicht week 3 -->
	<?php foreach($sub_afdelingen_list_3 as $day){ ?>
	
		<!-- Set variables for AJAX Post -->
		<input id="id" name="id" type="hidden" value="<?php echo isset($uren_overzicht_3['id']) ? $uren_overzicht_3['id'] : ''; ?>" />
		<input id="week" name="week" type="hidden" value="<?php echo $_POST['week_nr_3']; ?>" />
		
		<?php 
			$dag_naam = $day['dag'];
			$uren_dag = isset($uren_overzicht_3['uren_' . $dag_naam]) ? $uren_overzicht_3['uren_' . $dag_naam] : ''; 
			if(empty($uren_dag)){
				$uren_dag = 0;
			}
		?>
	
		<div class="col-12 col-sm-6 col-xl p-1 pr-lg-3 pl-lg-3">
			<table class="table table-hover table-sm m-0">
				<thead class="thead bg-secondary text-white">
					<tr>
						<th></th>
						<th scope="col"><i class="fas fa-calendar-day"></i> <?php echo $day['dag']; ?>  <small><?php echo $day['datum_klaar']; ?></small></th>
						<th scope="col" class="text-right"><i class="fas fa-tally"></i></th>
						<th scope="col" class="text-right"><i class="fas fa-user-clock"></i></th>		
					</tr>
				</thead>
				<tbody>
					<?php 
						$aantal = 0;
						foreach($day['sub_afdelingen_list'] as $sub_afdelingen){ 
							$aantal = $aantal + $sub_afdelingen['te_produceren'];
							$subafdelingen = array_column($day['opmerkingen'], 'sub_afdeling');
					?>	
						<tr>
							<td>
							
								<!-- Modal opmerkingen -->
								<?php 
									if(isset($day['opmerkingen']) && !empty($day['opmerkingen']) && in_array($sub_afdelingen['sub_afdeling'],$subafdelingen)){	
										$modal_id = strip_tags($sub_afdelingen['sub_afdeling']);
										$modal_id = preg_replace("/[^A-Za-z0-9 ]/", '', $modal_id);
										$modal_id = str_replace(' ', '', $modal_id);
								?> 
								
								<!-- Button trigger modal opmerkingen -->
								<button type="button" class="btn btn-info btn-sm rounded-circle" style="padding: .0rem .24rem;" data-toggle="modal" data-target="#<?php echo $modal_id . $day['datum_klaar']; ?>">
									<i class="fas fa-info-circle"></i>
								</button>

								<div class="modal fade" id="<?php echo $modal_id . $day['datum_klaar']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
									<div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
										<div class="modal-content">
											<div class="modal-header">	
												<h5 class="modal-title" id="exampleModalScrollableTitle">Opmerkingen - <?php echo $sub_afdelingen['sub_afdeling']; ?></h5>
												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
													<span aria-hidden="true">&times;</span>
												</button>
											</div>
											<div class="modal-body">
												<div class="alert alert-primary" role="alert">
													<strong><?php echo $day['dag']; ?> - <?php echo $day['datum_klaar']; ?></strong>
												</div>
												<?php
												if(isset($day['opmerkingen'])){
													foreach($day['opmerkingen'] as $opmerking){ 
														if($opmerking['bon'] != "-" && $opmerking['bon'] != "" && $opmerking['sub_afdeling'] == $sub_afdelingen['sub_afdeling']){ ?>
														<div class="alert alert-success" role="alert">
															<?php 
																echo "<strong>Ordernr: </strong>" . $opmerking['ordernr'] . "<br />";
																echo "<strong>Artikelnr: </strong>" . $opmerking['artikelnr'] . "<br />";
																echo "<strong>Opmerking: </strong><br />" . $opmerking['bon'] . "<br />";
															?>
														</div>
														<?php }
													}
												}
												?>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-dismiss="modal">Sluiten</button>
											</div>
										</div>
									</div>
								</div>
								
								<?php }?><!-- END OF - Modal opmerkingen -->
								
							</td>
							<td>							
								<?php echo $sub_afdelingen['sub_afdeling']; ?> 			
							</td>
							<td class="text-right border-right"><?php echo $sub_afdelingen['te_produceren']; ?></td>
							<td class="text-right"><?php echo $sub_afdelingen['productie_uren']; ?></td>
						</tr>
					<?php } ?>
				</tbody>	
				<tfoot>
					<tr class="bg-light">
						<td></td>
						<td><br />Totaal productie uren:</td>
						<td class="border-right"></td>
						<td class="text-right"><br /><strong><?php echo $day['aantal_uren']; ?></strong></td>
					</tr>
					<tr class="bg-light">
						<td></td>
						<td>Totaal beschikbare uren:</td>
						<td class="border-right"></td>
						<td class="text-right" style="padding: 0 !important;"><strong><input class="text-right" id="uren" name="uren_<?php echo $day['dag']; ?>" type="text" value="<?php echo $uren_dag; ?>" maxlength="5" size="5" /></strong></td>
					</tr>
					<tr class="bg-light">
						<td></td>
						<td>Totaal uren:</td>
						<td class="border-right"></td>
						<?php 
							$totaal_uren_week3 = $uren_dag - $day['aantal_uren'];
							if($totaal_uren_week3 > 0){
								echo '<td class="text-right bg-success text-white">';
							}elseif($totaal_uren_week3 == 0){
								echo '<td class="text-right bg-warning text-white">';
							}else{
								echo '<td class="text-right bg-danger text-white">';
							}
						?>
							<strong><?php echo $totaal_uren_week3; ?></strong>
							</td>
					</tr>
					<tr class="bg-light">
						<td></td>
						<td><strong>Nog te produceren <?php echo $day['dag']; ?>:</strong></td>
						<td class="text-right border-right"><strong><?php echo $aantal; ?></strong></td>
						<td></td>
					</tr>
				</tfoot>	
			</table>
		</div>
	<?php } ?>	
	<!-- END OF - Dag overzicht week 3 -->
	
	</form> <!-- END OF - AJAX form beschikbare uren -->
	
</div>
<!-- END OF - Show Week 3 overzicht -->
<br />
<!-- Show Week 4 overzicht-->
<div class="row collapse multi-collapse show m-0" id="multiCollapse_week4">
	<div class="col-12 m-0">
		<h5 class="d-inline-block m-0 mr-4"> <?php echo "Weeknummer: " . $_POST['week_nr_4']; ?> </h5>
		
		<!-- AJAX form beschikbare uren week 1-->
		<form class="d-inline-block" id="beschikbare_uren">
			<!-- Check writing rights -->
			<?php if($this->session->userdata('schrijven') == 1){ ?>
				<button class="btn btn-primary btn-sm" type="submit">Update uren week <?php echo $_POST['week_nr_4']; ?></button> 
			<?php } ?>	
			<?php $year = date('Y');?>
			<input id="year" name="year" type="hidden" value="<?php echo $year; ?>" />
			<input id="afdeling" name="afdeling" type="hidden" value="<?php echo $afdeling; ?>" />
		
	</div>
	
	<!-- Dag overzicht week 4 -->
	<?php foreach($sub_afdelingen_list_4 as $day){ ?>
	
		<!-- Set variables for AJAX Post -->
		<input id="id" name="id" type="hidden" value="<?php echo isset($uren_overzicht_4['id']) ? $uren_overzicht_4['id'] : ''; ?>" />
		<input id="week" name="week" type="hidden" value="<?php echo $_POST['week_nr_4']; ?>" />
		<?php 
			$dag_naam = $day['dag'];
			$uren_dag = isset($uren_overzicht_4['uren_' . $dag_naam]) ? $uren_overzicht_4['uren_' . $dag_naam] : ''; 
			if(empty($uren_dag)){
				$uren_dag = 0;
			}
		?>
	
		<div class="col-12 col-sm-6 col-xl p-1 pr-lg-3 pl-lg-3">
			<table class="table table-hover table-sm m-0">
				<thead class="thead bg-secondary text-white">
					<tr>
						<th></th>
						<th scope="col"><i class="fas fa-calendar-day"></i> <?php echo $day['dag']; ?>  <small><?php echo $day['datum_klaar']; ?></small></th>
						<th scope="col" class="text-right"><i class="fas fa-tally"></i></th>
						<th scope="col" class="text-right"><i class="fas fa-user-clock"></i></th>		
					</tr>
				</thead>
				<tbody>
					<?php 
						$aantal = 0;
						foreach($day['sub_afdelingen_list'] as $sub_afdelingen){ 
							$aantal = $aantal + $sub_afdelingen['te_produceren'];
							$subafdelingen = array_column($day['opmerkingen'], 'sub_afdeling');
					?>	
						<tr><!-- TR Subafdeling info -->	
							<td><!-- TD - Opmerkingen -->
							
								<!-- Modal opmerkingen -->
								<?php if(isset($day['opmerkingen']) && !empty($day['opmerkingen']) && in_array($sub_afdelingen['sub_afdeling'],$subafdelingen)){	
									$modal_id = strip_tags($sub_afdelingen['sub_afdeling']);
									$modal_id = preg_replace("/[^A-Za-z0-9 ]/", '', $modal_id);
									$modal_id = str_replace(' ', '', $modal_id); ?> 
								
									<!-- Button trigger modal opmerkingen -->
									<button type="button" class="btn btn-info btn-sm rounded-circle" style="padding: .0rem .24rem;" data-toggle="modal" data-target="#<?php echo $modal_id . $day['datum_klaar']; ?>">
										<i class="fas fa-info-circle"></i>
									</button>
									
									<!-- Modal opmerkingen view-->
									<div class="modal fade" id="<?php echo $modal_id . $day['datum_klaar']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
									<div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
										<div class="modal-content">
											<div class="modal-header">	
												<h5 class="modal-title" id="exampleModalScrollableTitle">Opmerkingen - <?php echo $sub_afdelingen['sub_afdeling']; ?></h5>
												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
													<span aria-hidden="true">&times;</span>
												</button>
											</div>
											<div class="modal-body">
												<div class="alert alert-primary" role="alert">
													<strong><?php echo $day['dag']; ?> - <?php echo $day['datum_klaar']; ?></strong>
												</div>
												<?php
												if(isset($day['opmerkingen'])){
													foreach($day['opmerkingen'] as $opmerking){ 
														if($opmerking['bon'] != "-" && $opmerking['bon'] != "" && $opmerking['sub_afdeling'] == $sub_afdelingen['sub_afdeling']){ ?>
														<div class="alert alert-success" role="alert">
															<?php 
																echo "<strong>Ordernr: </strong>" . $opmerking['ordernr'] . "<br />";
																echo "<strong>Artikelnr: </strong>" . $opmerking['artikelnr'] . "<br />";
																echo "<strong>Opmerking: </strong><br />" . $opmerking['bon'] . "<br />";
															?>
														</div>
														<?php }
													}
												}
												?>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-dismiss="modal">Sluiten</button>
											</div>
										</div>
									</div>
								</div>
									<!-- END OF - Modal opmerkingen view-->
								
								<?php } ?>
								<!-- END OF - Modal opmerkingen -->
								
							</td>
							<td><!-- TD - Subafdeling -->							
								<?php echo $sub_afdelingen['sub_afdeling']; ?> 			
							</td>
							<td class="text-right border-right"><!-- TD - Aantalen -->
							<?php echo $sub_afdelingen['te_produceren']; ?>
							</td>	
							<td class="text-right"><!-- TD - Productie uren -->
								<?php echo $sub_afdelingen['productie_uren']; ?>
							</td>
						</tr>
					<?php } ?>
				</tbody>	
				<tfoot>
					<tr class="bg-light">	
						<td></td>
						<td><br />Totaal productie uren:</td>
						<td class="border-right"></td>
						<td class="text-right"><br /><strong><?php echo $day['aantal_uren']; ?></strong></td>
					</tr>
					<tr class="bg-light">
						<td></td>					
						<td>Totaal beschikbare uren:</td>
						<td class="border-right"></td>
						<td class="text-right" style="padding: 0 !important;"><strong><input class="text-right" id="uren" name="uren_<?php echo $day['dag']; ?>" type="text" value="<?php echo $uren_dag; ?>" maxlength="5" size="5" /></strong></td>
					</tr>
					<tr class="bg-light">	
						<td></td>
						<td>Totaal uren:</td>
						<td class="border-right"></td>
						<?php 
							$totaal_uren_week4 = $uren_dag - $day['aantal_uren'];
							if($totaal_uren_week4 > 0){
								echo '<td class="text-right bg-success text-white">';
							}elseif($totaal_uren_week4 == 0){
								echo '<td class="text-right bg-warning text-white">';
							}else{
								echo '<td class="text-right bg-danger text-white">';
							}
						?>
							<strong><?php echo $totaal_uren_week4; ?></strong>
							</td>
					</tr>
					<tr class="bg-light">
						<td></td>
						<td><strong>Nog te produceren <?php echo $day['dag']; ?>:</strong></td>
						<td class="text-right border-right"><strong><?php echo $aantal; ?></strong></td>
						<td></td>
					</tr>
				</tfoot>	
			</table>
		</div>
	<?php } ?>	
	<!-- END OF - Dag overzicht week 4 -->
	
	</form> <!-- END OF - AJAX form beschikbare uren -->
	
</div>
<!-- END OF - Show Week 4 overzicht -->

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
<!-- END OF - Order table row-->

<script>
$(document).ready(function(){
	//Scripts after ajax complete
	$( document ).ajaxComplete(function() {
		
		var table = $('#order_table').DataTable();

		function  myCallbackFunction(updatedCell, updatedRow, oldValue) {			
			$.ajax({
				url : "<?php echo site_url('Orders/update_order_row') ?>",
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
			"columns": [1,2,3,12,14],
			"allowNulls": {
				"columns": [1,2,3,12,14],
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
						<?php foreach($afdeling_list_all as $afdeling_putten){  ?>
							{ "value": "<?php echo $afdeling_putten['afdeling']; ?>", "display": "<?php echo $afdeling_putten['afdeling']; ?>" },
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
					"column":12, 
					"type":"text",
					"attr": {
						"type": "number"
					},
					"options":null 
				},
				{
					"column":14, 
					"type":"textarea", 
					"options":null 
				}
			]
		});
		<?php } ?>
		
		//AJAX Form beschikbare uren
		// Variable to hold request
		var request;
			
		// Bind to the submit event of our form
		$("#beschikbare_uren").submit(function(event){

			// Prevent default posting of form - put here to work in case of errors
			event.preventDefault();

			// Abort any pending request
			if (request) {
				request.abort();
			}
			// setup some local variables
			var $form = $(this);

			// Let's select and cache all the fields
			var $inputs = $form.find("input, select, button, textarea");

			// Serialize the data in the form
			var serializedData = $form.serialize();

			// Let's disable the inputs for the duration of the Ajax request.
			// Note: we disable elements AFTER the form data has been serialized.
			// Disabled form elements will not be serialized.
			$inputs.prop("disabled", true);

			// Fire off the request to /form.php
			request = $.ajax({
				url : "<?php echo site_url('Orders/update_uren') ?>",
				type: "post",
				data: serializedData
			});

			// Callback handler that will be called on success
			request.done(function (response, textStatus, jqXHR){
				// Log a message to the console
				console.log("Uren zijn succesvol geupdatet!");
				document.location.reload(true);
			});

			// Callback handler that will be called on failure
			request.fail(function (jqXHR, textStatus, errorThrown){
				// Log the error to the console
				console.error(
					"The following error occurred: "+
					textStatus, errorThrown
				);
			});

			// Callback handler that will be called regardless
			// if the request failed or succeeded
			request.always(function () {
				// Reenable the inputs
				$inputs.prop("disabled", false);
			});

		});
		
		function AjaxCallDagenOverzicht() {
			// nieuw ajax aanvraag voor bijwerken dagenoverzicht.
			$.ajax ({
				url : "<?php echo site_url("Orders/afdelingen_werklijst_putten/" . $_POST['week_nr']) ?>",
				type : 'GET',
				success: function(lijst) {
					// tekenen nieuw overzicht.		
					var alldata = JSON.parse (lijst);
					var tabellen = $(".table-sm");
					var totaalopdracht = 0;
					for (i = 0; i < 20; i++) {
						// matrix conversie.
						my = ~~(i/5); // niet vragen waarom ~~(var/5) (dit geeft blijkbaar een integer result)
						mx = ~~(i%5);		
						dagdata = alldata.sub_afdelingen_list[my];
						var aantal = tabellen[i].children[1].children.length;
						// tellers op 0
						for (j = 0; j < aantal; j++) {
							tabellen[i].children[1].children[j].children[2].innerText = "0";
						}
						totaalopdracht = 0;
						for (k = 0; k < dagdata[mx].sub_afdelingen_list.length; k++) {
							totaalopdracht += parseInt (dagdata[mx].sub_afdelingen_list[k].te_produceren);
							for (j = 0; j < aantal; j++) {
								if (dagdata[mx].sub_afdelingen_list[k].sub_afdeling == 
									tabellen[i].children[1].children[j].children[1].innerText) {
										tabellen[i].children[1].children[j].children[2].innerText = dagdata[mx].sub_afdelingen_list[k].te_produceren;
									}
							}										
						}
						tabellen[i].children[2].children[3].children[2].innerHTML = "<strong>" + totaalopdracht + "</strong>";
					}
					
				},
				error: function() {
					document.location.reload(true);
				}
			});
		}
		
		
		// ruim oude (+) knop op.
		$(".add_geproduceerd").remove();
		
		<!-- Check writing rights -->
		<?php if($this->session->userdata('schrijven') == 1){ ?>
		
		// maak (+) knop voor geproduceerd
		gNodes = table.column(12).nodes();
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
					cell.innerHTML = v[0][12];
					
					$.ajax({
						url : "<?php echo site_url('Orders/update_order_row') ?>",
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
				v[0][12] = v[0][11];	// zet geproduceerd op aantal.
				v[0][13] = 0;	// zet te_produceren op 0.
				//console.log(v);			
				t.rows(rowobj).data(v);
				$.ajax({
					url : "<?php echo site_url('Orders/update_order_row') ?>",
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
					url : "<?php echo site_url('Orders/update_order_row') ?>",
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
		"order": 				[[ 17, "asc" ],[ 11, "desc" ]], //Sorteer op leverdatum
		"columns": 				[										
									null,					// 0 Afvinken
									{ "visible": false },	// 1  Afdeling
									null,				 	// 2  Sub afdeling
									null, 					// 3  Status
									{ "visible": false },	// 4  Debiteurnr
									null,					// 5  Klant
									null, 					// 6  Ordernr.
									{ "visible": false },	// 7  Artikelnr.
									{ "visible": false }, 	// 8  Opbrgroep
									{ "visible": false }, 	// 9  Orderregel soort
									null, 					// 10 Product
									null, 					// 11 Aantal
									null,					// 12 Geproduceerd
									null,					// 13 Te produceren
									{ "visible": false },	// 14 Uren
									{ "visible": false },	// 15 Bon
									null, 					// 16 Datum klaar
									{ "visible": false },	// 17 Week klaar
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
										"afdeling_filter":		'<?php echo $afdeling; ?>'
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