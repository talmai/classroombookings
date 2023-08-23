<?php
echo $this->session->flashdata('saved');
echo form_open_multipart(current_url(), array('id'=>'schooldetails', 'class'=>'cssform'));
?>


<fieldset>

	<legend accesskey="I" tabindex="<?php echo tab_index(); ?>"><?php echo $this->lang->line('SchoolInformation'); ?></legend>

	<p>
		<label for="schoolname" class="required"><?php echo $this->lang->line('SchoolName'); ?></label>
		<?php
		$value = set_value('schoolname', element('name', $settings), FALSE);
		echo form_input(array(
			'name' => 'schoolname',
			'id' => 'schoolname',
			'size' => '30',
			'maxlength' => '255',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
	</p>
	<?php echo form_error('schoolname'); ?>

	<p>
		<label for="website"><?php echo $this->lang->line('Websiteaddress'); ?></label>
		<?php
		$value = set_value('website', element('website', $settings), FALSE);
		echo form_input(array(
			'name' => 'website',
			'id' => 'website',
			'size' => '40',
			'maxlength' => '255',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
	</p>
	<?php echo form_error('website'); ?>

</fieldset>



<fieldset>

	<legend accesskey="L" tabindex="<?php echo tab_index() ?>"><?php echo $this->lang->line('SchoolLogo'); ?></legend>

	<div><?php echo $this->lang->line('SchoolLogoDescription'); ?></div>

	<p>
		<label><?php echo $this->lang->line('Currentlogo'); ?></label>
		<?php
		$logo = element('logo', $settings);
		if ( ! empty($logo) && is_file(FCPATH . 'uploads/' . $logo)) {
			echo img('uploads/' . $logo, FALSE, "style='padding:1px; border:1px solid #ccc; max-width: 300px; width: auto; height: auto'");
		} else {
			echo "<span><em>".$this->lang->line('Nonefound')."</em></span>";
		}
		?>
	</p>

	<p>
		<label for="userfile"><?php echo $this->lang->line('Fileupload'); ?></label>
		<?php
		echo form_upload(array(
			'name' => 'userfile',
			'id' => 'userfile',
			'size' => '25',
			'maxlength' => '255',
			'tabindex' => tab_index(),
			'value' => '',
		));
		?>
		<p class="hint"><?php echo $this->lang->line('msgUpload'); ?></p>
	</p>

	<?php
	if ($this->session->flashdata('image_error') != '') {
		echo "<p class='hint error'><span>" . $this->session->flashdata('image_error') . "</span></p>";
	}
	?>

	<p>
		<label for="logo_delete"><?php echo $this->lang->line('Deletelogomsg'); ?>?</label>
		<?php
		echo form_checkbox(array(
			'name' => 'logo_delete',
			'id' => 'logo_delete',
			'value' => '1',
			'tabindex' => tab_index(),
			'checked' => FALSE,
		));
		?>
		<p class="hint"><?php echo $this->lang->line('Deletelogomsg2'); ?></p>
	</p>

</fieldset>


<?php

$this->load->view('partials/submit', array(
	'submit' => array('Save', tab_index()),
	'cancel' => array('Cancel', tab_index(), 'controlpanel'),
));

echo form_close();
