<?php

namespace app\components\bookings\exceptions;


class AgentException extends \RuntimeException
{


	public static function forInvalidType($types)
	{
		//return new static("Unrecognised booking type. Should be one of " . implode(', ', $types));
		return new static("Tipo de reserva não-reconhecida. Deve ser do tipo " . implode(', ', $types));
	}

	public static function forNoSession()
	{
		//return new static('Requested date does not belong to a session.');
		return new static('A data solicitada não pertence a uma sessão.');
	}


	public static function forNoPeriod()
	{
		//return new static('Requested period could not be found.');
		return new static('O período solicitado não foi encontrado.');
	}


	public static function forNoRoom()
	{
		//return new static('Requested room could not be found or is not bookable.');
		return new static('A sala/espaço solicitado não foi encontrado ou não pode ser reservado.');
	}


	public static function forInvalidDate()
	{
		//return new static('Requested date is not recognised or is not bookable.');
		return new static('A data solicitada não foi reconhecida ou não pode ser agendada.');
	}


	public static function forNoWeek()
	{
		//return new static('Requested date is not associated with a timetable week.');
		return new static('A data solicitada não está associada com um horário semanal.');
	}


	public static function forNoBooking()
	{
		//return new static('Requested booking could not be found.');
		return new static('A reserva solicitada não foi encontrada.');
	}


}
