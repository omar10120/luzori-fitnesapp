<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str; // Added for generating a temporary ID

class AIFitnessController extends Controller
{
    // 1. Endpoint for Flutter to start a workout
    public function startWorkout(Request $request)
    {
        $request->validate([
            'exercise' => 'required|string',
            'camera' => 'nullable|string|in:front,back'
        ]);

        $user = Auth::user();
        
        // --- 30 MINUTE LIMIT LOGIC (COMMENTED OUT FOR TESTING) ---
        // $minutesUsedToday = $user->workoutSessions()->whereDate('created_at', today())->sum('duration_minutes');
        // if ($minutesUsedToday >= 30) {
        //     return response()->json(['error' => 'Daily 30-minute limit reached.'], 403);
        // }

        // 2. Ask Python to start the session
        // UPDATED: Fallback port changed to 8001
        $pythonBaseUrl = env('PYTHON_AI_URL', 'http://127.0.0.1:8001');
        $pythonToken = env('PYTHON_AI_TOKEN', '1234567890');

        $response = Http::withToken($pythonToken)->post("{$pythonBaseUrl}/api/workout/start", [
            'user_id' => $user->id,
            'exercise' => $request->exercise,
            'camera' => $request->camera ?? 'front',
        ]);

        if ($response->failed()) {
            return response()->json([
                'error' => 'Failed to start AI engine',
                'details' => $response->json() // Helpful for debugging
            ], 500);
        }

        $data = $response->json();

        // 3. Save session to DB (COMMENTED OUT FOR TESTING)
        // $session = $user->workoutSessions()->create([
        //     'exercise_type' => $request->exercise,
        //     'python_session_id' => $data['session_id'],
        //     'started_at' => now(),
        // ]);

        // 4. Return data to Flutter
        return response()->json([
            'message' => 'Workout started successfully',
            // 'session_id' => $session->id, // Commented out DB ID
            'session_id' => $data['session_id'], // Using Python's ID temporarily for testing
            'python_session_id' => $data['session_id'],
            // UPDATED: Fallback WS port changed to 8001
            'websocket_url' => env('PYTHON_WS_URL', 'ws://127.0.0.1:8001') . "/ws/workout/{$data['session_id']}?token={$pythonToken}"
        ]);
    }

    // 2. Endpoint for Flutter to stop the workout
    public function stopWorkout(Request $request)
    {
        $request->validate([
            'python_session_id' => 'required|string'
        ]);

        // UPDATED: Fallback port changed to 8001
        $pythonBaseUrl = env('PYTHON_AI_URL', 'http://127.0.0.1:8001');
        $pythonToken = env('PYTHON_AI_TOKEN', '1234567890');

        // Tell Python to stop and give us the final summary
        $response = Http::withToken($pythonToken)->post("{$pythonBaseUrl}/api/workout/stop", [
            'session_id' => $request->python_session_id,
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to stop AI engine'], 500);
        }

        $summary = $response->json();

        // Save the final results to Laravel DB (COMMENTED OUT FOR TESTING)
        // $session = Auth::user()->workoutSessions()->where('python_session_id', $request->python_session_id)->first();
        // if ($session) {
        //     $session->update([
        //         'ended_at' => now(),
        //         'duration_minutes' => $summary['duration'] / 60, // convert seconds to mins
        //         'reps' => $summary['reps'],
        //         'calories' => $summary['calories'],
        //         'score' => $summary['score'] ?? 0
        //     ]);
        // }

        return response()->json([
            'message' => 'Workout completed',
            'summary' => $summary
        ]);
    }

    // 3. Endpoint for Python to fetch Exercise Config
    public function getExerciseConfig($exercise)
    {
        // Python is calling Laravel to get the rules
        return response()->json([
            'exercise' => $exercise,
            'angles' => [
                'up' => 40,
                'down' => 160
            ],
            'side' => 'left',
            'thresholds' => []
        ]);
    }
}