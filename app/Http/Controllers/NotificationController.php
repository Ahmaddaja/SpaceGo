<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserNotification;

class NotificationController extends Controller
{
    public function delete($id)
    {
        UserNotification::findOrFail($id)->delete();
        return back();
    }
}
