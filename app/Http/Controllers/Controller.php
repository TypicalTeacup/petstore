<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

abstract class Controller
{
    protected function apiResponse(int $code, $body, string $type = 'unknown')
    {
        return response([
            'code' => $code,
            'type' => $type,
            'body' => $body
        ], $code);
    }

    protected function validate($validator, Request $request = null)
    {
        try {
            if ($validator instanceof Validator) {
                $validated = $validator->validate();
            } else {
                $validated = $request->validate($validator);
            }
        } catch (ValidationException $ex) {
            $errors = $ex->validator->errors()->toArray();

            $flatErrors = array_merge(...array_values($errors));
            $message = $flatErrors[0];

            $errorCount = count($flatErrors);
            if ($errorCount > 1) {
                $multiple = $errorCount > 2 ? 's' : '';
                $errorCount--;
                $message .= " (and $errorCount more error$multiple)";
            }

            return $this->apiResponse(405, [
                'message' => $message,
                'errors' => $errors
            ], 'validationException');
        }
        return $validated;
    }

    protected function getCSVQueryParam(Request $request, $param, $unique = false)
    {
        $result = $request->query($param);

        if (!is_array($result)) {
            $result = [$result];
        }
        $result = explode(',', implode(',', $result));

        if ($unique) {
            $result = array_unique($result);
        }

        return $result;
    }
}
