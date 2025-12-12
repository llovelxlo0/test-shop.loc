<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;

class ReviewModerationController extends Controller
{
    public function approve(Review $review)
    {
        $this->authorize('moderate', $review);

        $review->update([
            'status' => Review::STATUS_APPROVED,
        ]);

        return back()->with('success', 'Отзыв одобрен.');
    }

    public function reject(Review $review)
    {
        $this->authorize('moderate', $review);

        $review->update([
            'status' => Review::STATUS_REJECTED,
        ]);

        return back()->with('success', 'Отзыв отклонен.');
    }
}
