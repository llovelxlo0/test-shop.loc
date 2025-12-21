<?php

namespace App\Http\Controllers\Wishlist;

use App\Http\Controllers\Controller;
use App\Models\Goods;
use Illuminate\Support\Facades\Auth;


class WishlistController extends Controller
{
    public function toggleWishlist(Goods $goods)
    {
    $user = Auth::user();

        $exists = $user->wishlist()->where('goods_id', $goods->id)->exists();

        if ($exists) {
            $user->wishlist()->detach($goods->id);
            return response()->json(['status' => 'removed from wishlist']);
        }

        $user->wishlist()->attach($goods->id);
        return response()->json(['status' => 'added to wishlist']);
   }
   public function index()
   {
        $items = Auth::user()->wishlist()->with('goods')->get();
        return view('wishlist.index', compact('items'));
   }
}
