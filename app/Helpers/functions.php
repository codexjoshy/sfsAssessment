<?php
function respondWithSuccess($message, $data = [])
{
    return response()->json([
        'status' => 'success',
        'message' => $message,
        'data' => $data
    ]);
}

function respondWithError()
{
    return response()->json([
        'status' => 'error',
        'message' => 'Something went wrong.'
    ]);
}