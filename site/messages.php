<?php include_once("procedures.php"); ?>
<?php
    $id = intval($_GET['id']);
    if (isset($_GET['id']))
    {
        $messages = getUserMessages(0, -1, $id);
        if (isset($messages[$id]))
        {
            $msg = $messages[$id];
            if ($msg['recevier'] == getActiveUser())
                markMessageAsViewed($id);
?>

<?php include("top.php"); ?>
            <br />
            <p style="font-size:16px;">
            Тема: <?php echo $msg['title']; ?><br />
            От кого: <?php echo getNicknameById($msg['sender']); ?><br />
            Кому: <?php echo getNicknameById($msg['recevier']); ?><br />
            Сообщение:
            </p>           
            <?php
                echo $msg['text'];
            ?> 

<?php
        };
    };
?>

<?php include("bottom.php"); ?>
