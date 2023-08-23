<?php

echo $this->session->flashdata('saved');

$this->lang->load('users');
if ( !($this->lang->line('Type')) ) {
	$this->lang->load('custom'); 
}

$iconbar = iconbar(array(
	array('users/add', $this->lang->line('AddUser'), 'add.png'),
	array('users/import', $this->lang->line('ImportUsers'), 'user_import.png'),
));

echo $iconbar;

$this->load->view('users/filter');

$sort_cols = ["Type", "Enabled", "Username", "Display Name", "Last Login", "Actions"];

?>

<div id="users_list">

	<table width="100%" cellpadding="2" cellspacing="2" border="0" class="zebra-table sort-table" up-data='<?= json_encode($sort_cols) ?>'>
		<col /><col /><col /><col />
		<thead>
		<tr class="heading">
			<td width="7%" class="h" title="Type"><?= $this->lang->line('Type') ?></td>
			<td width="8%" class="h" title="Enabled"><?= $this->lang->line('Enabled') ?></td>
			<td width="20%" class="h" title="Username"><?= $this->lang->line('Username') ?></td>
			<td width="20%" class="h" title="Name"><?= $this->lang->line('Displayname') ?></td>
			<td width="20%" class="h" title="Lastlogin"><?= $this->lang->line('Lastlogin') ?></td>
			<td width="5%" class="n" title="X"></td>
		</tr>
		</thead>
		<tbody>
		<?php
		$i=0;
		if ($users) {
		foreach ($users as $user) { ?>
		<tr>
			<?php
			$img_type = ($user->authlevel == ADMINISTRATOR ? 'user_administrator.png' : 'user_teacher.png');
			$img_enabled = ($user->enabled == 1) ? 'enabled.png' : 'no.png';
			?>
			<td width="50" align="center"><img src="<?= base_url("assets/images/ui/{$img_type}") ?>" width="16" height="16"  alt="<?php echo $img_type ?>" /></td>
			<td width="70" align="center"><img src="<?= base_url("assets/images/ui/{$img_enabled}") ?>" width="16" height="16"  alt="<?php echo $img_enabled ?>" /></td>
			<td><?php echo html_escape($user->username) ?></td>
			<td><?php
			if( $user->displayname == '' ){ $user->displayname = $user->username; }
			echo html_escape($user->displayname);
			?></td>
			<td><?php
			if($user->lastlogin == '0000-00-00 00:00:00' || empty($user->lastlogin)){
				$lastlogin = $this->lang->line('Never');
			} else {
				$lastlogin = date("d/m/Y, H:i", strtotime($user->lastlogin));
			}
			echo $lastlogin;
			?></td>
			<td width="45" class="n"><?php
				$actions['edit'] = 'users/edit/'.$user->user_id;
				$actions['delete'] = 'users/delete/'.$user->user_id;
				$this->load->view('partials/editdelete', $actions);
				?>
			</td>
		</tr>
		<?php $i++; } } ?>
		</tbody>
	</table>

	<?= $pagelinks ?>

</div>

<?php

echo $iconbar;
