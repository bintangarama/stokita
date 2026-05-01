<?php

namespace App\Services;

use App\Models\Item;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Panel;
use Spatie\Permission\Models\Role;

class ReorderAlertService
{
    /**
     * Check & create reorder notification if needed
     */
    public static function check(Item $item): void
    {
        // 1. Item tidak dilacak stok
        if (! $item->track_stock) {
            return;
        }

        // 2. Tidak punya batas reorder
        if ($item->reorder_threshold === null) {
            return;
        }

        // 3. Stok masih aman
        if ($item->current_stock > $item->reorder_threshold) {
            return;
        }

        // 4. Anti spam (24 jam sekali)
        if (
            $item->last_reorder_alert_at &&
            $item->last_reorder_alert_at->diffInHours(now()) < 24
        ) {
            return;
        }

        // 5. Tentukan penerima (admin & manager)
        $users = Role::whereIn('name', ['admin', 'manager'])
            ->first()
            ->users;
        foreach ($users as $user) {
            Notification::make()
                ->title('Low Stock Alert')
                ->body("Stok {$item->name} tersisa {$item->current_stock} {$item->baseUnit?->name}")
                ->icon('heroicon-o-exclamation-triangle')
                ->danger()
                ->send()
                ->sendToDatabase($user);
        }

        // 6. Update waktu notifikasi terakhir
        $item->update([
            'last_reorder_alert_at' => now(),
        ]);
    }
}
