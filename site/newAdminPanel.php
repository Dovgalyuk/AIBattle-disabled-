<?php
    include_once("procedures.php");
    $page = $_GET["page"] or "";
    $subpage = intval($_GET["subpage"]) or 0;
    $subpage2 = intval($_GET["subpage2"]) or 0;

    $pages = array("news", "games", "duels", "tournaments", "users", "faq");

    function getRusTitle($p)
    {
        if ($p == "news") return "Новости";
        if ($p == "games") return "Игры";
        if ($p == "files") return "Файлы";
        if ($p == "duels") return "Дуэли";
        if ($p == "tournaments") return "Турниры";
        if ($p == "users") return "Пользователи";
        if ($p == "faq") return "FAQ";
        return "";
    }

    function getAlternativePage($p)
    {
        if ($p == "checkers")
            return "games";
        return "";
    }

    function getSubpageTitle($p, $subp, $subp2)
    {
        $games = getGameList($subp);
        if ($p == "games")
        {
            if ($subp == -1)
                return "Создание игры";
            $game = $games[0];
            return $game['name'];
        }
        if ($p == "news")
        {
            if ($subp == -1)
                return "Новая новость";
            $news = getNewsData($subp);
            return $news['header'];
        } 
        if ($p == "checkers")
        {
            $game = $games[0];
            if ($subp == -1)
                return "Новый чекер для '" . $game['name'] . "'";
            if ($subp == 0)
                return "Чекеры для '" . $game['name'] . "'";
        $checkers = getCheckerList($subp);
            $checker = $checkers[0];
            return $checker['name'] . " для '" . $game['name'] . "'";
        }
        return "";
    }

    function printTitleItem($name, $link)
    {
        if ($link == "")
            echo '<li class="active">'.$name.'</li>';
        else
            echo '<li><a href="'.$link.'">'.$name.'</a></li>';
    }

    function printPageTitle()
    {
        $page = $_GET["page"] or "";
        if ($page == "")
        {
            printTitleItem("Главная", "");
        }
        else
        {
            printTitleItem("Главная", "?page=");
            if ($page == "tournament")
            {
                $tour = getTournamentData($_GET["id"]);
                printTitleItem(getRusTitle("games"), "?page=games");
                printTitleItem(getGameName($tour['game']), "?page=game&id=".$tour['game']);
                printTitleItem(getRusTitle("tournaments"), "?page=tournaments");
                printTitleItem($tour['name'], "");
            }
            else if ($page == "games")
            {
                printTitleItem(getRusTitle("games"), "");
            }
            else if ($page == "game")
            {
                printTitleItem(getRusTitle("games"), "?page=games");
                printTitleItem(getGameName($_GET["id"]), "");
            }
            else
            {
                printTitleItem(getRusTitle($page), "");
            }
        }
    }
    if (isAdmin())
    {
?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        

        <title>AiBattle - Experimental admin panel</title>

        <!-- Bootstrap core CSS -->
        
        <link href="./css/bootstrap.min.css" rel="stylesheet">
        
        <link href="./css/mySiteCSS.css" rel="stylesheet">
        <link href="./css/adminPanel.css" rel="stylesheet">
        <link href="./css/ui-lightness/jquery-ui-1.10.4.min.css" rel="stylesheet">
                
        <script src="./ckeditor/ckeditor.js"></script>
        <script src="./ckeditor/adapters/jquery.js"></script>
        
        
        <script src="./js/jquery-1.10.2.min.js"></script>
        <script src="./js/jquery-ui-1.10.4.min.js"></script>
        <script src="./js/procedures.js"></script>
                <script src="./js/bootstrap.min.js"></script>
     </head>

    <body style="overflow-x: hidden">

    <nav class="navbar navbar-default navbar-fixed-top navbar-inverse">
      <div class="container" style="margin-left:0;">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/"><span class="glyphicon glyphicon-chevron-left"></span> AIBattle</a>
        </div>
      </div>
    </nav>
<div id="wrapper">
    <div id="sidebar-wrapper">
    <ul class="sidebar-nav">

    <?php
        foreach ($pages as $p)
        {
            echo '<li class="' . ((($page == $p) || (getAlternativePage($page) == $p))?'active':'') . '"><a href="?page=' . $p . '">' . getRusTitle($p) . '</a></li>';
        }
    ?>


    </ul>
    </div>
<div id="page-content-wrapper">
<div class="container-fluid">
<ol class="breadcrumb">
<?php
    printPageTitle();
?>
</ol>
<?php 
    if ($page == "games") // Страница игр
    {
        include("admin/games.php");
    }
    else if ($page == "game")
    {
        include("admin/game.php");
    }
    else if ($page == "news") // Страница новостей
    {
        include("admin/news.php");
    }
    else if ($page == "tournaments")
    {
        include("admin/tournaments.php");
    } 
    else if ($page == "tournament")
    {
        include("admin/tournament.php");
    } 
    else if ($page == "users") // Страница пользователей
    {
        include("admin/users.php");
    }
    else if ($page == "") // Главная
    {
?>
<h3>Статус сервера

 <span class="small" style="color: green">Всё хорошо</span></h3>

<table class="table table-hover">
<tbody>
    <tr>
        <td>
            <a href="?page=users">Активные пользователи</a>
        </td>
        <td>
            <?php
                echo count(getUsersList());
            ?>
        </td>

    </tr>
    <tr>
        <td>
            <a href="?page=tournaments">Турниры</a>
        </td>
        <td>
             <?php
                echo count(getRunningAndClosedTournaments());
             ?>
        </td>
    </tr>
    <tr>
        <td>
            Новые вопросы
        </td>
        <td>
             Нет
        </td>
    </tr>
</tbody>
<?php
    }
?>
</div>
</div>
</div>
<script>
    $(".navbar-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
</script>
</body></html>
<?php
    } else
    {
        header('HTTP/1.0 403 Forbidden');
        include_once("top.php");
?>
    <div class="content container">
        <div class="alert alert-danger" role="alert">У вас нет привилегий для просмотра этой страницы</div>
    </div>
<?php
    include_once("bottom.php");
    }
?>
