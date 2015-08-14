<?php
    $gameList = getGameList();
?>

<table class="table">
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
        echo '<a href="?page=game&id=' . $game['id'] . '" role="button" class="btn btn-default">Редактировать</a>';
        echo '<button class="btn btn-danger" onclick="deleteGame(' . $game['id'] . ')">Удалить</button>';
        echo "</div></td>";
        echo "</tr>";
    }
?>
</tbody>
</table>

<a role="button" href="?page=game&id=-1" style="float: right" class="btn btn-primary">
Создать игру
</a>
