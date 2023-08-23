<?php

echo $this->session->flashdata('saved');

if ( !($this->lang->line('Type')) ) {
	$this->lang->load('custom'); 
}

echo iconbar([
	['rooms/add_field', $this->lang->line('AddField'), 'add.png'],
]);

$sort_cols = ["Name", "Type", "Options", "None"];

?>

<table width="100%" cellpadding="2" cellspacing="2" border="0" class="zebra-table sort-table" id="jsst-roomfields" up-data='<?= json_encode($sort_cols) ?>'>
	<col /><col /><col /><col />
	<thead>
	<tr class="heading">
		<td class="h" title="Name"><?= $this->lang->line('Name') ?></td>
		<td class="h" title="Type"><?= $this->lang->line('Type') ?></td>
		<td class="h" title="Options"><?= $this->lang->line('Options') ?></td>
		<td class="n" title="X"></td>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=0;
	if ($fields) {
	foreach ($fields as $field) { ?>
	<tr>
		<td><?php echo html_escape($field->name) ?></td>
		<td><?php echo $options_list[$field->type] ?></td>
		<td><?php
		if (isset($field->options) && is_array($field->options)) {
			$values = array();
			foreach ($field->options as $option) {
				$label = trim($option->value);
				if (empty($label)) continue;
				$values[] = html_escape($label);
			}
			echo implode(", ", $values);
		}
		?></td>
		<td width="45" class="n"><?php
			$actions['edit'] = 'rooms/edit_field/'.$field->field_id;
			$actions['delete'] = 'rooms/delete_field/'.$field->field_id;
			$this->load->view('partials/editdelete', $actions);
			?>
		</td>
	</tr>
	<?php $i++; }
	} else {
		echo '<td colspan="4" align="center" style="padding:16px 0">'.$this->lang->line('msgNoroomsexist').'</td>';
	}
	?>
	</tbody>
</table>

<?php

// echo $iconbar;
