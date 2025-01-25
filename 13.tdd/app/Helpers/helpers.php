<?php

function jsonResponse($data = [], $status = 200, $message = 'OK', $errors = [])
{
    return response()->json(compact('data', 'status', 'message', 'errors'), $status);
}
