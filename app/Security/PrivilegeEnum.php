<?php
declare(strict_types=1);

namespace App\Security;


class PrivilegeEnum
{
	// Standard API functions
	const API_LIST = 'list';
	const API_CREATE = 'create';
	const API_GET = 'get';
	const API_UPDATE = 'update';
	const API_DELETE = 'delete';

	// UserController privileges
	const LIST_USERS = 'listUsers';
	const LIST_TRAINERS = 'listTrainers';

	// AttendeeController privileges
	const LIST_MY = 'listMy';
	const UPDATE_CARD = 'updateCard';
	const ACTIVATE = 'activate';
	const DISABLE = 'disable';

	// AttendanceController privileges
	const GET_BY_ATTENDEE = 'getByAttendee';
	const EXCUSE = "excuse";
	const CHECK = "check";

	// EventController privileges
	const LIST_TRAININGS = 'listTrainings';
	const LIST_ACTIONS = 'listActions';
	const PUBLISHED_ACTIONS = 'publishedActions';
	const PUBLISHED_TRAININGS = 'publishedTrainings';
	const ASSIGN = 'assign';
	const GET_ATTENDANCE = 'getAttendance';
	const HIDE = 'hide';
	const PUBLISH = 'publish';
	const LIST_PUBLISHED = 'listPublished';

	// TermController privileges
	const DELETE_NOT_MY = "deleteNotMy";
	const EDIT_NOT_MY = "editNotMy";
	const CREATE_NOT_MY = "createNotMy";

	// SubscribeController privileges
	const SUBSCRIBE = "subscribe";
	const UNSUBSCRIBE = "unsubscribe";

}
