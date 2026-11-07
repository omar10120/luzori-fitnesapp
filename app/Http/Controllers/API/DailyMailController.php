<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DailyMail;
use App\Models\Diet;
use Illuminate\Support\Facades\DB;

class DailyMailController extends Controller
{
    public function index(Request $request)
    {
        // Only get Daily Mails that belong to the logged-in user
        $dailyMails = DailyMail::where('user_id', auth()->id())->with('diets');

        // Search by title
        $dailyMails->when(request('title'), function ($q) {
            return $q->where('title', 'LIKE', '%' . request('title') . '%');
        });

        // Pagination logic (matching your other controllers)
        $per_page = config('constant.PER_PAGE_LIMIT');
        if ($request->has('per_page') && !empty($request->per_page)) {
            if (is_numeric($request->per_page)) {
                $per_page = $request->per_page;
            }
            if ($request->per_page == -1) {
                $per_page = $dailyMails->count();
            }
        }

        // Order by time (ascending)
        $dailyMails = $dailyMails->orderBy('time', 'asc')->paginate($per_page);

        $response = [
            'pagination' => json_pagination_response($dailyMails),
            'data'       => $dailyMails->items(),
        ];

        return json_custom_response($response);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'time' => 'required|date_format:H:i', // or H:i:s
            'diet_ids' => 'required|array|min:1',
            'diet_ids.*' => 'exists:diets,id'
        ]);

        // Verify all diets belong to the user or are global (admin)
        $validDiets = Diet::whereIn('id', $request->diet_ids)
            ->where(function ($q) {
                $q->whereNull('user_id')->orWhere('user_id', auth()->id());
            })->count();

        if ($validDiets !== count($request->diet_ids)) {
            return json_message_response('One or more diets are invalid or do not belong to you.', 403);
        }

        try {
            DB::beginTransaction();

            $dailyMail = DailyMail::create([
                'user_id' => auth()->id(),
                'title' => $request->title,
                'time' => $request->time,
                'status' => true
            ]);

            // Sync the diets to the pivot table
            $dailyMail->diets()->sync($request->diet_ids);

            DB::commit();

            return json_custom_response([
                'message' => 'Daily Mail created successfully',
                'data' => $dailyMail->load('diets')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return json_message_response('Failed to create Daily Mail', 500);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'title' => 'required|string|max:255',
            'time' => 'required|date_format:H:i',
            'diet_ids' => 'required|array|min:1',
            'diet_ids.*' => 'exists:diets,id'
        ]);

        // Find the Daily Mail and ensure it belongs to the user
        $dailyMail = DailyMail::where('user_id', auth()->id())->find($request->id);
        if (!$dailyMail) {
            return json_message_response(__('message.not_found_entry', ['name' => 'Daily Mail']), 404);
        }

        // Verify all new diets belong to the user or are global
        $validDiets = Diet::whereIn('id', $request->diet_ids)
            ->where(function ($q) {
                $q->whereNull('user_id')->orWhere('user_id', auth()->id());
            })->count();

        if ($validDiets !== count($request->diet_ids)) {
            return json_message_response('One or more diets are invalid or do not belong to you.', 403);
        }

        try {
            DB::beginTransaction();

            $dailyMail->update([
                'title' => $request->title,
                'time' => $request->time,
            ]);

            // Sync will automatically add new diets and remove the ones unchecked
            $dailyMail->diets()->sync($request->diet_ids);

            DB::commit();

            return json_custom_response([
                'message' => 'Daily Mail updated successfully',
                'data' => $dailyMail->load('diets')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return json_message_response('Failed to update Daily Mail', 500);
        }
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        // Find the Daily Mail and ensure it belongs to the user
        $dailyMail = DailyMail::where('user_id', auth()->id())->find($request->id);
        if (!$dailyMail) {
            return json_message_response(__('message.not_found_entry', ['name' => 'Daily Mail']), 404);
        }

        // Delete the record. 
        // Because of ->onDelete('cascade') in your pivot migration, 
        // the entries in 'daily_mail_diet' will automatically be deleted too.
        $dailyMail->delete();

        return json_message_response(__('message.delete_form', ['form' => 'Daily Mail']));
    }
}