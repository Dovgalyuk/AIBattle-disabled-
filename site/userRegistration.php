<?php 
	include_once('procedures.php');

	if (isset($_POST['submitReg']))
	{
		$reason = registerUser($_POST['login'], $_POST['password']);
		echo '<meta http-equiv="refresh" content="0; url=redirectIndex.php?reason='.$reason.'">';
		exit();
	}
?>

<?php include("top.php"); ?>
		
	<div class = "container content">
		<div class="titleName">Регистрация</div>
		<form class="form-signin" role="form" method="POST">
			<input type="login" class="form-control" name="login" placeholder="Login">
			<input class="form-control" type="password" name="password" required="" placeholder="Password"></input>
			<br>
			<button type="submit" class="btn btn-lg btn-primary btn-block" name="submitReg">Зарегистрироваться</button>
		</form>
	</div>
				

<?php include("bottom.php"); ?>