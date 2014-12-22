<?php

    include_once('procedures.php');
    $user1 = intval($_POST["user1"]);
    $user2 = intval($_POST["user2"]);
    $round = intval($_POST["round"]);
    $method = $_POST["m"];

    if ((!isset($user1) || !isset($user2) || !isset($round)) && !isset($method))
    {
        echo "1&0 ".print_r($_POST);
        exit();
    }
    
    if (isset($method) && isset($user1) && isset($user2))
    {
        echo getUserName($user1).'&'.getUserName($user2);
        exit();
    }
    
    $duel = getDuelStatus($round, $user1, $user2);
    $result = 0;
    $res = $duel['status'];
    
    if ($res == "WIN 1" || $res == "IM 2" || $res == "RE 2" || $res == "TL 2" || $res == "ML 2") $result = 1;
    if ($res == "WIN 2" || $res == "IM 1" || $res == "RE 1" || $res == "TL 1" || $res == "ML 1") $result = -1;
    
    echo '0&'.$result.'&'.$res;
    exit();
    
?>