<?php
declare(strict_types=1);

namespace App\Security;


use App\Controller\Auth\AttendanceController;
use App\Controller\Auth\EventController;
use App\Controller\Auth\AttendeeController;
use App\Controller\Auth\MeController;
use App\Controller\Auth\SubscribeController;
use App\Controller\Auth\TermController;
use App\Controller\Auth\UsersController;

class ResourceEnum
{
	const USER = UsersController::class;
	const ME = MeController::class;
	const ATTENDEE = AttendeeController::class;
	const EVENT = EventController::class;
	const TERM = TermController::class;
	const ATTENDANCE = AttendanceController::class;
	const SUBSCRIBE = SubscribeController::class;

	/**
	 * @var array [url => class]
	 */
	public static array $all = [
		'users' => self::USER,
		'me' => self::ME,
		'attendee' => self::ATTENDEE,
		'event' => self::EVENT,
		'term' => self::TERM,
		'attendance' => self::ATTENDANCE,
		'subscribe' => self::SUBSCRIBE,
	];
}
