<?php

namespace App\Http\Controllers;

use App\Models\DismissedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function destroy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'notification_key' => ['required', 'string', 'max:255'],
        ]);

        DismissedNotification::firstOrCreate([
            'user_id' => $request->user()->id,
            'notification_key' => $data['notification_key'],
        ]);

        return back();
    }

    public function clear(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'notification_keys' => ['nullable', 'array'],
            'notification_keys.*' => ['string', 'max:255'],
        ]);

        foreach (array_unique($data['notification_keys'] ?? []) as $key) {
            DismissedNotification::firstOrCreate([
                'user_id' => $request->user()->id,
                'notification_key' => $key,
            ]);
        }

        return back();
    }
}
