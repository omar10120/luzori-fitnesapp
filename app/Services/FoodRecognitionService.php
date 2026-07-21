<?php

namespace App\Services;

use App\Models\FoodAnalysisRequest;
use App\Models\Subscription;
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
        // $this->checkSubscription($user);

        $subscription = $this->getActiveSubscription($user);
        $this->assertWithinLimit($user, $subscription);

        // Call third‑party API
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
            return $this->persistAndBuildResult(
                $user,
                $subscription,
                $file,
                null,
                null,
                500,
                $e->getMessage(),
                'en'
            );
        }

        $decodedResponse = $response->json();
        if (!is_array($decodedResponse)) {
            $decodedResponse = ['raw' => $response->body()];
        }

        $dividedEnglish = $this->divideCaloriesByTen($decodedResponse);

        $shouldTranslate = str_starts_with($locale, 'ar');
        $arabicResponse = null;

        if ($shouldTranslate) {
            $arabicResponse = $this->translateResponseToLanguage($dividedEnglish, 'ar');
            if (isset($dividedEnglish['message']) && is_string($dividedEnglish['message'])) {
                $arabicResponse['message'] = $this->translateText($dividedEnglish['message'], new GoogleTranslate('ar'));
            }
        }

        if (!$response->successful()) {
            return $this->persistAndBuildResult(
                $user,
                $subscription,
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
            $subscription,
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
     */
    protected function persistAndBuildResult(
        User $user,
        Subscription $subscription,
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
            $subscription,
            $file,
            $englishResponse,
            $arabicResponse,
            $topItem,
            $nutrition,
            $isSuccess,
            $lang
        ) {
            $lockedSubscription = Subscription::where('id', $subscription->id)->lockForUpdate()->first();
            $this->assertWithinLimit($user, $lockedSubscription);

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
                'response_json'    => $englishResponse,
                'response_json_ar' => $arabicResponse,
                'lang'             => $lang,
                'status'           => $isSuccess ? FoodAnalysisRequest::STATUS_SUCCESS : FoodAnalysisRequest::STATUS_FAILED,
            ]);

            storeMediaFile($history, $file, 'food_recognition_image');

            return $history;
        });

        $used = $this->subscriptionUsageCount($user, $subscription);
        $limit = (int) $subscription->food_recognition_limit;

        $clientData = ($lang === 'ar' && $arabicResponse) ? $arabicResponse : $englishResponse;

        $result = [
            'history'            => $history,
            'remaining_requests' => max(0, $limit - $used),
            'api_data'           => $clientData,
            'success'            => $isSuccess,
            'http_status'        => $httpStatus,
        ];

        if (!$isSuccess && $errorMessage) {
            if ($lang === 'ar') {
                $result['message'] = $this->translateText($errorMessage, new GoogleTranslate('ar'));
            } else {
                $result['message'] = $errorMessage;
            }
        }

        return $result;
    }

    protected function getActiveSubscription(User $user): Subscription
    {
        $subscription = $user->subscriptionPackage;

        if (!$subscription) {
            throw new HttpResponseException(
                json_custom_response([
                    'success' => false,
                    'message' => 'Active subscription required for food recognition.',
                ], 403)
            );
        }

        return $subscription;
    }

    
    

    protected function assertWithinLimit(User $user, Subscription $subscription): void
    {
        $limit = (int) $subscription->food_recognition_limit;
        $used = $this->subscriptionUsageCount($user, $subscription);

        if ($limit <= 0 || $used >= $limit) {
            throw new HttpResponseException(
                json_custom_response([
                    'success' => false,
                    'message' => 'Food recognition limit reached for your subscription.',
                ], 429)
            );
        }
    }

    protected function subscriptionUsageCount(User $user, Subscription $subscription): int
    {
        return FoodAnalysisRequest::where('user_id', $user->id)
            ->where('created_at', '>=', $subscription->subscription_start_date)
            ->when($subscription->subscription_end_date, function ($q) use ($subscription) {
                $q->where('created_at', '<=', $subscription->subscription_end_date);
            })
            ->count();
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

        if (isset($data['message']) && is_string($data['message'])) {
            $data['message'] = $this->translateText($data['message'], $translator);
        }

        if (isset($data['lang']) && is_string($data['lang'])) {
            $data['lang'] = $targetLocale;
        }

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
    protected function checkSubscription(User $user): void
    {
        $subscription = $user->subscriptionPackage;
        Log::info('Subscription: ' . $subscription);
        if (!$subscription || $subscription->subscription_end_date < now()) {
            throw new HttpResponseException(
                json_custom_response([
                    'success' => false,
                    'message' => 'Subscription expired.',
                ], 403)
            );
        }
    }


}
