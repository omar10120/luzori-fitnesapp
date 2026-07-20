<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\FoodRecognitionRequest;
use App\Http\Resources\FoodAnalysisRequestResource;
use App\Models\FoodAnalysisRequest;
use App\Http\Resources\FoodRecognitionResource;
use App\Services\FoodRecognitionService;
use Illuminate\Http\Request;

class FoodRecognitionController extends Controller
{
    public function recognize(FoodRecognitionRequest $request, FoodRecognitionService $service)
    {
        $locale = $request->header('Accept-Language', 'en');
        $result = $service->recognize(auth()->user(), $request->file('media'), $locale);

        $statusCode = $result['success'] ? 200 : ($result['http_status'] ?? 500);

        return json_custom_response(
            new FoodRecognitionResource($result),
            $statusCode
        );
    }

    public function getList(Request $request)
    {
        $locale = $request->header('Accept-Language', 'en');
        $isArabic = str_starts_with($locale, 'ar');
    
        $histories = FoodAnalysisRequest::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();
    
        $histories->transform(function ($item) use ($isArabic) {
            // Use the appropriate JSON column
            $data = $isArabic && $item->response_json_ar
                ? $item->response_json_ar
                : $item->response_json;
    
            // Build your response array, e.g.:
            return [
                'id'                => $item->id,
                'top_food_name'     => $isArabic && $item->response_json_ar
                                        ? data_get($item->response_json_ar, 'results.0.items.0.name')
                                        : $item->top_food_name,
                // ... other fields, maybe use the data array directly
                'api_data'          => $data,
            ];
        });
    
        return response()->json(['success' => true, 'data' => $histories]);
    }
    public function getDetail(Request $request)
    {
        $locale = $request->header('Accept-Language', 'en');
        
        $isArabic = str_starts_with($locale, 'ar');
        $history = FoodAnalysisRequest::where('user_id', $request->user()->id)
            ->where('id', $request->id)
            ->first();

        if ($history == null) {
            return json_message_response(__('message.not_found_entry', ['name' => __('message.data')]));
        }

        return json_custom_response([
            'data' => new FoodAnalysisRequestResource($history, $isArabic),
        ]);
    }
    public function getAssignFoodRecognitionList()
    {
        $foodRecognition = FoodRecognition::where('user_id', auth()->id())->orderBy('id', 'desc')->paginate(10);

        $items = FoodRecognitionResource::collection($foodRecognition);

        $response = [
            'pagination'    => json_pagination_response($items),
            'data'          => $items,
        ];

        return json_custom_response($response);
    }
}
