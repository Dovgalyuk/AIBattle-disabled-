<?php include_once("procedures.php"); ?>
<?php
    if (isActiveUser() && isset($_POST['submit']))
    {
        if (postMessage($_POST['recevier'], $_POST['title'], $_POST['text']))
            echo "Сообщение успешно отправлено";
        else
            echo "При отправке произошла ошибка";
        exit;

    }
?>
<?php include("top.php"); ?>
<script type="text/javascript">
    function sendMessage()
    {
        text = CKEDITOR.instances.userMessage.getData();
        $.post(
            'userMessages.php',
            {
                'recevier' : $("#recevier").val(),
                'title' : $("#title").val(),
                'text' : text,
                'submit' : 'true'
            },
            function (data)
            {
                alert(data);
                window.location.reload();
            } 
        );
    }

</script>
<?php
    if (isActiveUser())
    {
        $id = getActiveUser();
        $messages = getUserMessages();
?>
<br />
<table class="table table-bordered">
<thead>
<tr>
<td>От кого</td>
<td>Кому</td>
<td>Тема</td>
<td>Сообщение</td>
</tr>
</thead>
<tbody>
<?php
        foreach ($messages as $key => $value)
        {
            if (intval($value['viewed']) == 0)
                echo "<tr style=\"font-weight:bold;\">";
            else
                echo "<tr>";
            echo "<td>".getNicknameById($value['sender'])."</td>";
            echo "<td>".getNicknameById($value['recevier'])."</td>";
            echo "<td>".$value['title']."</td>";
            echo "<td><a href=\"messages.php?id=$key\">Просмотреть</a></td>";
            echo "</tr>";
        }

?>
</tbody>
</table>
    <h2 align="center">Написать сообщение</h2>
    <form method="POST" action="userMessages.php" onsubmit="sendMessage(); return false;">
    Получатель:
    <select class="form-control" id="recevier" name="recevier">
<?php
        $users = getUsersList(true);
        foreach ($users as $data)
        {
            if ($data['id'] != $id)
                echo '<option value="'.$data['id'].'">'.$data['login'].'</option>';
        }
?>
    </select>
    <br />
    <input type="text" placeholder="Тема" name="title" id="title" class="form-control" required />
    <br />
    <textarea id="userMessage" name="text" class="form-control" required>
    Сообщение
    </textarea>
    <br />
    <button class="btn btn-default" type="submit" name="submit">Отправить</button>
    <script type="text/javascript">
        CKEDITOR.replace("userMessage");
    </script>
    </form>

<?php
    }
    else
    {
?>
<h1 align="center" class="redColored">Авторизируйтесь</h1>
<?php
    }
?>

<?php include("bottom.php"); ?>
