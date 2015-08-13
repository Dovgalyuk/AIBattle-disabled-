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
?>
<script type="text/javascript">
    function deleteGame(id)
    {
        id = id || <?php echo $subpage; ?>;
        
        $.post
        (
            "jqueryGame.php",
            {
                'gameId'        : id,
                'deleteGame'    : true
            },
            function (data)
            {
                showModalAlert(data);
                window.location.search = "?page=games";
            }
        );
    }
</script>
<?php
        if ($subpage == 0)
        {
            $gameList = getGameList();
?>

<table class="table">
<caption>Игры</caption>
<thead>
<tr>
<td>ID</td>
<td>Название</td>
<td></td>
</tr>
</thead>
<tbody>
<?php
    foreach ($gameList as $game)
    {
        echo "<tr>";
        echo "<td>" . $game['id'] . "</td>";
        echo "<td>" . $game['name'] . "</td>";
        echo "<td>";
        echo '<div class="btn-group">';
        echo '<a href="?page=checkers&subpage2=' . $game['id'] . '" role="button" class="btn btn-default">Чекеры</a>';
        echo '<button class="btn btn-default">Файлы</button>';
        echo '<a href="?page=' . $page . '&subpage=' . $game['id'] . '" role="button" class="btn btn-default">Редактировать</a>';
        echo '<button class="btn btn-danger" onclick="deleteGame(' . $game['id'] . ')">Удалить</button>';
        echo "</div></td>";
        echo "</tr>";
    }
?>
</tbody>
</table>

<a role="button" href="?page=<?php echo $page; ?>&subpage=-1" style="float: right" class="btn btn-primary">
Создать игру
</a>

<?php
    }
    else
    {
        $games = getGameList($subpage);
        $game = $games[0];
        ?>
        <script>
        function loadFormData()
        {
            var gameSelectorValue = <?php echo $subpage; ?>;
            
            var gameName = document.getElementById('gameName').value;
            var gameDescription = CKEDITOR.instances.gameDescription.getData();
            var timeLimit = document.getElementById('gameTurnTime').value;
            var memoryLimit = document.getElementById('gameMemoryLimit').value;
            
            if (gameName != '')
            {
            
                var form = new FormData();
                form.append('gameId', gameSelectorValue);
                form.append('gameName', gameName);
                form.append('gameDescription', gameDescription);
                form.append('timeLimit', timeLimit);
                form.append('memoryLimit', memoryLimit);
                
                if ($('#uploadVisualizerFile')[0].files[0])
                {
                    form.append('visualizerPath', 'uploadVisualizerFile');
                    form.append('uploadVisualizerFile', $('#uploadVisualizerFile')[0].files[0]);
                }
                form.append(gameSelectorValue == -1 ? 'createGame' : 'updateGame', true);
                
                $.ajax({
                    url: 'jqueryGame.php',
                    type: 'POST',
                    success: function (data)
                    {   
                        showModalAlert(data);
                        window.location.search = "?page=games";
                    },
                    data: form,
                    cache: false,
                    contentType: false,
                    processData: false
                });
            }
            else showModalAlert('Название игры не может быть пустым!');
        }
        function deleteVisualizer()
        {
            var gameSelectorValue = <?php echo $subpage; ?>;
            
            $.post
            (
                "jqueryGame.php",
                {
                    'gameId'            : gameSelectorValue,
                    'deleteVisualizer'  : true
                },
                function (data)
                {
                    showModalAlert(data);
                    window.location.reload();
                }
            );
        }
        </script>
        <div class="form-group">
            <label for="gameName">Название игры</label>
            <input id="gameName" class="form-control" placeholder="Введите название игры" value="<?php
            if ($subpage != -1)
                echo $game['name'];
        ?>" />
        </div>
        <div class="form-group">
        <label for="gameDescription">Описание игры</label>
        <textarea id = "gameDescription" class="form-control" rows="3"><?php if ($subpage != -1) echo trim($game['description']);?></textarea>
            <script>
                CKEDITOR.replace('gameDescription');
            </script>

        </div>
        <div class="form-group">
        <?php if ($game['hasVisualizer'] && $subpage != -1) 
            { 
        ?>
            <label><i>Визуализатор в наличии!</i></label>
            <br>
        <?php 
            } 
        ?>
        <label for = "uploadVisualizerFile" class = "APfont">Визуализатор <?php if ($game['hasVisualizer'] && $subpage != -1) { ?> (обновление) <?php } ?>:</label>
        <input type = "file" id = "uploadVisualizerFile">
        <?php
            if ($game['hasVisualizer'] && $subpage != -1)
            {
        ?>
            <br>
            <button type = "submit" name = "submit" onclick = "deleteVisualizer(); return false;" class = "btn btn-default">Удалить визуализатор</button>
        <?php
            }
        ?>
        <br />
        <div class="form-group">
            <label for="gameTurnTime" class = "APfont">Время хода (ms):</label>
            <input type="text" class="form-control" id="gameTurnTime" placeholder="Введите ограничение времени на ход" value = "<?php if ($subpage != -1) echo $game['timeLimit']; ?>">
        </div>
        <br>
        <div class="form-group">
            <label for="gameMemoryLimit" class = "APfont">Лимит памяти (kb):</label>
            <input type="text" class="form-control" id="gameMemoryLimit" placeholder="Введите ограничение стратегии по памяти" value = "<?php if ($subpage != -1) echo $game['memoryLimit']; ?>">
        </div>
        <div class="btn-group">
            <button type = "submit" name = "submit" onclick = "loadFormData(); return false;" class = "btn btn-default">
                <?php
                    if ($subpage == -1)
                        echo 'Создать игру';
                    else
                        echo 'Применить изменения';
                ?>
            </button>
            <?php
                if ($subpage != -1)
                {
            ?>
            <button onclick="deleteGame(); return false;" class="btn btn-default">Удалить игру</button>
            <?php
                }
            ?>
        </div>
    </div>
        <?php
    }
    }
    if ($page == "news") // Страница новостей
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
