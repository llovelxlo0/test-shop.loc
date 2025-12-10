<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Goods;
use App\Models\User;


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
}
