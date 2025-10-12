<?php

namespace App\Http\Controllers;

use InfyOm\Generator\Utils\ResponseUtil;

class AppBaseController extends Controller
{
    public function sendResponse($result, $message)
    {
        return response()->json(ResponseUtil::makeResponse($message, $result));
    }

    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['errors'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
    public function sendSuccess($message)
    {
        return response()->json([
            'success' => true,
            'message' => $message
        ], 200);
    }
}
