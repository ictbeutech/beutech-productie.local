<!-- Voorraad Pagina-->

<!-- Check user rights to view this page -->
<?php if ( $user_access === 1) { ?>

<!-- Page heading row -->
<div class="row d-flex justify-content-center border border-dark bg-dark py-1 mt-0 mb-2">
	
	<!-- Check writing rights -->
	<?php if($this->session->userdata('schrijven') == 1){ ?>
		<div class="col my-2 text-center">
			<form action="" method="POST">		
				<button type="submit" class="btn btn-secondary btn-sm" name="submit-sync-voorraad-beutech">
					<i class="fas fa-sync-alt"></i> Synchroniseer Voorraad Beutech
				</button>
			</form>
		</div>
		<div class="col my-2 text-center">
			<a class="btn btn-secondary btn-sm" href="/voorraad/alles" role="button">
				<i class='fas fa-eye'></i> Bekijk voorraad Beutech
			</a>
		</div>
	<?php } ?>
	
</div>
<!-- END OF - Page heading row -->
	
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