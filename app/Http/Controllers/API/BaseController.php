<?php


namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;


class BaseController extends Controller
{
    /**
     * success response method.
     * @
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, string $message)
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];


        return response()->json($response, 200);
    }


    /**
     * return error response.
     * @param  string $error error message
     * @param  array  $errorMessages error message
     * @param  int    $code error code
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError(string $message,  $errorMessages = [], int $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];


        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }


        return response()->json($response, $code);
    }
}