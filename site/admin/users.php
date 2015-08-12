<?php
    $users = getUsersList(false, true);
?>
    <script>
        changeActiveAdminButton('usersButton');
    </script>

<table class="table table-hover">
<thead>
<tr>
<td>ID</td>
<td>Логин</td>
<td>Роль</td>
<td>Фамилия</td>
<td>Имя</td>
<td>Отчество</td>
</tr>
</thead>
<tbody>
<?php
    foreach ($users as $user)
    {
        if ($user['group'] == "banned")
            echo '<tr class="redColored">';
        else
            echo '<tr>';
        echo '<td>'.$user['id'].'</td>';
        echo '<td><a href="userProfile.php?id='.$user['id'].'">'.$user['login'].'</a></td>';
        echo '<td>'.$user['group'].'</td>';
        echo '<td>'.$user['surname'].'</td>';
        echo '<td>'.$user['name'].'</td>';
        echo '<td>'.$user['patronymic'].'</td>';
        echo '</tr>';
    }
?>
</tbody>
</table>
