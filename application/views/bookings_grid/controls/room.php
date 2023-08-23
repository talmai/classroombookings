
<?php
echo form_open($form_action, ['method' => 'get', 'id' => 'bookings_controls_room'], $query_params);
if(!$this->lang->line('Room')){
	$this->lang->load('custom');
}

?>

<table>
	<tr>
		<td valign="middle">
			<label>
				<?php
				$url = "rooms/info/{$room->room_id}";
				$name = $this->lang->line('Room').':';
				$link = anchor($url, $name, [
					'up-layer' => 'new drawer',
					'up-position' => 'left',
					'up-target' => '.room-info',
					'up-preload',
				]);
				echo "<strong>{$link}</strong>";
				?>
			</label>
		</td>
		<td valign="middle">
			<?php
			echo form_dropdown([
				'name' => 'room',
				'id' => 'room_id',
				'options' => $rooms,
				'selected' => $room->room_id,
			]);
			?>
		</td>
		<td> &nbsp; <input type="submit" value=" Load " /></td>
	</tr>
</table>

<?= form_close() ?>

<br />
