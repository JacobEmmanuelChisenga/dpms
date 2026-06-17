<?php

use App\Models\Permit;
use Illuminate\Support\Facades\Artisan;

Artisan::command('permits:sync-expiry', function (): void {
    $count = Permit::query()
        ->where('status', Permit::STATUS_VALID)
        ->whereDate('expiry_date', '<', now()->toDateString())
        ->update(['status' => Permit::STATUS_EXPIRED]);

    $this->info(sprintf('Marked %d permit(s) as expired.', $count));
})->purpose('Synchronize permit statuses with expiry dates.');
