<h3>Tasks by Priority</h3>
<form method='POST' action='{completer}'>
<table class="table">
    <tr>
        <th>Id</th>
		<th></th>
        <th>Task</th>
        <th>Priority</th>
    </tr>
    {display_tasks}
    <tr>
        <td>{id}</td>
		<td><input type='checkbox' name='task{id}'/></td>
        <td>{task}</td>
        <td>{priority}</td>
    </tr>
    {/display_tasks}    
</table>
<input type='submit' value='Complete checked tasks'/>
</form>