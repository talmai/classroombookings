<?php
if ( !($this->lang->line('AddRooms')) ) {
	$this->lang->load('rooms');
}
?>
<dl>
  <dt><?= $this->lang->line('RoomInformation') ?></dt>
  <dd><?= $this->lang->line('msgRoomInformation') ?></dd>
</dl>
