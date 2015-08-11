<?php
	include_once('procedures.php');
	$link = getDBConnection();
	if (mysqli_select_db($link, getDBName()))
	{
		$user = intval(getActiveUserID());
        $id = intval($_GET['id']);
        $query = "SELECT id FROM strategies WHERE (id = $id)";
        if (!isAdmin())
            $query .= " AND (user = $user)";

        if (mysqli_num_rows(mysqli_query($query)) > 0)
        {
			header ("Content-Type: text/plain");
			$file = @file_get_contents("./compilelogs/$id.txt");
            echo $file;
        }
	}
?>
