<?php

function jsonResponse($data = [], $status = 200, $message = 'OK', $error = [])
{
    return response()->json(compact('data', 'status', 'message', 'error'), $status);
}
