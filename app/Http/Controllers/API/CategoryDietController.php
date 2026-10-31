<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CategoryDiet;
use App\Http\Resources\CategoryDietResource;
use Illuminate\Support\Str;

class CategoryDietController extends Controller
{
    public function getList(Request $request)
    {
        $categorydiet = CategoryDiet::where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('user_id')
                    ->orWhere('user_id', auth()->id());
            });

        $categorydiet->when(request('title'), function ($q) {
            return $q->where('title', 'LIKE', '%' . request('title') . '%');
        });
        
        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page)){
            if(is_numeric($request->per_page))
            {
                $per_page = $request->per_page;
            }
            if($request->per_page == -1 ){
                $per_page = $categorydiet->count();
            }
        }

        $categorydiet = $categorydiet->orderBy('title', 'asc')->paginate($per_page);

        $items = CategoryDietResource::collection($categorydiet);

        $response = [
            'pagination'    => json_pagination_response($items),
            'data'          => $items,
        ];
        
        return json_custom_response($response);
    }
        public function getUserList(Request $request)
    {
        // Only get records created by the logged-in user
        $categorydiet = CategoryDiet::where('status', 'active')
            ->where('user_id', auth()->id());

        $categorydiet->when(request('title'), function ($q) {
            return $q->where('title', 'LIKE', '%' . request('title') . '%');
        });
        
        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page)){
            if(is_numeric($request->per_page))
            {
                $per_page = $request->per_page;
            }
            if($request->per_page == -1 ){
                $per_page = $categorydiet->count();
            }
        }

        $categorydiet = $categorydiet->orderBy('title', 'asc')->paginate($per_page);

        $items = CategoryDietResource::collection($categorydiet);

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
        ]);

        $categorydiet = CategoryDiet::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'slug' => $this->uniqueSlug($validated['title']),
            'status' => 'active',
        ]);

        return json_custom_response([
            'message' => __('message.save_form', ['form' => __('message.categorydiet')]),
            'data' => new CategoryDietResource($categorydiet),
        ]);
    }

    public function updateCustom(Request $request)
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
            'title' => ['required', 'string', 'max:255'],
        ]);

        $categorydiet = CategoryDiet::where('user_id', auth()->id())->find($validated['id']);
        if (!$categorydiet) {
            return json_message_response(__('message.not_found_entry', ['name' => __('message.categorydiet')]), 404);
        }

        if ($categorydiet->title !== $validated['title']) {
            $validated['slug'] = $this->uniqueSlug($validated['title'], $categorydiet->id);
        }

        unset($validated['id']);
        $categorydiet->update($validated);

        return json_custom_response([
            'message' => __('message.update_form', ['form' => __('message.categorydiet')]),
            'data' => new CategoryDietResource($categorydiet->fresh()),
        ]);
    }

    public function destroyCustom(Request $request)
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $categorydiet = CategoryDiet::where('user_id', auth()->id())->find($validated['id']);
        if (!$categorydiet) {
            return json_message_response(__('message.not_found_entry', ['name' => __('message.categorydiet')]), 404);
        }

        $categorydiet->delete();

        return json_message_response(__('message.delete_form', ['form' => __('message.categorydiet')]));
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title) ?: 'category-diet';
        $slug = $baseSlug;
        $suffix = 1;

        while (CategoryDiet::where('slug', $slug)
            ->when($ignoreId !== null, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $baseSlug . '-' . $suffix++;
        }

        return $slug;
    }
}
