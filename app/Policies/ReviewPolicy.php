<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Review $review): bool
    {
        if ($review->isApproved()) {
            return true;
        }
        if ($user === null) {
            return false;
        }
        if ($user->isAdmin()) {
            return true;
        }
        return $user->id === $review->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Review $review): bool
    {
        return $user->id === $review->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Review $review): bool
    {
        return $user->id === $review->user_id || $user->isAdmin();
    }
    /**
     * Модерация (изменение статуса: approve / reject)
     */
    public function moderate(User $user, Review $review): bool
    {
        return $user->isAdmin();
    }
    /*
    * Голоса (лаки или дизлайки) за отзывы.
    */
    public function vote(User $user, Review $review): bool
    {
        if ($user->id === $review->user_id) {
        return false; // не голосуем за себя
    }

    if (!$review->isApproved()) {
        return false; // голосуем только за одобренные
    }

    return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Review $review): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Review $review): bool
    {
        return false;
    }
}
