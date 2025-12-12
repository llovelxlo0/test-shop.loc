<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use App\Models\ReviewVote;

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
