<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Level;
use App\Http\Resources\LevelResource;
use Illuminate\Support\Str;

class LevelController extends Controller
{
    public function getList(Request $request)
    {
        $level = Level::where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('user_id')
                    ->orWhere('user_id', auth()->id());
            });

        $level->when(request('title'), function ($q) {
            return $q->where('title', 'LIKE', '%' . request('title') . '%');
        });
        
        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page)){
            if(is_numeric($request->per_page))
            {
                $per_page = $request->per_page;
            }
            if($request->per_page == -1 ){
                $per_page = $level->count();
            }
        }

        $level = $level->orderBy('title', 'asc')->paginate($per_page);

        $items = LevelResource::collection($level);

        $response = [
            'pagination'    => json_pagination_response($items),
            'data'          => $items,
        ];
        
        return json_custom_response($response);
    }
        public function getUserList(Request $request)
    {
        // Only get records created by the logged-in user
        $level = Level::where('status', 'active')
            ->where('user_id', auth()->id());

        $level->when(request('title'), function ($q) {
            return $q->where('title', 'LIKE', '%' . request('title') . '%');
        });
        
        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page)){
            if(is_numeric($request->per_page))
            {
                $per_page = $request->per_page;
            }
            if($request->per_page == -1 ){
                $per_page = $level->count();
            }
        }

        $level = $level->orderBy('title', 'asc')->paginate($per_page);

        $items = LevelResource::collection($level);

        $response = [
            'pagination'    => json_pagination_response($items),
            'data'          => $items,
        ];
        
        return json_custom_response($response);
    }

    public function storeCustom(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'rate' => ['nullable', 'integer'],
        ]);

        $level = Level::create([
            ...$validated,
            'user_id' => auth()->id(),
            'slug' => $this->uniqueSlug($validated['title']),
            'status' => 'active',
        ]);

        return json_custom_response([
            'message' => __('message.save_form', ['form' => __('message.level')]),
            'data' => new LevelResource($level),
        ]);
    }

    public function updateCustom(Request $request)
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
            'title' => ['required', 'string', 'max:255'],
            'rate' => ['nullable', 'integer'],
        ]);

        $level = Level::where('user_id', auth()->id())->find($validated['id']);
        if (!$level) {
            return json_message_response(__('message.not_found_entry', ['name' => __('message.level')]), 404);
        }

        if ($level->title !== $validated['title']) {
            $validated['slug'] = $this->uniqueSlug($validated['title'], $level->id);
        }

        unset($validated['id']);
        $level->update($validated);

        return json_custom_response([
            'message' => __('message.update_form', ['form' => __('message.level')]),
            'data' => new LevelResource($level->fresh()),
        ]);
    }

    public function destroyCustom(Request $request)
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $level = Level::where('user_id', auth()->id())->find($validated['id']);
        if (!$level) {
            return json_message_response(__('message.not_found_entry', ['name' => __('message.level')]), 404);
        }

        $level->delete();

        return json_message_response(__('message.delete_form', ['form' => __('message.level')]));
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title) ?: 'level';
        $slug = $baseSlug;
        $suffix = 1;

        while (Level::where('slug', $slug)
            ->when($ignoreId !== null, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $baseSlug . '-' . $suffix++;
        }

        return $slug;
    }
}
