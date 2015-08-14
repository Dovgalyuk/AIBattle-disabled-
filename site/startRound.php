<?php
    include_once('procedures.php');
    if (isAdmin())
    {
        $roundId = intval($roundId);
        $ids = getRoundPlayers($roundId);
        
        $link = getDBConnection();
        if (mysqli_select_db($link, getDBName()))
        {
            mysqli_query($link, "UPDATE rounds SET date = now() WHERE id = $roundId"); // выставляем дату раунда
            
            foreach ($ids as $id)
                mysqli_query($link, "INSERT INTO scores SET round = $roundId, strategy = " . $id . ", score = 0");
                
            foreach ($ids as $id1)
            {
                foreach ($ids as $id2)
                {
                    if ($id1 != $id2)
                        mysqli_query($link, "INSERT INTO duels SET round = $roundId, strategy1 = $id1, strategy2 = $id2, status = 'W'");
                }
            }
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
                pclose(popen("start /B php -q runDuels.php", "r"));
            else
                exec("nohup /usr/bin/php -f ./runDuels.php > /dev/null 2>&1 &");
        }
    }
?>
