<?php
    include_once("procedures.php");

    $stat = strategies_getStatistics($tournamentId);

    $graph = "";
    $ticks = "";
    $first = -1;
    $last = -1;
    $max = -1;
    $count = 0;
    foreach ($stat as $day)
    {
        $last = $day[1];
        if ($first < 0)
        {
            $first = $day[1];
        }
        else
        {
            $graph .= ",";
            $ticks .= ",";
        }
        if ($max < $day[0])
            $max = $day[0];
        $graph .= "[" . $day[1] . "," . $day[0] . "]";
        //$graph .= $day[0];
        $ticks .= "'".$day[1]."'";
        ++$count;
    }
    if ($max > 0)
    {
?>

<div id="statdiv" style="height:400px;width:<?php echo $count*60;?>px; "></div>

<script>
    $.jqplot('statdiv', [[<?php echo $graph; ?>]],
             { title:'Отправленные стратегии за день',
               series:[{renderer:$.jqplot.BarRenderer,
                       rendererOptions:{barWidth:50}}],
               axes:{yaxis:{min:0, max:<?php echo $max + 1; ?>},
                     xaxis:{ticks:[<?php echo $ticks;?>]}}
             });
</script>
<?php
    }
?>
