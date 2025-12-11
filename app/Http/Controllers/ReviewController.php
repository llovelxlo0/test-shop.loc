<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Goods;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ReviewRequest;

class ReviewController extends Controller
{
    public function store(Goods $goods, ReviewRequest $request)
    {
        $data = $request->validated();
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('reviews', 'public');
        }
        Review::create([
        'goods_id' => $goods->id,
        'user_id' => Auth::id(),
        'rating' => $data['rating'],
        'comment' => $data['comment'] ?? null,
        'image' => $imagePath,
        ]);
        return redirect()->route('goods.info', $goods)->with('success', 'Отзыв успешно добавлен.');
    }
    public function edit(Review $review)
    {
        return view('reviews.edit', compact('review'));
    }
    public function update(Review $review, ReviewRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            if ($review->image) {
                Storage::disk('public')->delete($review->image);
            }
            $data['image'] = $request->file('image')->store('reviews', 'public');
        }
        $review->update([
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
            'image' => $data['image'] ?? $review->image,
        ]);
        return redirect()->route('goods.info', $review->goods_id)->with('success', 'Отзыв успешно обновлен.');
    }
    public function destroy(Review $review)
    {
        $goodsId = $review->goods_id;
        $review->delete();
        return redirect()->route('goods.info', $goodsId)->with('success', 'Отзыв успешно удален.');
    }
}
