<?php
declare(strict_types=1);

namespace App\Controller\Auth;

use App\Controller\BaseV1Controller;
use App\Helpers\ResponseHelper;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Doctrine\ORM\EntityNotFoundException;
use Nette\Security\AuthenticationException;


abstract class BaseAuthController extends BaseV1Controller
{
}
