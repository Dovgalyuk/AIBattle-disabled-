<script type="text/javascript">
    function deleteNews(currentNews)
    {
        window.location.search = "?page=news";
        var currentNews = currentNews || <?php echo $subpage; ?>;
        $.post
        (
            "jqueryNews.php",
            {
                'newsId'        : currentNews,
                'deleteNews'    : true
            },
            function (data)
            {
                showModalAlert(data);
            }
        );  
        
    }
</script>
<?php
    $subpage = intval($_GET["subpage"]) or 0;
    $subpage2 = intval($_GET["subpage2"]) or 0;
    if ($subpage == 0)
    {
        $newsList = getNewsData();
?>

<table class="table">
<caption>Новости</caption>
<thead>
<tr>
<td>Заголовок</td>
<td>Дата</td>
<td></td>
</tr>
</thead>
<tbody>
<?php
    foreach ($newsList as $news)
    {
        echo "<tr>";
        echo "<td>" . $news['header'] . "</td>";
        echo "<td>" . $news['date'] . "</td>";
        echo "<td><div class=\"btn-group\">";
        echo '<a href="?page=' . $page . '&subpage=' . $news['id'] . '" role="button" class="btn btn-default">Редактировать</a>';
        echo '<button class="btn btn-danger" onclick="deleteNews(' . $news['id'] . ')">Удалить</button>';
        echo "</div></td>";
        echo "</tr>";
    }
?>
</tbody>
</table>

<a role="button" href="?page=<?php echo $page; ?>&subpage=-1" style="float: right" class="btn btn-primary">
Создать новость
</a>
<?php
}
else
{
    $currentNews = getNewsData($subpage);
    $date = '';
    if ($subpage == -1)
    {
        $date[0] = date("Y");
        $date[1] = date("m");
        $date[2] = date("d");
    }
    else
    {
        $date = explode("-", $currentNews["date"]);
    }
    ?>
    <script type="text/javascript">
        function loadFormData()
        {
            var currentNews         = <?php echo $subpage; ?>;
            var currentTitle        = document.getElementById('newsHeader').value;
            var currentText         = CKEDITOR.instances.newsDescription.getData();
            var currentDate         = document.getElementById('datepicker').value;
            
            if (currentTitle != '')
            {
                var newsData = {'newsId' : currentNews, 'header' : currentTitle, 'text' : currentText, 'date' : currentDate};
                
                if (currentNews == -1)
                    newsData['createNews'] = true;
                else
                    newsData['updateNews'] = true;
                
                $.post
                (   "jqueryNews.php", 
                    newsData,
                    function (data)
                    {
                        showModalAlert(data);
                        window.location.search = "?page=news";
                    }
                );
            }
            else showModalAlert('Заголовок новости не должен быть пустым!');
        }
    </script>
                <div class="form-group">
                <label for="newsHeader">Заголовок новости:</label>
                <input type="text" class="form-control" id="newsHeader" placeholder="Введите заголовок" value = "<?php if ($nsubpage != -1) echo $currentNews['header']; ?>">
            </div>
            <br>
            <div class="form-group">
                <label for="newsDescription">Описание новости:</label>
                <textarea id = "newsDescription" class="form-control" rows="3"><?php if ($subpage != -1) echo trim($currentNews['text']);?></textarea>
                <script>
                    CKEDITOR.replace('newsDescription');
                </script>
            </div>
            <br>
            <br>
            <div class="form-group">
                <label for="date">Дата новости:</label>
                <input type="text" name="date" value="<?php echo $date[2]."/".$date[1]."/".$date[0];?>" id="datepicker">
                <script type="text/javascript">
                    $(
                        function() 
                        {
                            $("#datepicker").datepicker({
                                yearRange: "1990:2016",
                                dateFormat: "dd/mm/yy"
                            });
                        }
                    );
                </script>
            </div>
            <div class="btn-group">
                <button type = "submit" name = "submit" onclick = "loadFormData(); return false;" class = "btn btn-default">
                    <?php
                        if ($subpage == -1)
                            echo 'Создать новость';
                        else
                            echo 'Применить изменения';
                    ?>
                </button>
                <?php
                    if ($subpage!= -1)
                    {
                ?>
                <button type = "submit" name = "submit" onclick = "deleteNews(); return false;" class = "btn btn-default">Удалить новость</button>
                <?php
                    }
                ?>
            </div>
<?php
}
?>
