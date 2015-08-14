<?php
    $id = intval($_GET["id"]);
    if ($id == -1)
    {
        $gameId = intval($_GET["gameid"]);
        $name = "";
        $description = "";
        $state = "preparing";
        $checker = -1;
    }
    else
    {
        $tour = getTournamentData($id);
        $gameId = $tour['game'];
        $name = $tour['name'];
        $description = trim($tour['description']);
        $state = $tour['state'];
        $checker = $tour['defaultChecker'];
    }
?>
<script>
    changeActiveAdminButton('tournamentsButton');
    
    function submitForm()
    {
        var name = document.getElementById('name').value;
        var description = CKEDITOR.instances.description.getData();
        var state = document.getElementById('state').value;
        var checker = document.getElementById('checker').value;
        var id = <?php echo $id; ?>;
        var game = <?php echo $gameId; ?>;
        
        if (name != '')
        {
            var data = {'tournamentSelector' : id,
                        'tournamentName' : name,
                        'gameSelector' : game,
                        'tournamentDescription' : description,
                        'tournamentState' : state,
                        'defaultChecker' : checker};
            
            if (id == -1)
                data['createTournament'] = true;
            else
                data['updateTournament'] = true;
            
            $.post
            (   "jqueryTournament.php", 
                data,
                function (data)
                {
                    showModalAlert(data);
                    window.location.search = "?page=tournaments";
                }
            );
        }
        else
        {
            showModalAlert('Название турнира не должно быть пустым!');
        }
    }
</script>

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
        <label for = "checker" class = "APfont">Тестировщик по умолчанию:</label>
        <select id = "checker" class="form-control">
        </select>
    </div>
    <script>
        var gameId = <?php echo $gameId; ?>;
        var p = <?php echo $checker; ?>;
        
        $.post
        (
            'jqueryGetCheckerList.php',
            {'gameId' : gameId},
            function (data)
            {
                var selectData = JSON.parse(data);
                var list = document.getElementById('checker');
                
                $("#checker").empty();
                
                for (var i = 0; i < selectData.length; ++i)
                {
                    list.options[i] = new Option(selectData[i].name, selectData[i].id, false, p == selectData[i].id);
                }
            }
        );
    </script>            
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
    <div class="btn-group">
        <button type = "submit" name = "submit" 
            onclick = "submitForm(); return false;" 
            class = "btn btn-default">
            <?php
                if ($id == -1)
                    echo 'Создать турнир';
                else
                    echo 'Изменить турнир';
            ?>
        </button>
    </div>    
</form>
