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
            // Store error without translation
            return $this->persistAndBuildResult(
                $user,
                $usage,
                $file,
                null,       // English response (null)
                null,       // Arabic response (null)
                500,
                $e->getMessage(),
                'en'
            );
        }

        // 3. Decode and enhance the response
        $decodedResponse = $response->json();
        if (!is_array($decodedResponse)) {
            $decodedResponse = ['raw' => $response->body()];
        }

        // Always divide calories by 10 (business rule)
        $dividedEnglish = $this->divideCaloriesByTen($decodedResponse);

        // 4. Determine if Arabic translation is needed
        $shouldTranslate = str_starts_with($locale, 'ar');
        $arabicResponse = null;

        if ($shouldTranslate) {
            $arabicResponse = $this->translateResponseToLanguage($dividedEnglish, 'ar');
            // Also translate the top-level message for the client
            if (isset($dividedEnglish['message']) && is_string($dividedEnglish['message'])) {
                $arabicResponse['message'] = $this->translateText($dividedEnglish['message'], new GoogleTranslate('ar'));
            }
        }

        // 5. Persist both versions (English and Arabic if translated)
        if (!$response->successful()) {
            return $this->persistAndBuildResult(
                $user,
                $usage,
                $file,
                $dividedEnglish,
                $arabicResponse,
                $response->status(),
                data_get($decodedResponse, 'message', $response->body() ?: 'Food recognition request failed.'),
                $shouldTranslate ? 'ar' : 'en'
            );
        }

        return $this->persistAndBuildResult(
            $user,
            $usage,
            $file,
            $dividedEnglish,
            $arabicResponse,
            200,
            null,
            $shouldTranslate ? 'ar' : 'en'
        );
    }

    /**
     * Persist the analysis result and build the final response array.
     *
     * @param User $user
     * @param FoodAnalysisUsage $usage
     * @param UploadedFile $file
     * @param array|null $englishResponse   // Divided, untranslated
     * @param array|null $arabicResponse    // Divided, translated (nullable)
     * @param int $httpStatus
     * @param string|null $errorMessage
     * @param string $lang                  // The language requested ('en' or 'ar')
     * @return array
     */
    protected function persistAndBuildResult(
        User $user,
        FoodAnalysisUsage $usage,
        UploadedFile $file,
        ?array $englishResponse,
        ?array $arabicResponse,
        int $httpStatus,
        ?string $errorMessage = null,
        string $lang = 'en'
    ): array {
        $topItem = data_get($englishResponse, 'results.0.items.0', []);
        $nutrition = data_get($topItem, 'nutrition', []);
        $isSuccess = $httpStatus >= 200 && $httpStatus < 300;

        $history = DB::transaction(function () use (
            $user,
            $usage,
            $file,
            $englishResponse,
            $arabicResponse,
            $topItem,
            $nutrition,
            $isSuccess,
            $lang
        ) {
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
                'user_id'          => $user->id,
                'provider'         => 'caloriemama',
                'is_food'          => data_get($englishResponse, 'is_food'),
                'top_food_name'    => data_get($topItem, 'name'),
                'top_group'        => data_get($topItem, 'group'),
                'top_score'        => data_get($topItem, 'score'),
                'calories'         => data_get($nutrition, 'calories'),
                'protein'          => data_get($nutrition, 'protein'),
                'total_fat'        => data_get($nutrition, 'totalFat'),
                'total_carbs'      => data_get($nutrition, 'totalCarbs'),
                'response_json'    => $englishResponse,  // Always English, divided
                'response_json_ar' => $arabicResponse,   // Arabic, divided (or null)
                'lang'             => $lang,             // The requested language
                'status'           => $isSuccess ? FoodAnalysisRequest::STATUS_SUCCESS : FoodAnalysisRequest::STATUS_FAILED,
            ]);

            storeMediaFile($history, $file, 'food_recognition_image');

            $lockedUsage->increment('used');

            return $history;
        });

        $freshUsage = $usage->fresh();

        // Choose which version to return to the client based on the requested language
        $clientData = ($lang === 'ar' && $arabicResponse) ? $arabicResponse : $englishResponse;

        $result = [
            'history'            => $history,
            'remaining_requests' => max(0, $freshUsage->daily_limit - $freshUsage->used),
            'api_data'           => $clientData,
            'success'            => $isSuccess,
            'http_status'        => $httpStatus,
        ];

        if (!$isSuccess && $errorMessage) {
            // If Arabic was requested, translate the error message too
            if ($lang === 'ar') {
                $result['message'] = $this->translateText($errorMessage, new GoogleTranslate('ar'));
            } else {
                $result['message'] = $errorMessage;
            }
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
     * Translate relevant fields (name, group, message) to a target language.
     */
    private function translateResponseToLanguage(array $data, string $targetLocale): array
    {
        $translator = new GoogleTranslate($targetLocale);

        // Translate top‑level message if present
        if (isset($data['message']) && is_string($data['message'])) {
            $data['message'] = $this->translateText($data['message'], $translator);
        }

        // Set the language identifier
        if (isset($data['lang']) && is_string($data['lang'])) {
            $data['lang'] = $targetLocale;
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