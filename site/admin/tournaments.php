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
        echo '<tr>';
        echo '<td><a href="newAdminPanel.php?page=tournament&id='.$tour['id'].'"</a>'
            . $tour['name'] .'</td>';
        echo '<td>'.getGameName($tour['game']).'</td>';
        echo '<td>'.$tour['defaultChecker'].'</td>';
        echo '<td>'.$tour['state'].'</td>';
        echo '</tr>';
    }
?>
</tbody>
</table>
