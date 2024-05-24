<?php

namespace App\Http\Controllers;

use App\Models\Notifications;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class NotificationsController extends Controller
{
    public function index(Request $request)
    {
        $query = Notifications::query()->with('user'); // Eager load the user relationship

        // Filter by user if user_id is provided
        if ($request->has('user_id')) {
            $user_id = $request->input('user_id');
            $query->where('user_id', $user_id);
        }

        $notifications = $query->latest()->paginate(10); // Paginate the results

        $users = Users::all(); // Fetch all users for the filter dropdown

        return view('notifications.index', compact('notifications', 'users')); // Pass interests and users to the view
    }

    public function destroy(Notifications $notifications)
    {
        $notifications->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
