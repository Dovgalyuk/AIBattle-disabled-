<?php
	include_once('procedures.php');
	
	$id = intval($_GET['id']);
	
	if ($file = @file_get_contents("./testers/".$id))
	{
		$file = "<code>" . nl2br(str_replace(" ", "&nbsp;", 
				str_replace("\t", "    ", htmlspecialchars($file)))) . "</code>";
		echo $file;
	}
?>