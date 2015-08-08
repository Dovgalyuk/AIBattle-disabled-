<?php
    include_once("procedures.php");

    $stat = strategies_getStatistics($tournamentId);
?>
<table class = "table table-bordered">
<tr style="font-weight:bold;" align = center><td>День</td><td>Загружено стратегий</td></tr>
<?php
    foreach ($stat as $day)
    {
        print "<tr align = center><td>" 
              . $day[1] . "</td><td>" . $day[0] 
              . "</td></tr>";
    }
?>
</table>