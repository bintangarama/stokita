<?php

namespace App\Filament\Widgets;

use App\Models\SystemNotifications;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class UnreadNotifications extends TableWidget
{
    protected static ?string $heading = 'Notifikasi';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn(): Builder =>
                SystemNotifications::query()
                    ->where('user_id', auth()->id())
                    ->where('is_read', false)
                    ->latest()
            )
            ->columns([
                TextColumn::make('title')
                    ->weight('bold')
                    ->color(fn($record) => match ($record->type) {
                        'warning' => 'warning',
                        'danger'  => 'danger',
                        default   => 'primary',
                    }),

                TextColumn::make('message')
                    ->wrap(),

                TextColumn::make('created_at')
                    ->since()
                    ->label('Waktu'),
            ]);
    }
}
