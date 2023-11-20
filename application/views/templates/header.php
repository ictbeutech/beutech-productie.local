<!doctype html>
<html lang="en">
	<head>
		<!--
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
		<script src="http://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.js"></script>
		<script>
			//paste this code under the head tag or in a separate js file.
			// Wait for window load
			$(window).load(function() {
				// Animate loader off screen
				$(".se-pre-con").fadeOut("slow");;
			});
		</script>	
		-->
		
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<link rel="shortcut icon" href="/assets/images/favicon.ico" type="image/x-icon">
		<link rel="icon" href="/assets/images/favicon.ico" type="image/x-icon">
		
		
		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="/assets/css/bootstrap.min.css">

		<!-- Datatables css -->
		<link rel="stylesheet" type="text/css" href="/assets/DataTables/datatables.min.css"/>
		
		<!--<link rel="stylesheet" type="text/css" href="/assets/css/editor.bootstrap.min.css"/>-->
		
		
		
		<!-- jQuery UI css -->
		<link rel="stylesheet" type="text/css" href="/assets/jquery-ui-1.12.1/jquery-ui.css"/>
		 
		<!-- Custom CSS -->
		<link rel="stylesheet" href="/assets/css/style.css">

		<!-- Font Awesome -->
		<script defer src="/assets/fontawesome-pro/js/all.js"></script>
		
		<!-- Javascripts -->
		<!-- jQuery first, then Popper.js, then Bootstrap JS  -->
		<script type="application/javascript" src="/assets/js/jquery-3.4.1.min.js"></script>
		<script type="application/javascript" src="/assets/js/popper.min.js"></script>
		<script type="application/javascript" src="/assets/js/bootstrap.min.js"></script>
		
		<!-- Datatables JS -->
		<script type="text/javascript" src="/assets/DataTables/datatables.min.js"></script>
		<script type="text/javascript" src="/assets/DataTables/dataTables.cellEdit.js"></script>
		
		<!--<script type="text/javascript" src="/assets/js/dataTables.editor.min.js"></script>-->
		
		<!-- jQuery UI JS -->
		<script type="text/javascript" src="/assets/jquery-ui-1.12.1/jquery-ui.js"></script>
		
			
		<title>Beutech - Productie Planning</title>
	</head>
	<body>
	<?php 
		if($this->session->userdata('gebuikersnaam') == "Tom Maandag"){
		//echo "<pre>";
		//	print_r($this->session->userdata);
		//echo "</pre>";
		}
	?>
	<div class="se-pre-con"></div>
		<!-- BEGIN Navbar -->
		<nav class="navbar navbar-expand-xl navbar-dark bg-secondary text-white"> 
			<?php if(!empty($this->session->userdata('gebuikersnaam'))){ ?>
				<a class="navbar-brand" href="/dashboard">
					<img src="/assets/images/beutech-logo.png" width="246" height="59" class="d-inline-block align-top" alt="Beutech Logo">
				</a>
			
				<button class="navbar-toggler mr-4" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>

				<div class="collapse navbar-collapse" id="navbarSupportedContent">
					<ul class="navbar-nav mr-auto">
						<li class="nav-item px-3 active">
							<h5><a class="nav-link" href="/dashboard"><i class="fas fa-home-lg-alt"></i> Dashboard <span class="sr-only">(current)</span></a></h5>
						</li>
						<?php if ((in_array("Orderoverzicht", $this->session->userdata('afdelingen'))) || (in_array("Admin", $this->session->userdata('afdelingen')))) { ?>
							<li class="nav-item px-3">
								<h5><a class="nav-link" href="/orders"><i class="fas fa-th-list"></i> Orderoverzicht</a></h5>
							</li>
						<?php } ?>
						<li class="nav-item dropdown px-3">
							<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><h5><i class="fas fa-th"></i> Afdelingen</h5></a>
							<div class="dropdown-menu bg-light">
								<!--
								<a class="dropdown-item" href="#">Afdelingen overzicht</a>
								<div class="dropdown-divider"></div>
								-->
								<?php if ((in_array("Doorvoerbochten", $this->session->userdata('afdelingen'))) || (in_array("Admin", $this->session->userdata('afdelingen')))) { ?>
									<a class="dropdown-item" href="/orders/doorvoerbochten"><i class="fas fa-wave-sine"></i> Doorvoerbochten</a>
									<hr />
								<?php } ?>
								<?php if ((in_array("PE", $this->session->userdata('afdelingen'))) || (in_array("Admin", $this->session->userdata('afdelingen')))) { ?>
									<a class="dropdown-item" href="/orders/pe"><i class="fas fa-circle"></i> PE</a>
									<hr />
								<?php } ?>	
								<?php if ((in_array("Putten", $this->session->userdata('afdelingen'))) || (in_array("Admin", $this->session->userdata('afdelingen')))) { ?>
									<a class="dropdown-item" href="/orders/putten"><i class="fas fa-filter"></i> Putten</a>
									<hr />
								<?php } ?>	
								<?php if ((in_array("Montage", $this->session->userdata('afdelingen'))) || (in_array("Admin", $this->session->userdata('afdelingen')))) { ?>
									<a class="dropdown-item" href="/orders/montage"><i class="fas fa-tools"></i> Montage</a>
									<hr />
								<?php } ?>
								<?php if ((in_array("Draaibank", $this->session->userdata('afdelingen'))) || (in_array("Admin", $this->session->userdata('afdelingen')))) { ?>
									<a class="dropdown-item" href="/orders/draaibank"><i class="fas fa-circle-notch"></i> Draaibank</a>
									<hr />
								<?php } ?>
								<?php if ((in_array("Smans", $this->session->userdata('afdelingen'))) || (in_array("Admin", $this->session->userdata('afdelingen')))) { ?>
									<a class="dropdown-item" href="/orders/handvorm"><i class="fas fa-hand-paper"></i> Smans</a>
									<hr />
								<?php } ?>
								<?php if ((in_array("Handvorm", $this->session->userdata('afdelingen'))) || (in_array("Admin", $this->session->userdata('afdelingen')))) { ?>
									<a class="dropdown-item" href="/orders/handvorm_2"><i class="fas fa-hand-paper"></i> Handvorm</a>
									<hr />
								<?php } ?>
								<?php if ((in_array("Extrusie", $this->session->userdata('afdelingen'))) || (in_array("Admin", $this->session->userdata('afdelingen')))) { ?>
									<a class="dropdown-item" href="/orders/extrusie"><i class="fas fa-compress-arrows-alt"></i> Extrusie</a>
									<hr />
								<?php } ?>
								<?php if ((in_array("Logistiek", $this->session->userdata('afdelingen'))) || (in_array("Admin", $this->session->userdata('afdelingen')))) { ?>
									<a class="dropdown-item" href="/orders/logistiek"><i class="fas fa-truck"></i> Logistiek</a>
									<hr />
								<?php } ?>
								
							</div>
						</li>
						<?php if ((in_array("Instellingen", $this->session->userdata('afdelingen'))) || (in_array("Admin", $this->session->userdata('afdelingen')))) { ?>
							
							<li class="nav-item dropdown px-3">
							<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="/instellingen" role="button" aria-haspopup="true" aria-expanded="false"><h5><i class="fas fa-cogs"></i> Instellingen</h5></a>
							<div class="dropdown-menu bg-light">
								<!--
								<a class="dropdown-item" href="#">Afdelingen overzicht</a>
								<div class="dropdown-divider"></div>
								-->
								<?php if ((in_array("Instellingen", $this->session->userdata('afdelingen'))) || (in_array("Admin", $this->session->userdata('afdelingen')))) { ?>
									<a class="dropdown-item" href="/instellingen"><i class="fas fa-cogs"></i> Instellingen overzicht</a>
									<hr />
								<?php } ?>
								<?php if ((in_array("Gebruikers", $this->session->userdata('afdelingen'))) || (in_array("Admin", $this->session->userdata('afdelingen')))) { ?>
									<a class="dropdown-item" href="/instellingen/gebruikers"><i class="fas fa-users-cog"></i> Gebruikers</a>
									<hr />
								<?php } ?>
								<?php if ((in_array("Recepten", $this->session->userdata('afdelingen'))) || (in_array("Admin", $this->session->userdata('afdelingen')))) { ?>
									<a class="dropdown-item" href="/recepten"><i class="fas fa-th-list"></i> Recepten</a>
									<hr />
								<?php } ?>
								<?php if ((in_array("Voorraad", $this->session->userdata('afdelingen'))) || (in_array("Admin", $this->session->userdata('afdelingen')))) { ?>
									<a class="dropdown-item" href="/voorraad"><i class="fas fa-th-list"></i> Voorraad</a>
									<hr />
								<?php } ?>
								<?php if ((in_array("Productie", $this->session->userdata('afdelingen'))) || (in_array("Admin", $this->session->userdata('afdelingen')))) { ?>
									<a class="dropdown-item" href="/productie"><i class="fas fa-th-list"></i> Productie mutaties</a>
									<hr />
								<?php } ?>
							</div>
						</li>
							
						<?php } ?>
					</ul>
				</div>
				<div class="col bg-info text-white text-left border border-info rounded">
					<?php 
						$date = new DateTime();
						$day = $date->format("l");
						if($day == "Monday"){$day = "maandag";}
						elseif($day == "Tuesday"){$day = "dinsdag";}
						elseif($day == "Wednesday"){$day = "woensdag";}
						elseif($day == "Thursday"){$day = "donderdag";}
						elseif($day == "Friday"){$day = "vrijdag";}
						elseif($day == "Saterday"){$day = "zaterdag";}
						elseif($day == "Sunday"){$day = "zondag";}
					?>
					<p class="m-0">Week: &nbsp;&nbsp;&nbsp;<strong><?php echo date('W'); ?></strong></p>
					<p class="m-0">Dag: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $day; ?></strong></p>
					<p class="m-0">Datum: &nbsp;<strong><?php echo date('d-m-y'); ?></strong></p>
				</div>
				<div class="col text-right">
					<p>Ingelogd: <button class="btn btn-success btn-sm" aria-disabled="true" disabled><i class="fas fa-user"></i> <?php echo $this->session->userdata('gebuikersnaam') . " (" .$this->session->userdata('level') . ") ";?></button></p>
					<a class="btn btn-secondary  btn-sm" href="<?php echo site_url('login/logout');?>" role="button"><i class="fas fa-sign-out-alt"></i> Uitloggen</a>
				</div>
				
			<?php } else{ ?>
				<a class="navbar-brand" href="/login">
					<img src="/assets/images/beutech-logo.png" width="246" height="59" class="d-inline-block align-top" alt="Beutech Logo">
				</a>
				<a class="btn btn-primary btn-sm" href="login" role="button"><i class="fas fa-sign-in-alt"></i> Inloggen</a>
			<?php } ?>
		</nav> <!-- END OF Navbar -->
		
		<!-- BEGIN Main container -->
		<div class="container-fluid pb-3"> 
			<div class="row bg-secondary text-light">
				<div class="col text-center">
					<h2 class="m-0"><?php echo $title; ?></h2>
				</div>
			</div>

			<!-- Show message row -->
			<?php if($this->session->flashdata('msg_success')){ ?>
				<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="false">
					<div class="modal-dialog modal-dialog-centered" role="document">
						<div class="modal-content border-2 border-success">
							<div class="modal-header bg-success text-white border-0">
								<h5 class="modal-title mr-auto" id="exampleModalCenterTitle">Gelukt:</h5>
								<small class="text-white"><?php echo date('H:i:s'); ?></small>
								<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body bg-light">
								<?php echo $this->session->flashdata('msg_success'); ?>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>
			<?php if($this->session->flashdata('msg_error')){ ?>
				<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="false">
					<div class="modal-dialog modal-dialog-centered" role="document">
						<div class="modal-content border-0">
							<div class="modal-header bg-danger text-white border-0">
								<h5 class="modal-title" id="exampleModalCenterTitle">Er ging iets mis:</h5>
								<small class="text-white ml-4"><?php echo date('H:i:s'); ?></small>
								<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body bg-light">
								<?php echo $this->session->flashdata('msg_error'); ?>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>
			<!-- END OF Message row -->			