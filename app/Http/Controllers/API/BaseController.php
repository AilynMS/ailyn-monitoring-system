<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller as Controller;

class BaseController extends Controller
{
    /**
     * Success response method.
     *
     * @param mixed|array $result
     * @param string $message
     *
     *  @return \Illuminate\Http\JsonResponse
     */
    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'data' => $result,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }

    /**
     * Return a collection resource with additional data
     *
     * @param \Illuminate\Http\Resources\Json\ResourceCollection $resource
     * @param string $message
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function sendCollectionResource($resource, $message)
    {
        return $resource->additional([
            'message' => $message,
        ]);
    }

    /**
     * Return error response.
     *
     * @param string $error The general error message
     * @param array $errorMessages Specific error messages
     * @param int $code HTTP error code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (! empty($errorMessages)) {
            $response['errors'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}
