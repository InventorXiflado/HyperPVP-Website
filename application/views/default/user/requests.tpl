
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
	
		<h3>Your friend requests!</h3>

		<hr>
		

		<div class="row">
			<div class="span12">
			
							
			<table class="table table-striped">
				<tr>
					<td><strong>Username</strong></td>
					<td><strong>Head</strong></td>
					<td><strong></strong></td>
					<td><strong></strong></td>
				</tr>
				
				<?php 
				
				foreach (FriendDao::getFriendRequests(Session::auth()->username) as $friend) {

				$row = R::load('users_friendrequest', $friend['id']); 
				
				?> 
				
				
				<tr>
					<td><a href='{$site->url}/profile/<?php echo $row->from; ?>'><?php echo $row->from; ?></a></td>
					<td><a href='{$site->url}/profile/<?php echo $row->from; ?>' rel='tooltip' style='display: inline-block;' title='<?php echo $row->from; ?>'><img class='avatar' src='https://minotar.net/helm/<?php echo $row->from; ?>/32.png' player='<?php echo $row->from; ?>' style='width: 32px; height: 32px; margin-bottom: 2px; margin-left: 2px; margin-right: 2px;'/></a>	</td>
					<td><a href="{$site->url}/friend/request/accept?id=<?php echo $friend['id']; ?>"><button class="btn btn-success" type="button">Accept</button></a></td>
					<td><a href="{$site->url}/friend/request/deny?id=<?php echo $friend['id']; ?>"><button class="btn btn-warning" type="button">Deny</button></a></td>
				</tr>
				
				<?php } ?>
			</table>


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
