<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function error_request()
    {
        $result = [
            'status_code' => '400',
            'status_text' => 'Bad Request'
        ];

        return response()->json($result, 400);
    }

    public function error_notfound()
    {
        $result = [
            'status_code' => '404',
            'status_text' => 'Not Found'
        ];

        return response()->json($result, 404);
    }
    
    public function result_success($data)
    {
        $result = [
            'status_code' => '200',
            'status_text' => 'Success',
            'data' => $data,
        ];

        return response()->json($result, 200);
    }
}
