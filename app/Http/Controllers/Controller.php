<?php

namespace App\Http\Controllers;

use App\Traits\LogUtils;
use Illuminate\Contracts\Support\MessageBag;
use League\Fractal\Manager;
use Illuminate\Http\Response;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use Laravel\Lumen\Http\ResponseFactory;
use Illuminate\Pagination\AbstractPaginator;
use Laravel\Lumen\Routing\Controller as BaseController;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class Controller extends BaseController
{
    use LogUtils;

    protected $statusCode = Response::HTTP_OK;
    private $responseFactory;
    private $fractal;

    public function __construct(Manager $fractal)
    {
        $this->responseFactory = new ResponseFactory;
        $this->fractal = $fractal;
    }

    /**
     * Getter for statusCode
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Setter for statusCode
     *
     * @param int $statusCode Value to set
     *
     * @return self
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function respondWithItem($item, $callback, array $includes = [])
    {
        $resource = new Item($item, $callback);

        $rootScope = $this->fractal->parseIncludes($includes)->createData($resource);

        return $this->respondWithArray($rootScope->toArray());
    }

    public function respondWithArray(array $array, array $headers = [])
    {
        return $this->responseFactory->json($array, $this->statusCode, $headers);
    }

    public function respondWithPaginatedCollection(AbstractPaginator $paginator, $callback, array $includes = [])
    {
        $resource = new Collection($paginator->getCollection(), $callback);

        $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));

        $rootScope = $this->fractal->parseIncludes($includes)->createData($resource);

        return $this->respondWithArray($rootScope->toArray());
    }

    public function respondSuccess($message = "Successful")
    {
        return $this->setStatusCode(Response::HTTP_OK)
            ->respondWithArray([
                'data' => [
                    'status' => true,
                    'message' => $message
                ]
            ]);
    }

    public function respondWithError($message, array $extraData = [])
    {
        if ($this->getStatusCode() === Response::HTTP_OK) {
            trigger_error(
                "Errors Shouldn't be returned when status code is 200",
                E_USER_WARNING
            );
        }

        return $this->respondWithArray([
            'error' => array_merge([
                'http_code' => $this->statusCode,
                'message' => $message,
            ], $extraData)
        ]);
    }

    /**
     * Generates a Response with a 500 HTTP header and a given message.
     *
     * @param string $message
     * @return Response
     */
    public function errorInternalError($message = 'Internal Error')
    {
        return $this->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->respondWithError($message);
    }

    /**
     * Generates a Response with a 404 HTTP header and a given message.
     *
     * @param string $message
     * @return Response
     */
    public function errorNotFound($message = 'Resource Not Found')
    {
        return $this->setStatusCode(Response::HTTP_NOT_FOUND)
            ->respondWithError($message);
    }

    /**
     * Generates a Response with a 401 HTTP header and a given message.
     *
     * @param string $message
     * @return Response
     */
    public function errorUnauthorized($message = 'Unauthorized')
    {
        return $this->setStatusCode(Response::HTTP_UNAUTHORIZED)
            ->respondWithError($message);
    }

    /**
     * Generates a Response with a 400 HTTP header and a given message.
     * @param MessageBag $errors
     * @return Response
     */
    public function errorWrongArgs(MessageBag $errors)
    {
        $errors = array_map(
            function ($error) {
                return [
                    'message'   => $error
                ];
            },
            $errors->toArray()
        );

        $validationErrors = [
            'validation_errors' => $errors
        ];

        return $this->setStatusCode(Response::HTTP_BAD_REQUEST)
            ->respondWithError("Wrong Argument", $validationErrors);
    }
}
