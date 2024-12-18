<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsersStoreRequest;
use App\Models\Users;
use App\Models\VoiceVerifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VoiceVerificationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * 
     */

     public function verify(Request $request)
     {
         $verificationIds = $request->input('verification_ids', []);
     
         foreach ($verificationIds as $verificationId) {
             $voice_verification = VoiceVerifications::find($verificationId);
             if ($voice_verification) {
                 // Update the verification status to Paid (1)
                 $voice_verification->status = 1;
                 $voice_verification->save();
     
                 // Update the user's voice_verification_status to 1
                 $user = Users::find($voice_verification->user_id);
                 if ($user) {
                     $user->voice_verification_status = 1;
                     $user->save();
                 }
             }
         }
     
         return response()->json(['success' => true]);
     }
     
     public function reject(Request $request)
     {
         $verificationIds = $request->input('verification_ids', []);
     
         foreach ($verificationIds as $verificationId) {
             $voice_verification = VoiceVerifications::find($verificationId);
             if ($voice_verification) {
                 // Delete the verification record
                 $voice_verification->delete();
     
                 // Update the user's voice_verification_status to 2 (Rejected)
                 $user = Users::find($voice_verification->user_id);
                 if ($user) {
                     $user->voice_verification_status = 2;
                     $user->save();
                 }
             }
         }
     
         return response()->json(['success' => true]);
     }
     


     public function index(Request $request)
    {
        $query = VoiceVerifications::query()->with(['user']); // Eager load the user and their bank details
    
        // Filter by user if user_id is provided
        if ($request->has('user_id')) {
            $user_id = $request->input('user_id');
            $query->where('user_id', $user_id);
        }
    
        // Filter by search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($query) use ($search) {
                $query->where('id', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($query) use ($search) {
                          $query->where('name', 'like', "%{$search}%");
                      });
            });
        }
    
        // Filter by verified status
        if ($request->filled('status')) {
            $status = $request->input('status');
            $query->where('status', $status);
        }
    
    
        // Check if the request is AJAX
        if ($request->wantsJson()) {
            return response()->json($query->get());
        }
    
        $voice_verifications = $query->latest()->paginate(10); // Paginate the results
        $users = Users::all(); // Fetch all users for the filter dropdown
    
        return view('voice_verifications.index', compact('voice_verifications', 'users')); // Pass withdrawals, users, and bankdetails to the view
    }
    
     
}