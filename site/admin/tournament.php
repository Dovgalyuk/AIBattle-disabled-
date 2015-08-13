<script>
    changeActiveAdminButton('tournamentsButton');
</script>

<?php
    $id = intval($_GET["id"]);
    if ($id == -1)
    {
        $gameId = intval($_GET["gameid"]);
        $name = "";
        $description = "";
        $state = "preparing";
    }
    else
    {
        $tour = getTournamentData($id);
        $gameId = $tour['game'];
        $name = $tour['name'];
        $description = trim($tour['description']);
        $state = $tour['state'];
    }
?>
<form role="form" method="post">
    <div class="form-group">
        <label for="name" class = "APfont">Название турнира:</label>
        <input type="text" class="form-control" id="name" placeholder="Введите название турнира" 
            value = "<?php echo $name; ?>">
    </div>
    <br>
    <div class="form-group">
        <label for="description" class = "APfont">Описание турнира:</label>
        <textarea id = "description" class="form-control" rows="3">
            <?php echo $description;?>
        </textarea>
        <script>
            CKEDITOR.replace('description');
        </script>
    </div>
    <br>
    <div class="form-group">
        <label for="state" class = "APfont">Статус турнира:</label>
        <select id = "state" class="form-control">
            <option value = "preparing">Подготовка к открытию</option>
            <option value = "running">Открыт</option>
            <option value = "closed">Закрыт</option>
        </select>
        <script>
            setSelectedIndexInSelector('state', '<?php echo $state; ?>');
        </script>
    </div>
    
</form>
