<!-- Instellingen Pagina-->

<!-- Check user rights to view this page -->
<?php if ( $user_access === 1) { ?>

<!-- Page heading row -->	
<div class="row d-flex justify-content-center border border-dark bg-dark py-1 mt-0 mb-2">
	<div class="col my-2 text-center">
		<a class="btn btn-secondary btn-sm" href="/beutech_productie/instellingen/gebruikers" role="button"><i class="fas fa-users-cog"></i> Gebruikers</a>
	</div>
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