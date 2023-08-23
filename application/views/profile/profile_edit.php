<?php
$this->lang->load('profile');
echo $this->session->flashdata('saved');

echo form_open('profile/save', array('class' => 'cssform', 'id' => 'profile_edit'));

?>


<fieldset>

	<legend accesskey="U" tabindex="<?php tab_index() ?>"><?php echo $this->lang->line('Userdetails'); ?></legend>

	<p>
	  <label for="email" class="required"><?php echo $this->lang->line('Emailaddress'); ?></label>
	  <?php
		$email = set_value('email', $user->email, FALSE);
		echo form_input(array(
			'name' => 'email',
			'id' => 'email',
			'size' => '35',
			'maxlength' => '255',
			'tabindex' =>tab_index(),
			'value' => $email,
		));
		?>
	</p>
	<?php echo form_error('email'); ?>


	<p>
	  <label for="password1"><?php echo $this->lang->line('Password'); ?></label>
	  <?php
		echo form_password(array(
			'name' => 'password1',
			'id' => 'password1',
			'size' => '20',
			'maxlength' => '40',
			'tabindex' => tab_index(),
			'value' => '',
		));
		?>
	</p>
	<?php echo form_error('password1'); ?>


	<p>
	  <label for="password2"><?php echo $this->lang->line('Password'); ?> (<?php echo $this->lang->line('again'); ?>)</label>
	  <?php
		echo form_password(array(
			'name' => 'password2',
			'id' => 'password2',
			'size' => '20',
			'maxlength' => '40',
			'tabindex' => tab_index(),
			'value' => '',
		));
		?>
	</p>
	<?php echo form_error('password2'); ?>


</fieldset>


<fieldset>


	<p>
	  <label for="firstname"><?php echo $this->lang->line('Firstname'); ?></label>
	  <?php
		$firstname = set_value('firstname', $user->firstname, FALSE);
		echo form_input(array(
			'name' => 'firstname',
			'id' => 'firstname',
			'size' => '20',
			'maxlength' => '20',
			'tabindex' => tab_index(),
			'value' => $firstname,
		));
		?>
	</p>
	<?php echo form_error('firstname'); ?>


	<p>
	  <label for="lastname"><?php echo $this->lang->line('Lastname'); ?></label>
	  <?php
		$lastname = set_value('lastname', $user->lastname, FALSE);
		echo form_input(array(
			'name' => 'lastname',
			'id' => 'lastname',
			'size' => '20',
			'maxlength' => '20',
			'tabindex' => tab_index(),
			'value' => $lastname,
		));
		?>
	</p>
	<?php echo form_error('lastname'); ?>


	<p>
	  <label for="displayname"><?php echo $this->lang->line('Displayname'); ?></label>
	  <?php
		$displayname = set_value('displayname', $user->displayname, FALSE);
		echo form_input(array(
			'name' => 'displayname',
			'id' => 'displayname',
			'size' => '20',
			'maxlength' => '20',
			'tabindex' => tab_index(),
			'value' => $displayname,
		));
		?>
	</p>
	<?php echo form_error('displayname'); ?>


	<p>
	  <label for="ext"><?php echo $this->lang->line('Extension'); ?></label>
	  <?php
		$ext = set_value('ext', $user->ext, FALSE);
		echo form_input(array(
			'name' => 'ext',
			'id' => 'ext',
			'size' => '10',
			'maxlength' => '10',
			'tabindex' => tab_index(),
			'value' => $ext,
		));
		?>
	</p>
	<?php echo form_error('ext'); ?>


</fieldset>


<?php
$this->load->view('partials/submit', array(
	'submit' => array($this->lang->line('Save'), tab_index()),
));

echo form_close();
