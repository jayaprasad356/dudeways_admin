<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotificationsStoreRequest;
use App\Models\Notifications; // Assuming your model is named Notification (singular)
use App\Models\Users; // Assuming your model is named User (singular)
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Berkayk\OneSignal\OneSignalClient;

class NotificationsController extends Controller
{

    public function index(Request $request)
    {
        $query = Notifications::query()->with('user');

        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        $notifications = $query->latest()->paginate(10);
        $users = Users::all();

        return view('notifications.index', compact('notifications', 'users'));
    }

    public function create()
    {
        $users = Users::all();
        return view('notifications.create', compact('users'));
    }

    public function store(NotificationsStoreRequest $request)
    {
        $notification = Notifications::create([
            'message' => $request->message,
            'user_id' => $request->user_id,
            'notify_user_id' => $request->notify_user_id,
            'datetime' => now(),
        ]);

        if (!$notification) {
            return redirect()->back()->with('error', 'Something went wrong while creating the notification.');
        }

        Log::info('Notification sent successfully');
        return redirect()->route('notifications.index')->with('success', 'Notification created and sent successfully.');
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
        $users = Users::all(); // Fetch all users (assuming User model is singular)
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
