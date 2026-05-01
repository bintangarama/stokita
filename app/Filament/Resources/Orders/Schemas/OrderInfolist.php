<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order Information')
                    ->schema([
                        TextEntry::make('order_no')
                            ->label('Order No')
                            ->weight('bold'),

                        TextEntry::make('customer.name')
                            ->label('Customer')
                            ->placeholder('-'),

                        TextEntry::make('order_date')
                            ->label('Tanggal Order')
                            ->date('d M Y'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()            // tampil sebagai badge
                            ->color(fn($state) => match ($state) {
                                'draft'     => 'gray',
                                'confirmed' => 'warning',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                                default     => 'gray',
                            }),
                    ])->columns(2),
                Section::make('Ringkasan Pembayaran')
                    ->schema([
                        TextEntry::make('total_amount')
                            ->label('Subtotal')
                            ->money('IDR'),

                        TextEntry::make('discount')
                            ->label('Diskon')
                            ->money('IDR')
                            ->placeholder('Rp 0'),

                        TextEntry::make('grand_total')
                            ->label('Grand Total')
                            ->money('IDR')
                            ->weight('bold')
                            ->color('primary'),
                    ])
                    ->columns(3),
                Section::make('Informasi Sistem')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Dibuat Oleh')
                            ->placeholder('-'),

                        TextEntry::make('created_at')
                            ->dateTime('d M Y H:i'),

                        TextEntry::make('updated_at')
                            ->dateTime('d M Y H:i'),
                    ])
                    ->columns(3),
                Section::make('Catatan')
                    ->schema([
                        TextEntry::make('notes')
                            ->label('')
                            ->placeholder('-'),
                    ])
                    ->columnSpanFull()
                    ->hidden(fn($record) => empty($record->notes)),
            ]);
    }
}
