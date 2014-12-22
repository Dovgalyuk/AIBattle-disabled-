<?php
	
	include_once('procedures.php');
	$link = getDBConnection();
	if (mysqli_select_db($link, getDBName()))
	{
		$fileId = intval($_GET['file']);
		$mysf = mysqli_query($link, "SELECT originalName FROM attachments WHERE id = ".$fileId);
		if (mysqli_num_rows($mysf) > 0)
		{
			$newName = mysqli_result($mysf, 0);
		
			$file = addslashes("./attachments/".$_GET['file']);
		
			header ("Content-Type: application/octet-stream");
			header ("Accept-Ranges: bytes");
			header ("Content-Disposition: attachment; filename=$newName"); 
			header ("Content-Length: ".filesize($file)); 
		
			readfile($file);
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0; url=index.php">';
		}
	}
?>