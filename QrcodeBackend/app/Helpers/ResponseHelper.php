<?php
namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    /**
     * Prepare success response
     *
     * @param string $apiStatus
     * @param string $apiMessage
     * @param array $apiData
     * @return Illuminate\Http\JsonResponse
     */
    public function success(
        string $apiStatus = '',
        string $apiMessage = '',
        array $apiData = []
    ): JsonResponse {
        $response['status'] = $apiStatus;

        if (!empty($apiData)) {
            $response['data'] = $apiData;
        }

        if ($apiMessage) {
            $response['message'] = $apiMessage;
        }

        return response()->json($response, $apiStatus, []);
    }

    /**
     * Prepare error response
     *
     * @param string $statusCode
     * @param string $statusType
     * @param string $customErrorCode
     * @param string $customErrorMessage
     * @return Illuminate\Http\JsonResponse
     */
    public function error(
        string $statusCode = '',
        string $statusType = '',
        string $customErrorCode = '',
        string $customErrorMessage = ''
    ): JsonResponse {
        $response['status'] = $statusCode;
        $response['type'] = $statusType;
        if ($customErrorCode) {
            $response['code'] = $customErrorCode;
        }
        $response['message'] = $customErrorMessage;
        $data["errors"][] = $response;

        return response()->json($data, $statusCode, []);
    }
}
