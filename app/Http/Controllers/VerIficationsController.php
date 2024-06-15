<?php

namespace App\Http\Controllers;

use App\Models\Verifications;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VerificationsController extends Controller
{
    public function verify(Request $request)
    {
        $userIds = $request->input('user_ids', []);
        
        // Update users' points
        foreach ($userIds as $userId) {
            $user = Users::find($userId);
            if ($user) {
                $user->points += 100; // Increment points as needed
                $user->save();
            }
        }

        // Update verification statuses
        Verifications::whereIn('user_id', $userIds)
                    ->update(['status' => 1]);

        return response()->json(['success' => true]);
    }

    public function index(Request $request)
    {
        $query = Verifications::query()->with('user'); // Eager load the user relationship

        // Filter by user if user_id is provided
        if ($request->has('user_id')) {
            $user_id = $request->input('user_id');
            $query->where('user_id', $user_id);
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            $query->where('status', $status);
        }
    
        $verifications = $query->latest()->paginate(10); // Paginate the results

        $users = Users::all(); // Fetch all users for the filter dropdown

        return view('verifications.index', compact('verifications', 'users')); // Pass verifications and users to the view
    }

    public function destroy(Verifications $verification)
    {
        $verification->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
