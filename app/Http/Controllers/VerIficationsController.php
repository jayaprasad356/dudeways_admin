<?php
namespace App\Http\Controllers;

use App\Models\Verifications;
use App\Models\Users;
use Illuminate\Http\Request;
use Berkayk\OneSignal\OneSignalClient;

class VerificationsController extends Controller
{
    protected $oneSignalClient;

    public function __construct(OneSignalClient $oneSignalClient)
    {
        $this->oneSignalClient = $oneSignalClient;
    }

    public function verify(Request $request)
    {
        $verificationIds = $request->input('verification_ids', []);
        
        foreach ($verificationIds as $verificationId) {
            $verification = Verifications::find($verificationId);
            if ($verification) {
                $user = Users::find($verification->user_id);
                if ($user && $user->verify_bonus_sent !== 1) {
                    $user->points += 100; 
                    $user->total_points += 100; 
                    $user->verified = 1;
                    $user->verify_bonus_sent = 1;
                    $user->save();

                    \App\Models\Transaction::create([
                        'user_id' => $user->id,
                        'type' => 'verify_points',
                        'points' => 100, // Assuming the points are 100
                        'datetime' => now(),
                    ]);
                }

                  // Send notification to the user who posted the verification
                  $this->sendNotificationToUser(strval($user->id));

                // Update verification status
                $verification->status = 1;
                $verification->save();
            }
        }

        return response()->json(['success' => true]);
    }

    protected function sendNotificationToUser($user_id)
    {
        $message = "Your Profile Verified Successfully";
        $this->oneSignalClient->sendNotificationToExternalUser(
            $message,
            $user_id,
            $url = null,
            $data = null,
            $buttons = null,
            $schedule = null
        );
    }

    public function index(Request $request)
    {
        $query = Verifications::query()->with('user'); // Eager load the user relationship

        // Filter by user if user_id is provided
        if ($request->has('user_id')) {
            $user_id = $request->input('user_id');
            $query->where('user_id', $user_id);
        }

        if ($request->has('status')) {
            $status = $request->input('status');
            $query->where('status', $status);
        } else {
            // By default, fetch pending trips
            $query->where('status', 0);
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

