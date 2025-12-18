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
    public function show(Goods $goods)
    {
    $reviews = $goods->reviews()->latest()->get();
    dd($reviews);
    return view('goods.info', compact('goods', 'reviews'));
    
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
        // $reviews = Review::all();
        // dd($reviews);

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
    public function destroy(ReviewRequest $request, Review $review)
    {
        $this->authorize('delete', $review);
        $goodsId = $review->goods_id;
        $review->delete();
        return redirect()->route('goods.info', $goodsId)->with('success', 'Отзыв успешно удален.');
    }
}
