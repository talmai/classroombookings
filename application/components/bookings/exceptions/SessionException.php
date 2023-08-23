<?php

namespace app\components\bookings\exceptions;


class SessionException extends \RuntimeException
{


	public static function notSelected()
	{
		// $lang['no_active_session']); // ("No active Session found.");
		return new static("Nenhuma sessão ativa encontrada."); 
	}


}
