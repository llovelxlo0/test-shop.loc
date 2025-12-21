<?php

namespace App\Http\Controllers\Review;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ReviewReply;
use Illuminate\Http\Request;

class ReviewReplyController extends Controller
{
    public function store(Request $request, Review $review)
    {
        $this->authorize('create', ReviewReply::class);

        $data = $request->validate([
            'comment' => 'required|string|max:2000',
        ]);

        ReviewReply::create([
            'review_id' => $review->id,
            'user_id' => $request->user()->id,
            'comment' => $data['comment'],
        ]);
        return back()->with('success', 'Ответ добавлен.');
    }
    public function destroy(ReviewReply $reply)
    {
        $this->authorize('delete', $reply);
        $reply->delete();
        return back()->with('success', 'Ответ удалён.');
    }
    public function edit(ReviewReply $reply)
    {
        $this->authorize('update', $reply);
        $reply->load('review.goods');
        return view('reviews.replies.edit', compact('reply'));
    }
    public function update(Request $request, ReviewReply $reply)
    {
        $this->authorize('update', $reply);
        $data = $request->validate([
            'comment' => 'required|string|max:2000',
        ]);
        $reply->update($data);
        $goodsId = $reply->review->goods_id ?? null;
        if ($goodsId) {
            return redirect()->route('goods.fullinfo', $goodsId)->with('success', 'Ответ обновлён.');
        }
        return back()->with('success', 'Ответ обновлён.');
    }
}
