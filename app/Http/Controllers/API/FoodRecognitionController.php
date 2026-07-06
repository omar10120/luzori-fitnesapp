<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\FoodRecognitionRequest;
use App\Http\Resources\FoodRecognitionResource;
use App\Services\FoodRecognitionService;

class FoodRecognitionController extends Controller
{
    public function recognize(FoodRecognitionRequest $request, FoodRecognitionService $service)
    {
        
        $result = $service->recognize(auth()->user(), $request->file('media'));

        $statusCode = $result['success'] ? 200 : ($result['http_status'] ?? 500);

        return json_custom_response(
            new FoodRecognitionResource($result),
            $statusCode
        );
    }
}
