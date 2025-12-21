<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartRequest;
use App\Services\CartService;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function viewCart()
    {
        $cartItems = $this->cartService->getCartContents();
        $total = $cartItems->sum(function($item) {
            return $item['price'] * $item['quantity'];
        });
        return view('Basket', compact('cartItems' , 'total'));
    }

    public function add(CartRequest $request)
    {
        $goodsId = $request->input('goods_id');
        $quantity = $request->input('quantity', 1);
        $this->cartService->addToCart($goodsId, $quantity);
        return redirect()->route('cart.view')->with('success', 'Товар добавлен в корзину');
    }

    public function update(CartRequest $request)
    {
        $goodsId = $request->input('goods_id');
        $quantity = $request->input('quantity', 1);
        $this->cartService->updateCartItem($goodsId, $quantity);
        return redirect()->route('cart.view')->with('success', 'Количество обновлено');
    }

    public function remove(CartRequest $request)
    {
        $goodsId = $request->input('goods_id');
        $this->cartService->removeFromCart($goodsId);
        return redirect()->route('cart.view')->with('success', 'Товар удален из корзины');
    }

}
