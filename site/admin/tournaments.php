<script>
    changeActiveAdminButton('tournamentsButton');
</script>

<table class="table table-hover">
<thead>
<tr>
<td>Название</td>
<td>Игра</td>
<td>Тестировщик</td>
<td>Статус</td>
</tr>
</thead>
<tbody>
<?php
    $tours = getTournamentList();
    foreach($tours as $tour)
    {
        $checker = getCheckerList($tour['defaultChecker']);
        echo '<tr>';
        echo '<td><a href="?page=tournament&id='.$tour['id'].'"</a>'
            . $tour['name'] .'</td>';
        echo '<td><a href="?page=games&id='.$tour['game'].'">'.getGameName($tour['game']).'</a></td>';
        echo '<td>'.$checker[0]['name'].'</td>';
        echo '<td>'.$tour['state'].'</td>';
        echo '</tr>';
    }
?>
</tbody>
</table>
