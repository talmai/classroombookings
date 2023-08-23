<?php
echo $this->session->flashdata('saved');
echo form_open(current_url(), array('id'=>'settings', 'class'=>'cssform'));

if ( !($this->lang->line('Rooms')) ) {
	$this->lang->load('custom'); // all
}

?>
<fieldset>

	<legend accesskey="S" tabindex="<?php echo tab_index() ?>"><?= $this->lang->line('BookingsTitle') ?></legend>

	<p>
		<label for="brestriction"><?= $this->lang->line('Bookingrestriction') ?></label>
		<?php
		$value = 0; //$value = (int) set_value('bia', element('bia', $settings), FALSE);
		echo form_input(array(
			'name' => 'brestriction',
			'id' => 'brestriction',
			'size' => '5',
			'maxlength' => '3',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
		<p class="hint"><?= $this->lang->line('BookingrestrictionTxt') ?></p>
	</p>

	<p>
		<label for="bia"><?= $this->lang->line('Bookinginadvance') ?></label>
		<?php
		$value = (int) set_value('bia', element('bia', $settings), FALSE);
		echo form_input(array(
			'name' => 'bia',
			'id' => 'bia',
			'size' => '5',
			'maxlength' => '3',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
		<p class="hint"><?= $this->lang->line('BookingsHint1') ?></p>
	</p>
	<?php echo form_error('bia') ?>

	<p>
		<label for="num_max_bookings"><?= $this->lang->line('Maximumactivebookings') ?></label>
		<?php
		$value = (int) set_value('num_max_bookings', element('num_max_bookings', $settings), FALSE);
		echo form_input(array(
			'name' => 'num_max_bookings',
			'id' => 'num_max_bookings',
			'size' => '5',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
		<p class="hint"><?= $this->lang->line('BookingsHint2') ?></p>
		<p class="hint"><?= $this->lang->line('BookingsHint3') ?></p>
	</p>
	<?php echo form_error('num_max_bookings') ?>

	<hr size="1" />

	<p id="settings_displaytype">
		<label for="displaytype"><?= $this->lang->line('Displaytype') ?></label>
		<?php

		$field = "displaytype";
		$value = set_value($field, element($field, $settings), FALSE);

		$options = [
			['value' => 'day', 'label' => $this->lang->line('1dayatatime'), 'enable' => 'd_columns_rooms'],
			['value' => 'room', 'label' => $this->lang->line('1roomatatime'), 'enable' => 'd_columns_days'],
		];

		foreach ($options as $opt) {
			$id = "{$field}_{$opt['value']}";
			$input = form_radio(array(
				'name' => $field,
				'id' => $id,
				'value' => $opt['value'],
				'checked' => ($value == $opt['value']),
				'tabindex' => tab_index(),
				'up-switch' => '.d_columns_target',
			));
			echo "<label for='{$id}' class='ni'>{$input}{$opt['label']}</label>";
		}

		?>
		<br />
		<p class="hint"><?= $this->lang->line('BookingsHint4') ?><br />
			<strong><span><?= $this->lang->line('1dayatatime') ?></span></strong> - <?= $this->lang->line('1dayatatimeDescrip') ?><br />
			<strong><span><?= $this->lang->line('1roomatatime') ?></span></strong> - <?= $this->lang->line('1roomatatimeDescrip') ?>
		</p>
	</p>
	<?php echo form_error('displaytype'); ?>

	<p id="settings_columns">
		<label for="columns"><?= $this->lang->line('Columns') ?></label>
		<?php

		$field = 'd_columns';
		$value = set_value($field, element($field, $settings), FALSE);

		$options = [
			['value' => 'periods', 'label' => $this->lang->line('Periods'), 'for' => ''],
			['value' => 'rooms', 'label' => $this->lang->line('Rooms'), 'for' => 'day'],
			['value' => 'days', 'label' => $this->lang->line('Days'), 'for' => 'room'],
		];

		foreach ($options as $opt) {
			$id = "{$field}_{$opt['value']}";
			$input = form_radio(array(
				'name' => $field,
				'id' => $id,
				'value' => $opt['value'],
				'checked' => ($value == $opt['value']),
				'tabindex' => tab_index(),
			));
			echo "<label for='{$id}' class='d_columns_target ni' up-show-for='{$opt['for']}'>{$input}{$opt['label']}</label>";
		}
		?>
		<p class="hint"><?= $this->lang->line('BookingsHint5') ?></p>
	</p>
	<?php echo form_error('d_columns') ?>

	<hr size="1" />

	<p>
		<label for="<?= $field ?>"><?= $this->lang->line('UserdetailsTitle') ?></label>
		<?php

		$field = 'bookings_show_user_recurring';
		$value = set_value($field, element($field, $settings, '0'), FALSE);
		echo form_hidden($field, '0');
		$input = form_checkbox(array(
			'name' => $field,
			'id' => $field,
			'value' => '1',
			'tabindex' => tab_index(),
			'checked' => ($value == '1')
		));
		echo "<label for='{$field}' class='ni'>{$input} ".$this->lang->line('txtShowUsers1')."</label>";

		$field = 'bookings_show_user_single';
		$value = set_value($field, element($field, $settings, '0'), FALSE);
		echo form_hidden($field, '0');
		$input = form_checkbox(array(
			'name' => $field,
			'id' => $field,
			'value' => '1',
			'tabindex' => tab_index(),
			'checked' => ($value == '1')
		));
		echo "<label for='{$field}' class='ni'>{$input} ".$this->lang->line('txtShowUsers2')."</label>";
		?>

		<p class="hint"><?= $this->lang->line('BookingsHint6') ?></p>
		<p class="hint"><?= $this->lang->line('BookingsHint7') ?></p>

	</p>

</fieldset>




<fieldset>

	<legend accesskey="D" tabindex="<?php echo tab_index() ?>"><?= $this->lang->line('DateTimeTitle') ?></legend>


	<div style="padding: 16px 0;">
		<?= $this->lang->line('txtDatasPHP') ?> - <a href="https://www.php.net/manual/en/function.date.php#refsect1-function.date-parameters" target="_blank">ver referência</a>.
	</div>

	<p>
		<label for="timezone"><?= $this->lang->line('Timezone') ?></label>
		<?php
		$value = set_value('timezone', element('timezone', $settings, date_default_timezone_get()), FALSE);
		$input = form_dropdown([
			'name' => 'timezone',
			'id' => 'timezone',
			'options' => $timezones,
			'selected' => $value,
			'tabindex' => tab_index(),
			'up-autocomplete' => '',
		]);
		echo "<span style='display:inline-block;width:100%;max-width:320px;position:relative;background:transparent'>{$input}</span>";
		?>
		<p></p>
	</p>
	<?php echo form_error('timezone') ?>

	<p>
		<label for="date_format_long"><?= $this->lang->line('Longdateformat') ?></label>
		<?php
		$value = set_value('date_format_long', element('date_format_long', $settings), FALSE);
		echo form_input(array(
			'name' => 'date_format_long',
			'id' => 'date_format_long',
			'size' => '15',
			'maxlength' => '10',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
		<p class="hint"><?= $this->lang->line('txtFormatoData') ?></p>
	</p>
	<?php echo form_error('date_format_long') ?>

	<p>
		<label for="date_format_weekday"><?= $this->lang->line('Weekday date format') ?></label>
		<?php
		$value = set_value('date_format_weekday', element('date_format_weekday', $settings), FALSE);
		echo form_input(array(
			'name' => 'date_format_weekday',
			'id' => 'date_format_weekday',
			'size' => '15',
			'maxlength' => '10',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
		<p class="hint"><?= $this->lang->line('txtFormatoDataAbreviada') ?></p>
	</p>
	<?php echo form_error('date_format_weekday') ?>

	<p>
		<label for="time_format_period"><?= $this->lang->line('Periodtimeformat') ?></label>
		<?php
		$value = set_value('time_format_period', element('time_format_period', $settings), FALSE);
		echo form_input(array(
			'name' => 'time_format_period',
			'id' => 'time_format_period',
			'size' => '15',
			'maxlength' => '10',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
		<p class="hint"><?= $this->lang->line('Timeformatforperiods') ?>.</p>
	</p>
	<?php echo form_error('time_format_period') ?>


</fieldset>


<fieldset>

	<legend accesskey="L" tabindex="<?php echo tab_index() ?>"><?= $this->lang->line('LoginMessage') ?></legend>

	<div><?= $this->lang->line('txtMsgLogin') ?></div>

	<?php
	$field = 'login_message_enabled';
	$value = set_value($field, element($field, $settings, '0'), FALSE);
	?>
	<p>
		<label for="<?= $field ?>"><?= $this->lang->line('Enable') ?></label>
		<?php
		echo form_hidden($field, '0');
		echo form_checkbox(array(
			'name' => $field,
			'id' => $field,
			'value' => '1',
			'tabindex' => tab_index(),
			'checked' => ($value == '1')
		));
		?>
	</p>

	<?php
	$field = 'login_message_text';
	$value = set_value($field, element($field, $settings, ''), FALSE);
	?>
	<p>
		<label for="<?= $field ?>"><?= $this->lang->line('Message') ?></label>
		<?php
		echo form_textarea(array(
			'name' => $field,
			'id' => $field,
			'rows' => '5',
			'cols' => '60',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
	</p>
	<?php echo form_error($field) ?>

</fieldset>

<fieldset>

	<legend accesskey="M" tabindex="<?php echo tab_index() ?>"><?= $this->lang->line('MaintenanceMode') ?></legend>

	<div><?= $this->lang->line('txtMaintanance') ?></div>

	<p>
		<label for="maintenance_mode"><?= $this->lang->line('Enable') ?></label>
		<?php
		$value = set_value('maintenance_mode', element('maintenance_mode', $settings, '0'), FALSE);
		echo form_hidden('maintenance_mode', '0');
		echo form_checkbox(array(
			'name' => 'maintenance_mode',
			'id' => 'maintenance_mode',
			'value' => '1',
			'tabindex' => tab_index(),
			'checked' => ($value == '1')
		));
		?>
	</p>


	<p>
		<label for="maintenance_mode_message"><?= $this->lang->line('Message') ?></label>
		<?php
		$field = 'maintenance_mode_message';
		$value = set_value($field, element($field, $settings, ''), FALSE);
		echo form_textarea(array(
			'name' => $field,
			'id' => $field,
			'rows' => '5',
			'cols' => '60',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
		<p class="hint"><?= $this->lang->line('msgMaintenanceMode') ?></p>
	</p>
	<?php echo form_error($field) ?> 

</fieldset>



<?php

$this->load->view('partials/submit', array(
	'submit' => array('Save', tab_index()),
	'cancel' => array('Cancel', tab_index(), 'controlpanel'),
));

echo form_close();
