<?php include_once("procedures.php"); $NOT_USE_NAVBAR = true; ?>
<?php
    if (!isAdmin())
        exit();
    if (isset($_GET['page']))
    {
        $page = $_GET['page'];
        if (isset($_GET['page_data']))
            $page_data = $_GET['page_data'];
    }
?>
<?php include("top.php"); ?>
<link rel="stylesheet" href="css/dashboard.css" />
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php"><span class="glyphicon glyphicon-arrow-left"></span> Software Battle</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="userProfile.php">Профиль</a></li>
            <li><a href="logOff.php">Выход</a></li>
          </ul>
        </div>
      </div>
    </div>
<ol class="breadcrumb">
    <?php
    if (!isset($page))
        echo '<li class="active">Стартовая страница</li>';
    else
    {
        echo '<li><a href="?">Стартовая страница</a></li>';
        if ($page == "news_list")
        {
            echo '<li class="active">Новости</li>';
        }
        if ($page == "game_list")
        {
            echo '<li class="active">Игры</li>';
        }
        if ($page == "checker_list")
        {
            echo '<li class="active">Чекеры</li>';
        }
        if ($page == "file_list")
        {
            echo '<li class="active">Файлы</li>';
        }
        if ($page == "duel_list")
        {
            echo '<li class="active">Дуэли</li>';
        }
        if ($page == "tournament_list")
        {
            echo '<li class="active">Турниры</li>';
        }
        if ($page == "images")
        {
            echo '<li class="active">Изображения</li>';
        }
        if ($page == "round_list")
        {
            echo '<li class="active">Раунды</li>';
        }
        if ($page == "users")
        {
            echo '<li class="active">Пользователи</li>';
        }
        if ($page == "faq")
        {
            echo '<li class="active">FAQ</li>';
        }
    }
    ?>
</ol>
<div class="container-fluid">
    <div class="col-sm-3 col-md-2 sidebar">
        <ul class="nav nav-sidebar">
            <li><a href="?page=news_list">Новости</a></li>
            <li><a href="?page=game_list">Игры</a></li>
            <li><a href="?page=checker_list">Чекеры</a></li>
            <li><a href="?page=file_list">Файлы</a></li>
            <li><a href="?page=duel_list">Дуэли</a></li>
            <li><a href="?page=tournament_list">Турниры</a></li>
            <li><a href="?page=images">Изображения</a></li>
            <li><a href="?page=round_list">Раунды</a></li>
            <li><a href="?page=users">Пользователи</a></li>
            <li><a href="?page=faq">FAQ</a></li>
            
        </ul>
    </div>
    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main" id="Container">
    <?php
        if ($page == "news_list")
        {
            $news = getNewsData();
            echo '<ul>';
            foreach ($news as $key)
            {
                echo '<li><a href="#">'.$key["header"].' - '.$key["date"].'</a><br /></li>';
            }
            echo '<li><a href="#"><b>Добавить новость</b></a></li>';
            echo '</ul>';
        }
    ?>
    </div>
</div>
<?php include("bottom.php"); ?>
