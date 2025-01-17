<?php

echo $this->session->flashdata('saved');

if ( !($this->lang->line('Location')) ) {
	$this->lang->load('custom');
}

echo iconbar(array(
	array('rooms/add', $this->lang->line('AddRoom'), 'add.png'),
)); 

$sort_cols = ["Name", "Location", "Teacher", "Notes", "Photo", "None"];

?>

<table width="100%" cellpadding="2" cellspacing="2" border="0" class="zebra-table sort-table" id="jsst-rooms" up-data='<?= json_encode($sort_cols) ?>'>
	<col /><col /><col />
	<thead>
	<tr class="heading">
		<td class="h" title="Name"><?= $this->lang->line('Name') ?></td>
		<td class="h" title="Location"><?= $this->lang->line('Location') ?></td>
		<td class="h" title="Teacher"><?= $this->lang->line('Teacher') ?></td>
		<td class="h" title="Photo"><?= $this->lang->line('Photo') ?></td>
		<td class="n" title="X"></td>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=0;
	if( $rooms ){
	foreach( $rooms as $room ){ ?>
	<tr>
		<td><?php echo html_escape($room->name) ?></td>
		<td><?php echo html_escape($room->location) ?></td>
		<td>
			<?php
			if ($room->displayname == '' ) {
				$room->displayname = $room->username;
			}
			echo html_escape($room->displayname);
			?>
		</td>
		<td width="60" align="center">
			<?php
			$photo_path = "uploads/{$room->photo}";
			if ($room->photo != '' && is_file(FCPATH . $photo_path)) {
				$url = site_url("rooms/photo/{$room->room_id}");
				$icon_src = base_url('assets/images/ui/picture.png');
				$icon_el = "<img src='{$icon_src}' width='16' height='16' alt='".$this->lang->line('ViewPhoto')."'>";
				echo "<a href='{$url}' up-history='false' up-layer='new drawer' up-target='.room-photo' title='".$this->lang->line('ViewPhoto')."'>{$icon_el}</a>";
			}
			?>
		</td>
		<td width="45" class="n"><?php
			$actions['edit'] = 'rooms/edit/'.$room->room_id;
			$actions['delete'] = 'rooms/delete/'.$room->room_id;
			$this->load->view('partials/editdelete', $actions);
			?>
		</td>
	</tr>
	<?php $i++; }
	} else {
		echo '<td colspan="5" align="center" style="padding:16px 0">'.$this->lang->line('msgNoroomsexist').'</td>';
	}
	?>
	</tbody>
</table>

