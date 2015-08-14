<?php
    $id = intval($_GET["id"]) or -1;
    if ($id == -1)
    {
        $name = "";
        $description = "";
        $hasVisualizer = false;
        $timeLimit = 1000;
        $memoryLimit = 64000;
    }
    else
    {
        $games = getGameList($id);
        $game = $games[0];
        $name = $game['name'];
        $description = $game['description'];
        $hasVisualizer = $game['hasVisualizer'];
        $timeLimit = $game['timeLimit'];
        $memoryLimit = $game['memoryLimit'];
    }
?>
    <script>
    function deleteGame(id)
    {
        id = id || <?php echo $id; ?>;
        
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
    function loadFormData()
    {
        var gameSelectorValue = <?php echo $id; ?>;
        
        var gameName = document.getElementById('name').value;
        var gameDescription = CKEDITOR.instances.description.getData();
        var timeLimit = document.getElementById('timeLimit').value;
        var memoryLimit = document.getElementById('memoryLimit').value;
        
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
        else
        {
            showModalAlert('Название игры не может быть пустым!');
        }
    }
    function deleteVisualizer()
    {
        var gameSelectorValue = <?php echo $id; ?>;
        
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
        <label for="name">Название игры</label>
        <input id="name" class="form-control" placeholder="Введите название игры" value="<?php
            echo $name;
    ?>" />
    </div>
    <div class="form-group">
    <label for="description">Описание игры</label>
    <textarea id = "description" class="form-control" rows="3"><?php echo $description; ?></textarea>
        <script>
            CKEDITOR.replace('description');
        </script>

    </div>
    <div class="form-group">
    <?php if ($game['hasVisualizer'] && $id != -1) 
        { 
    ?>
        <label><i>Визуализатор в наличии!</i></label>
        <br>
    <?php 
        } 
    ?>
    <label for = "uploadVisualizerFile" class = "APfont">Визуализатор <?php if ($hasVisualizer && $id != -1) { ?> (обновление) <?php } ?>:</label>
    <input type = "file" id = "uploadVisualizerFile">
    <?php
        if ($hasVisualizer && $id != -1)
        {
    ?>
        <br>
        <button type = "submit" name = "submit" onclick = "deleteVisualizer(); return false;" class = "btn btn-default">Удалить визуализатор</button>
    <?php
        }
    ?>
    <br />
    <div class="form-group">
        <label for="timeLimit" class = "APfont">Время хода (ms):</label>
        <input type="text" class="form-control" id="timeLimit" 
            placeholder="Введите ограничение времени на ход" 
            value = "<?php echo $timeLimit; ?>">
    </div>
    <br>
    <div class="form-group">
        <label for="memoryLimit" class = "APfont">Лимит памяти (kb):</label>
        <input type="text" class="form-control" id="memoryLimit" 
            placeholder="Введите ограничение стратегии по памяти" 
            value = "<?php echo $memoryLimit; ?>">
    </div>
    <div class="btn-group">
        <button type = "submit" name = "submit" onclick = "loadFormData(); return false;" class = "btn btn-default">
            <?php
                if ($id == -1)
                    echo 'Создать игру';
                else
                    echo 'Применить изменения';
            ?>
        </button>
        <?php
            if ($id != -1)
            {
        ?>
        <button onclick="deleteGame(); return false;" class="btn btn-default">Удалить игру</button>
        <?php
            }
        ?>
    </div>
</div>
