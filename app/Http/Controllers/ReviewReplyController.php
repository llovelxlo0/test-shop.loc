<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\ReviewReply;

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
        return view('review.reply.edit', compact('reply'));
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
            return redirect()->route('goods.info', $goodsId)->with('success', 'Ответ обновлён.');
        }
        return back()->with('success', 'Ответ Обновлёе.');
    }
}
