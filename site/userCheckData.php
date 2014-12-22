<?php
	include_once('procedures.php');
	
	$link = getDBConnection();
	$reason = "";
	$redirect = $_GET['redirect'];
	$successLogin = false;
	
	if (!isset($redirect) || $redirect == '') $redirect = "index.php";
	
	if (mysqli_select_db($link, getDBName()))
	{	
		if (isset($_SESSION['SBUserid']) and isset($_SESSION['SBUserhash']))
		{
			$query = mysqli_query($link, "SELECT * FROM users WHERE id = '".$_SESSION['SBUserid']."' LIMIT 1");
			$data = mysqli_fetch_assoc($query);
			
			if (($data['hash'] !== $_SESSION['SBUserhash']) or (intval($data['id']) !== $_SESSION['SBUserid']))
			{
				setcookie("SBUserid", "", time() - 3600*24*30*12, "/");
				setcookie("SBUserhash", "", time() - 3600*24*30*12, "/");
				$reason = "Странные вещи произошли - cookies не сохранились!";
			}
			else $successLogin = true;
		}
		else 
            $reason = "Cookies отсуствуют!";
	} else 
        $reason = "Нет возможности подключиться к БД!";
	
	if (isActiveUser()) 
	{
	
	if (!$successLogin) 
            header("Location: redirectIndex.php?reason=".$reason);
		else 
            header("Location: ".htmlspecialchars($redirect));
        exit();
	}
	else
	{
        header("Location: userAuthorization.php");
        exit();
	}	
?>