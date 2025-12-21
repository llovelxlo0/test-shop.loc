<?php

namespace App\Http\Controllers\Review;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewRequest;
use App\Models\Goods;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    public function show(Goods $goods)
    {
    $reviews = $goods->reviews()->latest()->get();
    return view('goods.fullinfo', compact('goods', 'reviews'));

    }

    public function store(Goods $goods, ReviewRequest $request)
    {
        $this->authorize('create', Review::class);
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
        'status' => Review::STATUS_PENDING,
        ]);

        return redirect()->route('goods.info', $goods)->with('success', 'Отзыв успешно добавлен.');
    }
    public function edit(Review $review)
    {
        $this->authorize('update', $review);
        return view('reviews.edit', compact('review'));
    }
    public function update(Review $review, ReviewRequest $request)
    {
        $this->authorize('update', $review);
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
    public function destroy( Review $review)
    {
        $this->authorize('delete', $review);
        $goodsId = $review->goods_id;
        $review->delete();
        return redirect()->route('goods.info', $goodsId)->with('success', 'Отзыв успешно удален.');
    }
}
