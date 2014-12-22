<?php
	include_once('procedures.php');
	
	$_SESSION['adminPanelState'] = 'APuserManagement.php';
	
	if (isAdmin())
  {
    $link = getDBConnection();
    mysqli_select_db($link, getDBName());
    if ($query = mysqli_query($link, "SELECT * FROM users"))
    {
?>
	<script>
		changeActiveAdminButton('usersButton');
	</script>

<table class="table table-bordered">
<caption>Пользователи</caption>
<thead>
<tr>
<td>ID</td>
<td>Никнэйм</td>
<td>Роль</td>
<td>Фамилия</td>
<td>Имя</td>
<td>Отчество</td>
<!--<td>Удалить</td>-->
</tr>
</thead>
<tbody>
<?php
      while ($field = mysqli_fetch_assoc($query))
      {
          if ($field['group'] == "banned")
            echo '<tr class="redColored">';
          else
            echo '<tr>';
          echo '<td>'.$field['id'].'</td>';
          echo '<td><a href="userProfile.php?id='.$field['id'].'">'.$field['login'].'</a></td>';
          echo '<td>'.$field['group'].'</td>';
          echo '<td>'.$field['surname'].'</td>';
          echo '<td>'.$field['name'].'</td>';
          echo '<td>'.$field['patronymic'].'</td>';
          echo '</tr>';
      };
      mysqli_free_result($query);
?>
</tbody>
</table>
<?php
}} else
{
?>
	<p>Тебя не должно быть здесь!</p>
<?php
}
?>
