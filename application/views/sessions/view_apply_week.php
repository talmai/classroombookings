<?php
$attrs = ['class' => 'cssform-stacked'];
echo form_open('sessions/apply_week', $attrs, ['session_id' => $session->session_id]);
?>

<fieldset>

	<legend><?= $this->lang->line('Bulkapply') ?></legend>

	<div style="padding: 12px 0 12px 0;">
		<?= $this->lang->line('Bulkapplytxt') ?>
	</div>

	<p>
		<?php
		$options = array('' => $this->lang->line('SelectAWeek') );
		if (isset($weeks)) {
			foreach ($weeks as $week) {
				$options[$week->week_id] = html_escape($week->name);
			}
		}
		echo form_dropdown([
			'name' => 'week_id',
			'id' => 'week_id',
			'options' => $options,
		]);
		?>
	</p>


	<?php
	$this->load->view('partials/submit', array(
		'submit' => array( $this->lang->line('Applyweek'), tab_index()),
	));
	?>

</fieldset>

<?= form_close() ?>