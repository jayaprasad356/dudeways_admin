<?php
namespace App\Http\Controllers;

use App\Http\Requests\NotificationsStoreRequest;
use App\Models\Notifications;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use OneSignal;


class NotificationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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

        return view('notifications.index', compact('notifications', 'users')); // Pass trips and users to the view
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = Users::all(); // Fetch all users
        return view('notifications.create', compact('users')); // Pass users to the view
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  NotificationsStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NotificationsStoreRequest $request)
    {
        $notification = Notifications::create([
            'message' => $request->message,
            'user_id' => $request->user_id,
            'notify_user_id' => $request->notify_user_id,
            'datetime' => now(),
        ]);

        if (!$notification) {
            return redirect()->back()->with('error', 'Sorry, something went wrong while creating the notification.');
        }

        // Debugging output
        Log::info('Attempting to send notification');
        Log::info('Notification data: ', [
            'message' => $notification->message,
            'user_id' => $notification->notify_user_id,
        ]);
        
        try {
            OneSignal::sendNotificationToUser(
                $notification->message,
                $notification->notify_user_id,
                null,
                null,
                null,
                null
            );
        } catch (\Exception $e) {
            Log::error('OneSignal Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Sorry, something went wrong while sending the notification.');
        }
        
        Log::info('Notification sent successfully');
        
    }
    /**
     * Display the specified resource.
     *
     * @param  Notifications  $notifications
     * @return \Illuminate\Http\Response
     */
    public function show(Notifications $notifications)
    {
        // Implement show logic if needed
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Notifications  $notifications
     * @return \Illuminate\Http\Response
     */
    public function edit(Notifications $notifications)
    {
        $users = Users::all(); // Fetch all users
        return view('notifications.edit', compact('notifications', 'users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Notifications  $notifications
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Notifications $notifications)
    {
        $notifications->message = $request->message;
        $notifications->user_id = $request->user_id;
        $notifications->notify_user_id = $request->notify_user_id;
        $notifications->datetime = now();

        if (!$notifications->save()) {
            return redirect()->back()->with('error', 'Sorry, something went wrong while updating the notification.');
        }

        return redirect()->route('notifications.edit', $notifications->id)->with('success', 'Success, notification has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Notifications  $notifications
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notifications $notifications)
    {
        $notifications->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
