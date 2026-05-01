<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Filament\Exports\OrderExporter;
use App\Services\SalesService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_no')
                    ->label('Order No')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'draft' => 'gray',
                        'confirmed' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    }),
                TextColumn::make('grand_total')
                    ->label('Grand Total    ')
                    ->money('IDR', true)
                    ->sortable(),
                TextColumn::make('order_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('discount')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('created_by')
                    ->label('User')
                    ->formatStateUsing(fn($state) => optional(\App\Models\User::find($state))->name ?? '-')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                // ======================
                // QUICK ACTION: CONFIRM
                // ======================
                Action::make('confirm')
                    ->label('Confirm')
                    ->icon('heroicon-o-check')
                    ->color('warning')
                    ->visible(fn($record) => $record->status === 'draft')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => 'confirmed']);
                        SalesService::process($record);
                        Notification::make()
                            ->title("Order {$record->order_no} confirmed")
                            ->success()
                            ->send();
                    }),

                // ======================
                // QUICK ACTION: COMPLETE
                // ======================
                Action::make('complete')
                    ->label('Complete')
                    ->icon('heroicon-o-flag')
                    ->color('success')
                    ->visible(fn($record) => $record->status === 'confirmed')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => 'completed']);
                        SalesService::process($record);

                        Notification::make()
                            ->title("Order {$record->order_no} completed")
                            ->success()
                            ->send();
                    }),

                // ======================
                // QUICK ACTION: CANCEL
                // ======================
                Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn($record) => in_array($record->status, ['draft', 'confirmed']))
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => 'cancelled']);
                        SalesService::process($record);

                        Notification::make()
                            ->title("Order {$record->order_no} cancelled")
                            ->danger()
                            ->send();
                    }),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ExportBulkAction::make('export')
                        ->label('Export Data')
                        ->fileName(fn() => 'order-' . now()->format('Y-m-d'))
                        ->exporter(OrderExporter::class)
                    // Optional: batasi format yang diizinkan
                    ,
                ]),
            ]);
    }
}
