<?php

namespace App\Services;

use App\Models\FoodAnalysisRequest;
use App\Models\FoodAnalysisUsage;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Subscription;

class FoodRecognitionService
{
    public function recognize(User $user, UploadedFile $file): array
    {
        $today = now()->toDateString();
        $dailyLimit = (int) config('caloriemama.daily_limit', 5);

        $usage = FoodAnalysisUsage::firstOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            ['used' => 0, 'daily_limit' => $dailyLimit]
        );

        if ($usage->used >= $usage->daily_limit) {
            throw new HttpResponseException(
                json_custom_response([
                    'success' => false,
                    'message' => 'Daily food recognition limit reached.',
                ], 429)
            );
        }

        $url = config('caloriemama.url') . '?user_key=' . config('caloriemama.key');

        try {
            $response = Http::attach(
                'media',
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName()
            )
                ->timeout(30)
                ->retry(2, 500)
                ->post($url);
        } catch (\Throwable $e) {
            Log::error('Food recognition request failed: ' . $e->getMessage());
            // No division needed here because there is no response data
            return $this->persistAndBuildResult($user, $usage, $file, null, 500, $e->getMessage());
        }

        $decodedResponse = $response->json();
        if (!is_array($decodedResponse)) {
            $decodedResponse = ['raw' => $response->body()];
        } else {
            // Divide calories ONCE before storing and returning
            $decodedResponse = $this->divideCaloriesByTen($decodedResponse);
        }

        if (!$response->successful()) {
            return $this->persistAndBuildResult(
                $user,
                $usage,
                $file,
                $decodedResponse,
                $response->status(),
                data_get($decodedResponse, 'message', $response->body() ?: 'Food recognition request failed.')
            );
        }

        // Success: the returned result already contains the divided api_data
        return $this->persistAndBuildResult($user, $usage, $file, $decodedResponse, 200);
    }

    protected function persistAndBuildResult(
        User $user,
        FoodAnalysisUsage $usage,
        UploadedFile $file,
        ?array $decodedResponse,
        int $httpStatus,
        ?string $errorMessage = null
    ): array {
        $topItem = data_get($decodedResponse, 'results.0.items.0', []);
        $nutrition = data_get($topItem, 'nutrition', []);
        $isSuccess = $httpStatus >= 200 && $httpStatus < 300;

        $history = DB::transaction(function () use ($user, $usage, $file, $decodedResponse, $topItem, $nutrition, $isSuccess) {
            $lockedUsage = FoodAnalysisUsage::where('id', $usage->id)->lockForUpdate()->first();

            if ($lockedUsage->used >= $lockedUsage->daily_limit) {
                throw new HttpResponseException(
                    json_custom_response([
                        'success' => false,
                        'message' => 'Daily food recognition limit reached.',
                    ], 429)
                );
            }

            $history = FoodAnalysisRequest::create([
                'user_id'       => $user->id,
                'provider'      => 'caloriemama',
                'is_food'       => data_get($decodedResponse, 'is_food'),
                'top_food_name' => data_get($topItem, 'name'),
                'top_group'     => data_get($topItem, 'group'),
                'top_score'     => data_get($topItem, 'score'),
                'calories'      => data_get($nutrition, 'calories'),
                'protein'       => data_get($nutrition, 'protein'),
                'total_fat'     => data_get($nutrition, 'totalFat'),
                'total_carbs'   => data_get($nutrition, 'totalCarbs'),
                'response_json' => $decodedResponse,
                'status'        => $isSuccess ? FoodAnalysisRequest::STATUS_SUCCESS : FoodAnalysisRequest::STATUS_FAILED,
            ]);

            storeMediaFile($history, $file, 'food_recognition_image');

            $lockedUsage->increment('used');

            return $history;
        });

        $freshUsage = $usage->fresh();

        $result = [
            'history'            => $history,
            'remaining_requests' => max(0, $freshUsage->daily_limit - $freshUsage->used),
            'api_data'           => $decodedResponse,
            'success'            => $isSuccess,
            'http_status'        => $httpStatus,
        ];

        if (!$isSuccess && $errorMessage) {
            $result['message'] = $errorMessage;
        }

        return $result;
    }

        /**
     * Recursively divide all 'calories' values by 10.
     */
    private function divideCaloriesByTen(array $data): array
    {
        array_walk_recursive($data, function (&$value, $key) {
            if ($key === 'calories' && is_numeric($value)) {
                $value = (float) $value / 10;
            }
        });
        return $data;
    }
}
