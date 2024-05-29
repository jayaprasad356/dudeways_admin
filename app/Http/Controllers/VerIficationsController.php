<?php

namespace App\Http\Controllers;

use App\Models\Verifications;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class VerificationsController extends Controller
{
    public function index(Request $request)
    {
        $query = Verifications::query()->with('user'); // Eager load the user relationship

        // Filter by user if user_id is provided
        if ($request->has('user_id')) {
            $user_id = $request->input('user_id');
            $query->where('user_id', $user_id);
        }

        $verifications = $query->latest()->paginate(10); // Paginate the results

        $users = Users::all(); // Fetch all users for the filter dropdown

        return view('verifications.index', compact('verifications', 'users')); // Pass friends and users to the view
    }

    public function destroy(Verifications $verifications)
    {
        $verifications->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
