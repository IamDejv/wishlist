<?php
declare(strict_types=1);

namespace App\Controller\Auth;

use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\Enum\ResponseEnum;
use App\Helpers\ResponseHelper;
use App\Model\Repository\ImageRepository;

/**
 * @Path("/images")
 */
class ImageController extends BaseAuthController
{
	public function __construct(private ImageRepository $repository)
	{
	}

	/**
	 * @Path("/")
	 * @Method("GET")
	 *
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function listAll(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		return $response
			->withAttribute(ResponseEnum::MULTIPLE, $this->repository->findAll())
			->withStatus(ResponseHelper::OK);
	}
}
