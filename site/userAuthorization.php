<?php
	include_once('procedures.php');
    
    if (isActiveUser()) header("Location: /");
	
	$loginFailed = false;
	
	if (isset($_POST['submitAuth']))
	{
		$reason = "";
		$data = getAuthorizationData();
		//var_dump($data);
		if (!empty($data))
        {
            if (($data['group'] == 'banned') && ($data['password'] === md5(md5($_POST['password']))))
            {
                $userBanned = true;
            }
            else
            {
			    if ($data['password'] === md5(md5($_POST['password']))) 
			    {
				    $hash = md5(generateUniqueCode(10));
				    LogIn($hash, $data['id']);
				
				    echo '<meta http-equiv="refresh" content="0; url=userCheckData.php?redirect='.htmlspecialchars($_POST['redirect']).'">';
				    exit();
                }
                else 
			    {
				    //$reason = "Неправильный пароль!";
				    $loginFailed = true;
                }
            }
        }
        else 
		{
			//$reason = "Проблемы с БД!";
			$loginFailed = true;
		}
	}
?>

<?php include("top.php"); ?>
		
	<div class = "container content">
		<div class="titleName">Вход</div>
        
        <?php if ($loginFailed) { ?>
        <p style="color: red; text-decoration: bold; text-align: center">Неправильный логин или пароль</p>
        <?php
            }
        ?>
        <?php if ($userBanned) { ?>
        <p style="color: red; text-decoration: bold; text-align: center">Вы заблокированы администрацией</p>
        <?php
            }
        ?>
       
		<form class="form-signin" role="form" method="POST">
			<input type="login" class="form-control" name="login" placeholder="Login" />
			<input class="form-control" type="password" name="password" placeholder="Password" />
			<input type="hidden" name="redirect" value="<?php echo ($loginFailed) ? htmlspecialchars($_POST['redirect']) : htmlspecialchars($_SERVER['HTTP_REFERER']) ?>" />
			<br>
			<button type="submit" class="btn btn-lg btn-primary btn-block" name="submitAuth">Войти</button>
		</form>
	</div>
				
<?php include("bottom.php"); ?>
