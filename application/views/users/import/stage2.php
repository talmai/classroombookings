<?php

if ( !($this->lang->line('Type')) ) {
	$this->lang->load('custom'); 
}

function import_status($key) {

	$labels = array(
		'username_empty' => $this->lang->line('Usernameempty'),
		'password_empty' => $this->lang->line('Nopassword'),
		'username_exists' => $this->lang->line('Userexists'),
		'success' => $this->lang->line('Success'),
		'db_error' => $this->lang->line('Error'),
		'invalid' => $this->lang->line('Failedvalidation'),
	);

	if (array_key_exists($key, $labels)) {
		return $labels[$key];
	}

	return 'Unknown';
}
?>

<?php if (is_array($result)): ?>

<table cellpadding="2" cellspacing="2" width="100%">

	<thead>
		<tr class="heading">
			<td class="h"><?= $this->lang->line('Row') ?></td>
			<td class="h"><?= $this->lang->line('Username') ?></td>
			<td class="h"><?= $this->lang->line('Created') ?></td>
			<td class="h"><?= $this->lang->line('Status') ?></td>
		</tr>
	</thead>

	<tbody>

		<?php
		foreach ($result as $row) {

			$colour = ($row->status == 'success') ? 'darkgreen' : 'darkred';

			echo '<tr>';
			echo "<td>#{$row->line}</td>";
			echo '<td style="width: 50%">' . html_escape($row->user->username) . '</td>';
			echo '<td>' . ($row->status == 'success' ? $this->lang->line('Yes') : $this->lang->line('No') ) . '</td>';
			echo "<td style='font-weight:bold;color:{$colour}'>" . import_status($row->status) . "</td>";
			echo '</tr>';
		}
		?>
	</tbody>

</table>

<?php endif; ?>

<?php

$iconbar = iconbar(array(
	array('users', $this->lang->line('AllUsers'), 'school_manage_users.png'),
	array('users/import', $this->lang->line('ImportMoreUsers'), 'user_import.png'),
));

echo $iconbar;
