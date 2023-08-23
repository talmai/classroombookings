<?php
if ( !($this->lang->line('ManageUsersTitle')) ) {
	$this->lang->load('users'); 
}
?>
<style>
pre {
	background: #f4f4f4;
	padding: 10px;
}
</style>

<div>
	<h5><?= $this->lang->line('CSVformat') ?></h5>
	<p><?= $this->lang->line('txtCSVformat') ?></p>
</div>
<br>
