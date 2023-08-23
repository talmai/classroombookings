<?php

namespace app\components\bookings\exceptions;


class DateException extends \RuntimeException
{


	public static function invalidDate($date_string)
	{
		//return new static(sprintf("No date selected or date is not valid (%s).", $date_string));
		return new static(sprintf("Sem data selecionada ou a data é inválida (%s).", $date_string));
	}


	public static function forSessionRange($datetime)
	{
		if ($datetime) {
			$dt = $datetime->format('d/m/Y');
		} else {
			$dt = 'Nenhum'; //'None';
		}

		//return new static(sprintf("The selected date (%s), is not within the current Session.", $dt));
		return new static(sprintf("A data selecionada (%s), não está dentro da Sessão atual.", $dt));
	}


}
