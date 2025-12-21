<?php

namespace App\Http\Controllers\Review;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ReviewVote;
use Illuminate\Http\Request;

class ReviewVoteController extends Controller
{
    public function vote(Request $request, Review $review)
    {
        $this->authorize('vote', $review);

        $data = $request->validate([
            'value' => 'required|in:1,-1',
        ]);

        ReviewVote::updateOrCreate(
            [
                'review_id' => $review->id,
                'user_id'   => $request->user()->id,
            ],
            [
                'value' => $data['value'],
            ]
        );

        return back()->with('success', 'Ваш голос учтён.');
    }
}
