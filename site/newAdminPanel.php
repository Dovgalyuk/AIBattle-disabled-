<?php
    include_once("procedures.php");
    $page = $_GET["page"] or "";
    function getRusTitle($p)
    {
        if ($p == "news") return "Новости";
        if ($p == "games") return "Игры";
        if ($p == "checkers") return "Чекеры";
        if ($p == "files") return "Файлы";
        if ($p == "duels") return "Дуэли";
        if ($p == "images") return "Изображения";
        if ($p == "rounds") return "Раунды";
        if ($p == "users") return "Пользователи";
        if ($p == "faq") return "FAQ";
        return "";
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

	<body>

    <nav class="navbar navbar-default navbar-fixed-top navbar-inverse">
      <div class="container" style="margin-left:0;">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/"><span class="glyphicon glyphicon-arrow-left"></span> AiBattle</a>
        </div>
      </div>
    </nav>
<div id="wrapper">
    <div id="sidebar-wrapper">
    <ul class="sidebar-nav">
    <li class="<?php echo ($page == 'news')?'active':'' ?>">
    <a href="?page=news"><?php echo getRusTitle("news"); ?></a>
    </li>
    <li class="<?php echo ($page == 'games')?'active':'' ?>">
    <a href="?page=games"><?php echo getRusTitle("games"); ?></a>
    </li>
    <li class="<?php echo ($page == 'checkers')?'active':'' ?>">
    <a href="?page=checkers"><?php echo getRusTitle("checkers"); ?></a>
    </li>
    <li class="<?php echo ($page == 'files')?'active':'' ?>">
    <a href="?page=files"><?php echo getRusTitle("files"); ?></a>
    </li>
    <li class="<?php echo ($page == 'duels')?'active':'' ?>">
    <a href="?page=duels"><?php echo getRusTitle("duels"); ?></a>
    </li>
    <li class="<?php echo ($page == 'images')?'active':'' ?>">
    <a href="?page=images"><?php echo getRusTitle("images"); ?></a>
    </li>
    <li class="<?php echo ($page == 'rounds')?'active':'' ?>">
    <a href="?page=rounds"><?php echo getRusTitle("rounds"); ?></a>
    </li>
    <li class="<?php echo ($page == 'users')?'active':'' ?>">
    <a href="?page=users"><?php echo getRusTitle("users"); ?></a>
    </li>
    <li class="<?php echo ($page == 'faq')?'active':'' ?>">
    <a href="?page=faq"><?php echo getRusTitle("faq"); ?></a>
    </li>
    </ul>
    </div>
<div id="page-content-wrapper">
<div class="container-fluid">
<ol class="breadcrumb">
<?php
    if (getRusTitle($page) != "")
    {
?>
        <li><a href="?page=">Главная</a></li>
        <li class="active"><?php echo getRusTitle($page) ?></li>
<?php
    } else
    {
?>
        <li class="active">Главная</li>
<?php
    }
?>
</ol>
<?php if ($page == "users")
    {
?>
<?php
    $link = getDBConnection();
    mysqli_select_db($link, getDBName());
    if ($query = mysqli_query($link, "SELECT * FROM users"))
    {
?>
	<script>
		changeActiveAdminButton('usersButton');
	</script>

<table class="table table-hover">
<caption>Пользователи</caption>
<thead>
<tr>
<td>ID</td>
<td>Никнэйм</td>
<td>Роль</td>
<td>Фамилия</td>
<td>Имя</td>
<td>Отчество</td>
<!--<td>Удалить</td>-->
</tr>
</thead>
<tbody>
<?php
      while ($field = mysqli_fetch_assoc($query))
      {
          if ($field['group'] == "banned")
            echo '<tr class="redColored">';
          else
            echo '<tr>';
          echo '<td>'.$field['id'].'</td>';
          echo '<td><a href="userProfile.php?id='.$field['id'].'">'.$field['login'].'</a></td>';
          echo '<td>'.$field['group'].'</td>';
          echo '<td>'.$field['surname'].'</td>';
          echo '<td>'.$field['name'].'</td>';
          echo '<td>'.$field['patronymic'].'</td>';
          echo '</tr>';
      };
      mysqli_free_result($query);
?>
</tbody>
</table>
<?php
}}
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
?>
<html>
    <head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Ошибка 403</title>
        <link href="./css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="alert alert-danger" role="alert">Упс! Вам сюда нельзя!</div>
    </body>
</html>
<?php
    }
?>
