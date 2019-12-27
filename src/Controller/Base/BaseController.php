<?php


namespace App\Controller\Base;


use App\Others\DataKeeper;
use App\Service\BaseService;
use App\Service\SimpleTokenService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

abstract class BaseController extends AbstractController
{
    /**
     * @var BaseService
     */
    protected $service;

    private $simpleTokenService;

    public function __construct(SimpleTokenService $simpleTokenService)
    {
        $this->simpleTokenService = $simpleTokenService;
    }

    /**
     * @param Request $request
     * @return array | null
     */
    protected function getData(Request $request)
    {
        return json_decode($request->getContent(), true);
    }

    protected function createJsonErrorResponse(): JsonResponse
    {
        return $this->json(
            $this->service->createJsonErrorResponse()
        );
    }

    protected function createJsonResponse(?array $data): JsonResponse
    {
        return $this->json($data);
    }

    protected function getField(array $data, string $fieldName)
    {
        if (isset($data[$fieldName])) {
            return $data[$fieldName];
        } else {
            return null;
        }
    }

    private function getSomeField(array $data, string $fieldName, $defaultValue)
    {
        if (isset($data[$fieldName])) {
            return $data[$fieldName];
        } else {
            return $defaultValue;
        }
    }

    protected function getStringField(array $data, string $fieldName): string
    {

        if (isset($data[$fieldName]) && is_string($data[$fieldName])) {
            return $data[$fieldName];
        } else {
            return '';
        }
    }


    protected function getIntField(array $data, string $fieldName): int
    {

        if (isset($data[$fieldName]) && is_int($data[$fieldName])) {
            return $data[$fieldName];
        } else {
            return 0;
        }
    }

    /**
     * @todo Make auth first, then handle data
     * @param Request $request
     * @param array $data
     * @param $isAuthRequired
     * @param callable $dataHandler
     * @return JsonResponse
     */
    private function handleData(Request $request, array $data, $isAuthRequired, callable $dataHandler) {
        $token = $request->headers->get('Authorization');
        if (!empty($token)) {
            $token = str_replace('Simple ', '', $token);
        } else if ($isAuthRequired) {
            return $this->createJsonResponse($this->simpleTokenService->createResponse(['Invalid token'], BaseService::RESPONSE_CODE_FAIL_AUTHORIZATION));
        }
        if ($isAuthRequired) {
            $errors = $this->simpleTokenService->checkTokenForErrors($token);
            if (!empty($errors)) {
                return $this->createJsonResponse($this->simpleTokenService->createResponse($errors[1], $errors[0]));
            }
        }
        $result = $dataHandler(new DataKeeper($data), $token);
        return $this->createJsonResponse($result);

    }

    /**
     * @param Request $request
     * @param bool $isAuthRequired
     * @param callable(PostDataKeeper, string) $dataHandler
     * @return JsonResponse
     */
    public function handlePostData(Request $request, bool $isAuthRequired, callable $dataHandler): JsonResponse
    {
        $data = $this->getData($request);
        $token = null;
        if ($data === null) {
            return $this->createJsonErrorResponse();
        }
        $data = array_merge($data, $request->query->all());
        return $this->handleData($request, $data, $isAuthRequired, $dataHandler);
    }

    /**
     * @param Request $request
     * @param bool $isAuthRequired
     * @param callable $dataHandler
     * @return JsonResponse
     */
    public function handleGetData(Request $request, bool $isAuthRequired, callable $dataHandler): JsonResponse
    {
        return $this->handleData($request, $request->query->all(), $isAuthRequired, $dataHandler);
    }
}