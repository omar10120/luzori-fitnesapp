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
        
        $result = $service->recognize(auth()->user(), $request->file('media'));

        $statusCode = $result['success'] ? 200 : ($result['http_status'] ?? 500);

        return json_custom_response(
            new FoodRecognitionResource($result),
            $statusCode
        );
    }

    public function getList(Request $request)
    {
        $history = FoodAnalysisRequest::where('user_id', auth()->id());

        $history->when(request('status'), function ($q) {
            return $q->where('status', request('status'));
        });

        $history->when(request('is_food'), function ($q) {
            return $q->where('is_food', request('is_food'));
        });

        $history->when(request('title'), function ($q) {
            return $q->where('top_food_name', 'LIKE', '%' . request('title') . '%');
        });

        $per_page = config('constant.PER_PAGE_LIMIT');
        if ($request->has('per_page') && !empty($request->per_page)) {
            if (is_numeric($request->per_page)) {
                $per_page = $request->per_page;
            }
            if ($request->per_page == -1) {
                $per_page = $history->count();
            }
        }

        $history = $history->orderBy('id', 'desc')->paginate($per_page);

        $items = FoodAnalysisRequestResource::collection($history);

        $response = [
            'pagination'    => json_pagination_response($items),
            'data'          => $items,
        ];

        return json_custom_response($response);
    }

    public function getDetail(Request $request)
    {
        $history = FoodAnalysisRequest::where('user_id', auth()->id())
            ->where('id', $request->id)
            ->first();

        if ($history == null) {
            return json_message_response(__('message.not_found_entry', ['name' => __('message.data')]));
        }

        return json_custom_response([
            'data' => new FoodAnalysisRequestResource($history),
        ]);
    }
}
