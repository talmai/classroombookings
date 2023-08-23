<?php

namespace app\components\bookings\exceptions;


class AvailabilityException extends \RuntimeException
{


	public static function forNoWeek()
	{
		//return new static("The selected date is not assigned to a timetable week.");
		return new static("A data selecionada não está atribuída a um horário semanal.");
	}


	public static function forNoPeriods()
	{
		//return new static("There are no periods available for the selected date.");
		return new static("Não há períodos disponíveis para a data selecionada.");
	}


	public static function forHoliday($holiday = NULL)
	{
		if ( ! is_object($holiday)) {
			//return new static('The date you selected is during a holiday.');
			return new static('A data selecionada é durante um feriado / recesso.');
		}

		$format = 'A data selecionada está dentro de um feriado / recesso: %s: %s - %s';
		// 'The date you selected is during a holiday: %s: %s - %s';
		$start = $holiday->date_start->format('d/m/Y');
		$end = $holiday->date_end->format('d/m/Y');
		$str = sprintf($format, $holiday->name, $start, $end);
		return new static($str);
	}


}
