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
use Stichoza\GoogleTranslate\GoogleTranslate;

class FoodRecognitionService
{
    /**
     * Recognize food from an uploaded image.
    *
    * @param User $user
    * @param UploadedFile $file
    * @param string $locale  // e.g., 'en' or 'ar'
     * @return array
     */
    public function recognize(User $user, UploadedFile $file, string $locale = 'en'): array
    {
        // 1. Daily limit check
        $today = now()->toDateString();
        $dailyLimit = (int) config('caloriemama.daily_limit', 5);
            // $dailyLimit = Subscription::where('user_id', $user->id)->where('status', 'active')->first()->food_recognition_limit;

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

        // 2. Call third‑party API
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
            // No transformation needed – just store the error
            return $this->persistAndBuildResult($user, $usage, $file, null, 500, $e->getMessage());
        }

        // 3. Decode and enhance the response
        $decodedResponse = $response->json();
        if (!is_array($decodedResponse)) {
            $decodedResponse = ['raw' => $response->body()];
        } else {
            // Apply enhancements: divide calories and translate to Arabic
            $decodedResponse = $this->divideCaloriesByTen($decodedResponse);
            $decodedResponse = $this->translateResponseToArabic($decodedResponse, $locale);
        }

        // 4. Persist and build result (will use the enhanced response)
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

        return $this->persistAndBuildResult($user, $usage, $file, $decodedResponse, 200);
    }

    /**
     * Persist the analysis result and build the final response array.
     */
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
                'response_json' => $decodedResponse, // Enhanced (divided & translated)
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
            'api_data'           => $decodedResponse, // Enhanced data
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

    /**
     * Translate relevant fields (name, group, message) to Arabic.
     */
    private function translateResponseToArabic(array $data, string $locale): array
    {
        $translator = new GoogleTranslate($locale);

        // Translate top‑level message
        if (isset($data['message']) && is_string($data['message'])) {
            $data['message'] = $this->translateText($data['message'], $translator);
        }

        // Set language to Arabic
        if (isset($data['lang']) && is_string($data['lang'])) {
            $data['lang'] = $locale;
        }

        // Translate group names and item names inside 'results'
        if (isset($data['results']) && is_array($data['results'])) {
            foreach ($data['results'] as &$resultGroup) {
                if (isset($resultGroup['group']) && is_string($resultGroup['group'])) {
                    $resultGroup['group'] = $this->translateText($resultGroup['group'], $translator);
                }
                if (isset($resultGroup['items']) && is_array($resultGroup['items'])) {
                    foreach ($resultGroup['items'] as &$item) {
                        if (isset($item['name']) && is_string($item['name'])) {
                            $item['name'] = $this->translateText($item['name'], $translator);
                        }
                        if (isset($item['group']) && is_string($item['group'])) {
                            $item['group'] = $this->translateText($item['group'], $translator);
                        }
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Translate a single text, falling back to original on failure.
     */
    private function translateText(string $text, GoogleTranslate $translator): string
    {
        if (empty($text)) {
            return $text;
        }
        try {
            return $translator->translate($text);
        } catch (\Exception $e) {
            Log::warning("Translation failed for '$text': " . $e->getMessage());
            return $text;
        }
    }
}