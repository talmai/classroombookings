<?php

namespace app\components\bookings\exceptions;


class BookingValidationException extends \RuntimeException
{


	public static function forExistingBooking()
	{
		//return new static("Another booking already exists.");
		return new static("Já existe outra reserva.");
	}


	public static function forHoliday()
	{
		//return new static("Booking cannot be created on a holiday.");
		return new static("Uma reserva não pode ser criada em um feriado ou recesso.");
	}


}
