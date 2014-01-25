
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>PaintballPVP - Top Ranks</title>
	
	<?php $this->inc("base/style"); ?>
	
  </head>
  <body>
  
    <div class="container">

		<?php
			$this->inc("base/links");
		?>
	
		<hr>
	
		<h3>Register</h3>

		<hr>
		

		<div class="row">
			<div class="span12">
			
			<h4>Enter your secret code.</h4>
			<br>
			<p>When you go on a Paintball PVP server, type the command <strong>/register [email] [password]</strong> and you should get a secret 5 code pin. Enter that in the box below and you will be able to make an account.</p>
			<br>
			<form action="register/redeem" method="post">
				<div class="control-group">
					<div class="controls">
						<input type="text" name="pin" placeholder="PIN">
					</div>
				</div>
				<div class="control-group">
					<div class="controls">
						<button type="submit" class="btn">Redeem</button>
					</div>
				</div>
			</form>

			</div>
		</div>
	
		<hr>
	  
		<?php
		$this->inc("base/footer");
		?>
	  
    </div> <!-- /container -->
	
	<?php
	$this->inc("base/javascript");
	?>

  </body>
</html>
