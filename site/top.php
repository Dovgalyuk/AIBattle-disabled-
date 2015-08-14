<?php include_once('procedures.php'); ?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		

		<title><?php echo getPageHeaderByScriptName($_SERVER['PHP_SELF']);?></title>

		<!-- Bootstrap core CSS -->
		
		<link href="./css/bootstrap.min.css" rel="stylesheet">
		
		<link href="./css/mySiteCSS.css" rel="stylesheet">
		<link href="./css/ui-lightness/jquery-ui-1.10.4.min.css" rel="stylesheet">
		<link href="./css/jquery.jqplot.css" rel="stylesheet">

		<script src="./ckeditor/ckeditor.js"></script>
		<script src="./ckeditor/adapters/jquery.js"></script>

		<script src="./js/jquery-1.10.2.min.js"></script>
		<script src="./js/jquery-ui-1.10.4.min.js"></script>
		<script src="./js/jquery.jqplot.min.js"></script>
		<script src="./js/jqplot.barRenderer.min.js"></script>
		<script src="./js/procedures.js"></script>
                <script src="./js/bootstrap.min.js"></script>
	 </head>

	<body>
        <?php
            if (!isset($NOT_USE_NAVBAR))
            {
        ?>
        <nav class="navbar navbar-default navbar-static-top navbar-inverse" role="navigation">
            <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a href="index.php" class="navbar-brand">AIBattle</a>
            </div>
            <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
            <li class="dropdown <?php echo getActiveNavbarElement("tournament", $_SERVER['PHP_SELF']); ?>">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Турниры <b class="caret"></b></a>
            <ul class = "dropdown-menu">
                <?php
                    $tournaments = getRunningAndClosedTournaments();
                    foreach ($tournaments as $id => $name)
                    {
                        $data = getTournamentData($id);
                ?>
                    <li>
                        <a href="tournament.php?id=<?php echo $id;?>" onclick = "$.post('jqueryResetTournamentState.php', function(txt) { return txt;});">
                            <?php
                                if ($data['state'] == 'closed')
                                    echo '<i>'.$name.'</i>';
                                else
                                    echo $name;
                            ?>
                        </a>
                    </li>
                <?php
                    }
                ?>
            </ul>
            </li>
            <!--
            <li class = "dropdown <?php echo getActiveNavbarElement("game", $_SERVER['PHP_SELF']); ?>">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Игры <b class="caret"></b></a>
                <ul class = "dropdown-menu">
                    <?php
                        $games = getGameArray();
                        foreach ($games as $id => $name)
                        {
                    ?>
                        <li><a href="game.php?id=<?php echo $id;?>"><?php echo $name;?></a></li>
                    <?php
                        }
                    ?>
                </ul>
            </li>
            -->
            <?php
                if (isAdmin())
                {
            ?>
                <li class = "<?php echo getActiveNavbarElement("adminPanel", $_SERVER['PHP_SELF']); ?>"><a href="adminPanel.php">Управление</a></li>
            <?php
                }
            ?>
            </ul>
            <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle profilePadding" data-toggle="dropdown"> 
                    <span class="glyphicon glyphicon-user"></span> <?php if (!isActiveUser()) echo "Профиль"; else
echo getActiveUserNickname().(($notViewedMessages = getNotViewedMessages()) > 0 ? " ($notViewedMessages)" : "")?> <b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                <?php 
                    if (!isActiveUser())
                    {
                ?>
                    <li><a href="userAuthorization.php">Войти</a></li>
                    <li class="divider"></li>
                    <li><a href="userRegistration.php">Зарегистрироваться</a></li>
                <?php
                    }
                    else
                    {
                ?>
                    <li><a href="userProfile.php">Профиль</a></li>
                    <li><a href="userMessages.php">Сообщения<?php echo (($notViewedMessages = getNotViewedMessages()) > 0 ? " ($notViewedMessages)" : "");?></a></li>
                    <li class="divider"></li>
                    <li><a href="logOff.php">Выйти</a></li>
                <?php
                    }
                ?>
                </ul>
            </li>
            </ul>
            </div>
            </div>
        </nav>

		<div class = "wrap">
        <?php
            };
        ?>
		
