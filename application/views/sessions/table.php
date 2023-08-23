
<table
	width="100%"
	cellpadding="4"
	cellspacing="2"
	border="0"
	class="border-table table-align-vat"
	up-data='<?= json_encode($sort_cols) ?>'
	id="<?= $id ?>"
>
	<col /><col /><col /><col />
	<thead>
		<tr class="heading">
			<td class="h" width="20%" title="Name"><?php echo $this->lang->line('Name'); ?></td>
			<td class="h" width="10%" title="Current?"><?php echo $this->lang->line('Current'); ?>?</td>
			<td class="h" width="10%" title="Available?"><?php echo $this->lang->line('Available'); ?>?</td>
			<td class="h" width="25%" title="Start date"><?php echo $this->lang->line('Startdate'); ?></td>
			<td class="h" width="25%" title="End date"><?php echo $this->lang->line('Enddate'); ?></td>
			<td class="h" width="10%" title="Actions"></td>
		</tr>
	</thead>

	<?php if (empty($items)): ?>

	<tbody>
		<tr>
			<td colspan="6" align="center" style="padding:16px 0; color: #666"><?php echo $this->lang->line('NoSessions'); ?>.</td>
		</tr>
	</tbody>

	<?php else: ?>

	<tbody>
		<?php

		$dateFormat = setting('date_format_long', 'crbs');

		foreach ($items as $session) {

			echo "<tr>";

			$name = html_escape($session->name);
			$link = anchor("sessions/view/{$session->session_id}", $name);
			echo "<td>{$link}</td>";

			// Current
			$img = '';
			if ($session->is_current == 1) {
				$img = img(['src' => 'assets/images/ui/enabled.png', 'width' => '16', 'height' => '16', 'alt' => 'Current session']);
			}
			echo "<td>{$img}</td>";

			// Selectable
			$img = '';
			if ($session->is_selectable == 1) {
				$img = img(['src' => 'assets/images/ui/enabled.png', 'width' => '16', 'height' => '16', 'alt' => 'Selectable']);
			}
			echo "<td>{$img}</td>";

			if($this->lang->get_idioma()=="english"){
				$start = $session->date_start ? $session->date_start->format($dateFormat) : '';
				$end = $session->date_end ? $session->date_end->format($dateFormat) : ''; // Sunday 1st January 2023
			}else{
				$start = $session->date_start ? $this->lang->diaTraduzido( $session->date_start, 'EEEE, d MMMM yyyy' ) : '';
				$end = $session->date_end ? $this->lang->diaTraduzido( $session->date_end, 'EEEE, d MMMM yyyy' ) : '';
				// Domingo 1 Janeiro 2023 = 'EEEE d MMMM YYYY'
			}
			
			echo "<td>{$start}</td>";
			echo "<td>{$end}</td>";
			echo "<td>";
			
			$actions['edit'] = 'sessions/edit/'.$session->session_id;
			$actions['delete'] = 'sessions/delete/'.$session->session_id;
			$this->load->view('partials/editdelete', $actions);
			echo "</td>";

			echo "</tr>";

		}

		?>
	</tbody>

	<?php endif; ?>

</table>

