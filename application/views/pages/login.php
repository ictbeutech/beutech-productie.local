<div class="row d-flex justify-content-center">
	<div class="col-11 col-md-8 col-lg-4 my-5">
		<form class="form-signin" action="<?php echo site_url('login/auth');?>" method="post">
			<h4>Inloggen</h4>
			<!-- Show Message if login is incorrect -->
			<?php if(!empty($this->session->flashdata('msg'))){?>
				<div class="alert alert-danger" role="alert">
					<?php echo $this->session->flashdata('msg');?>
				</div>
			<?php } ?>
			<div class="input-group mb-3">
				<label for="username" class="sr-only">Gebruikersnaam:</label>
				<div class="input-group-prepend">
					<span class="input-group-text" id="basic-addon1"><i class="fas fa-user"></i></span>
				</div>
				<input type="email" name="email" class="form-control" placeholder="E-mail" required autofocus>
			</div>
			<div class="input-group mb-3">
				<label for="password" class="sr-only">Wachtwoord</label>
				<div class="input-group-prepend">
					<span class="input-group-text" id="basic-addon1"><i class="fas fa-key"></i></span>
				</div>
				<input type="password" name="password" class="form-control" placeholder="Wachtwoord" required>
			</div>
			<div class="checkbox">
				<label>
				<input type="checkbox" value="remember-me"> Onthoud mij
				</label>
			</div>
			<button class="btn btn-sm btn-lg btn-primary btn-block" type="submit"><i class="fas fa-sign-in-alt"></i> Login</button>
		</form>
	</div>
</div>