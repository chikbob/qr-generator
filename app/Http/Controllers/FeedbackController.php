<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedbacks = Feedback::with('user')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return Inertia::render('Feedback/Index', [
            'feedbacks' => $feedbacks,
        ]);
    }

    public function create()
    {
        return Inertia::render('Feedback/Create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'category' => 'nullable|string|max:100',
        ]);

        $priority = match ($user->plan?->name) {
            'Enterprise' => 'high',
            'Pro' => 'medium',
            default => 'low',
        };

        $feedback = Feedback::create([
            'user_id' => $user->id,
            'subject' => $request->subject,
            'message' => $request->message,
            'category' => $request->category ?? 'general',
            'priority' => $priority,
        ]);

        return redirect()->route('feedback.show', $feedback->id)
            ->with('success', 'flash.feedback.sent');
    }

    public function show(Feedback $feedback)
    {
        // Проверяем, чтобы пользователь не мог смотреть чужие заявки
        if ($feedback->user_id !== Auth::id()) {
            abort(403);
        }

        return Inertia::render('Feedback/Show', [
            'feedback' => $feedback->load('user'),
        ]);
    }
}
