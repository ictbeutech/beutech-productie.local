<!-- Instellingen gebruikers Pagina-->

<!-- Check user rights to view this page -->
<?php if ( $user_access === 1) { ?>

<!-- Page heading row -->
<div class="row d-flex justify-content-center border bg-light py-1 mt-0 mb-2">
	<div class="col my-3 text-center">
		<a class="btn btn-primary btn-sm" href="/instellingen" role="button"><i class="fas fa-arrow-left"></i> Terug naar instellingen</a>
	</div>
</div>
<!-- END OF - Page heading row -->

<!-- Afdelingen table -->
<div class="row">
	<div class="col">
		<table id="afdeling_table" class="table table-sm table-bordered table-hover table-striped" width="100%">
			<thead>
				<tr>
					<th class="bg-light">Gebruikersnaam</th>
					<th class="bg-light">Afdeling</th>
					<th class="bg-light">Rechten (pagina's/afdelingen)</th>
					<th class="bg-light">Rechten (Schrijven)</th>
					<th class="bg-light">E-mail</th>
					<th class="bg-light">Wachtwoord</th>
					<th>Opslaan/verwijderen</th>
				</tr>
			</thead>
			<tbody>
				<!-- Nieuwe gebruiker toevoegen -->
				<form method="POST">
					<tr class="table-info">
						<td><input name="gebruiker_naam" class="form-control" type="text" value="" placeholder="Nieuwe gebruiker" required></td>
						<td><input name="gebruiker_level" class="form-control" type="text" value="" placeholder="Selecteer afdeling.." required></td>
						<td>
							<?php foreach ($afdelingen as $afdeling){ ?>
								<div class="form-check">
									<input name="afdeling_<?php echo $afdeling; ?>" class="form-check-input" type="checkbox" id="inlineCheckbox" value="<?php echo $afdeling; ?>" >
									<label class="form-check-label" for="inlineCheckbox">
										<?php echo $afdeling; ?>
										<?php if($afdeling == "Admin"){ echo "(alle rechten)"; } ?>
									</label>
								</div>
							<?php } ?>
						</td>
						<td>
							<div class="form-check">
								<input name="schrijfrechten" class="form-check-input" type="checkbox" id="inlineCheckbox" value="1">
								<label class="form-check-label" for="inlineCheckbox">
									Schrijfrechten
								</label>
							</div>
						</td>
						<td><input name="gebruiker_email" type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Voer e-mail in" value="" required></td>
						<td><input name="gebruiker_password" class="form-control" type="text" placeholder="Voer gewenste wachtwoord in" required></td>
						
						<?php if($this->session->userdata('schrijven') == 1){ ?>
							<td><button name="gebruiker_toevoegen" type="submit" class="btn btn-primary" value="submit"><i class="fas fa-save"></i></button></td>
						<?php } ?>	
					</tr>
				</form>
				
				<!-- Bestaande gebruiker updaten -->
				<?php foreach($gebruikers as $gebruiker){?>
					<form method="POST">
						<input name="gebruiker_id" class="form-control" type="hidden" value="<?php echo $gebruiker->gebruiker_id; ?>">
						<tr>
							<td><input name="gebruiker_naam" class="form-control" type="text" value="<?php echo $gebruiker->gebruiker_naam; ?>" required></td>
							<td><input name="gebruiker_level" class="form-control" type="text" value="<?php echo $gebruiker->gebruiker_level; ?>" required></td>
							<td>
								<?php 
									$afdelingen_gebruiker = explode(";", $gebruiker->gebruiker_afdelingen); 
									$afdelingen_gebruiker_planningsrechten = array();
									if(isset($gebruiker->gebruiker_afdelingen_planningsrechten) && !empty($gebruiker->gebruiker_afdelingen_planningsrechten) ){
										$afdelingen_gebruiker_planningsrechten = explode(";", $gebruiker->gebruiker_afdelingen_planningsrechten);
									}
																	
									foreach ($afdelingen as $afdeling){
										if (in_array($afdeling, $afdelingen_gebruiker)) {
											$checked_afdelingen = "checked";
										}else{
											$checked_afdelingen = "";
										}
										$check_planningsrechten = strtolower($afdeling) . "_planningsrechten";
										if (in_array($check_planningsrechten, $afdelingen_gebruiker_planningsrechten)) {
											$checked_afdelingen_planningsrechten = "checked";											
										}else{
											$checked_afdelingen_planningsrechten = "";											
										}																										
										?>

										<?php
											$check_lock_rights = "";
											if($afdeling == "Doorvoerbochten" || $afdeling == "PE" || $afdeling == "Putten" || $afdeling == "Montage"){
												$check_lock_rights = '
													<br>
													<input style="margin-left: 1px;" name="afdeling_' .$afdeling. '_planningsrechten" class="form-check-input" type="checkbox" id="inlineCheckbox" value="' .strtolower($afdeling). '_planningsrechten" '.$checked_afdelingen_planningsrechten.' >
													<label style="margin-left: 20px;" class="form-check-label" for="inlineCheckbox">
														Planningsrechten
													</label>
												';
											}
										?>
										
										<div class="form-check">
											
											<input name="afdeling_<?php echo $afdeling; ?>" class="form-check-input" type="checkbox" id="inlineCheckbox" value="<?php echo $afdeling; ?>" <?php echo $checked_afdelingen; ?> >
											<label class="form-check-label" for="inlineCheckbox">
												<?php echo $afdeling; ?>
												<?php if($afdeling == "Admin"){ echo "(alle rechten)"; } ?>
											</label>										
											<?php echo $check_lock_rights; ?>
										</div>
								<?php } ?>
								
							</td>
							<td>
								<div class="form-check">
									<?php 
										if ($gebruiker->gebruiker_schrijfrechten == 1) {
											$checked_schrijfrechten = "checked";
										}else{
											$checked_schrijfrechten = "";
										}
									?>
									<input name="schrijfrechten" class="form-check-input" type="checkbox" id="inlineCheckbox" value="1" <?php echo $checked_schrijfrechten; ?>>
									<label class="form-check-label" for="inlineCheckbox">
										Schrijfrechten
									</label>
								</div>
							</td>
							<td><input name="gebruiker_email" type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email" value="<?php echo $gebruiker->gebruiker_email; ?>" required></td>
							<td><input name="gebruiker_password" class="form-control" type="text" placeholder="Type nieuw wachtwoord of laat leeg"></td>
							<td>
								<?php if($this->session->userdata('schrijven') == 1){ ?>
									<button name="gebruiker_wijzigen" type="submit" class="btn btn-primary" value="submit"><i class="fas fa-save"></i></button>
									<button name="gebruiker_verwijderen" type="submit" class="btn btn-danger" value="submit"><i class="fas fa-times"></i></button>
								<?php } ?>
							</td>
						</tr>
					</form>
				<?php } ?>
				
			</tbody>
		</table>
		
	</div>
</div>
<!-- END OF - Afdelingen table -->


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