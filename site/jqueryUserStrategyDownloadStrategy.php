<?php
	include_once('procedures.php');
		
	$id = intval($_POST['gameId']);
	
	$link = getDBConnection();
	if (mysqli_select_db($link, getDBName()))
	{
		$fileInputName = $_POST['formInputName'];
		$tournamentId = intval($_POST['tournamentId']);
		$extension = mysqli_real_escape_string($link, getFileExtension($fileInputName));
	
		mysqli_query($link, "INSERT INTO strategies SET user = ".intval(getActiveUserID()).", game = ".$id.", language = '".$extension."', tournament = ".$tournamentId);
		$strategy = mysqli_insert_id($link);

		saveFileOnDisc2(addslashes("./executions/").$strategy, $fileInputName);
	    
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')    
            $compileArr = array('cpp' => 'cl.bat', 'c' => 'cl.bat', 'vb' => 'vbcl.bat', 'pas' => 'fpc.bat');
        else
            $compileArr = array('cpp' => './gcc.sh', 'c' => './gcc.sh', 'py' => './python.sh');
		
		$output = array();
		$execValue = 0;
		
		if ($compileArr[$extension])
			exec($compileArr[$extension]." $strategy", $output, $execValue);
		else 
			$execValue = 1; 
		
		if ($execValue != 0)
			mysqli_query($link, "UPDATE strategies SET status = 'CE' WHERE id = ".$strategy." AND user = ".getActiveUserID()." AND tournament = ".$tournamentId);
		else 
			setActStatus($strategy, $id, $tournamentId);
	}

?>
