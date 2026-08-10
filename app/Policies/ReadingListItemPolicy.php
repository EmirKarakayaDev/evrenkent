<?php

namespace App\Policies;

use App\Models\ReadingListItem;
use App\Models\User;

class ReadingListItemPolicy
{
    public function update(User $user, ReadingListItem $readingListItem): bool
    {
        return $readingListItem->user_id === $user->id;
    }

    public function delete(User $user, ReadingListItem $readingListItem): bool
    {
        return $readingListItem->user_id === $user->id;
    }
}
