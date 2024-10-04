<?php

namespace App\Http\Controllers;

use App\Models\Users;
use App\Models\Withdrawals;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WithdrawalsController extends Controller
{
    public function index(Request $request)
    {
        $query = Withdrawals::query()->with('user'); // Eager load the user relationship

         // Filter by user if user_id is provided
         if ($request->has('user_id')) {
            $user_id = $request->input('user_id');
            $query->where('user_id', $user_id);
        }
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($query) use ($search) {
                $query->where('id', 'like', "%{$search}%")
                      ->orWhere('amount', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($query) use ($search) {
                          $query->where('name', 'like', "%{$search}%");
                      });
            });
        }
        $withdrawals = $query->latest()->paginate(10); // Paginate the results

        $users = Users::all(); // Fetch all users for the filter dropdown

        return view('withdrawals.index', compact('withdrawals', 'users')); // Pass friends and users to the view
    }

    public function destroy(Withdrawals $withdrawals)
    {
        $withdrawals->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}

