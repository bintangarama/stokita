<?php

namespace App\Filament\Resources\Purchases\Schemas;

use App\Models\Item;
use App\Models\Supplier;
use App\Models\Unit;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PurchaseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pembelian')
                    ->schema([
                        Select::make('supplier_id')
                            ->label('Supplier')
                            ->options(Supplier::pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        TextInput::make('invoice_no')
                            ->label('Nomor Invoice')
                            ->maxLength(120)
                            ->placeholder('INV-001'),
                        DatePicker::make('purchase_date')
                            ->label('Tanggal Pembelian')
                            ->default(now())
                            ->required(),
                        TextInput::make('total_amount')
                            ->required()
                            ->numeric()
                            ->default(0.0),
                        TextInput::make('created_by')
                            // ->numeric()
                            ->disabled(),
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(2),
                        // ->columnSpanFull(),
                    ]),
                Section::make('Detail Pembelian')
                    ->schema([
                        Repeater::make('items')
                            ->relationship('items')
                            ->label('Daftar Item')
                            ->schema([
                                Select::make('item_id')
                                    ->label('Item')
                                    ->options(Item::pluck('name', 'id'))
                                    ->searchable()
                                    ->required(),

                                Select::make('unit_id')
                                    ->label('Satuan')
                                    ->options(Unit::pluck('name', 'id'))
                                    ->searchable()
                                    ->required(),

                                TextInput::make('qty')
                                    ->label('Qty')
                                    ->numeric()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        // hitung subtotal setiap kali qty berubah
                                        $set('line_total', (float)($state ?? 0) * (float)($get('unit_price') ?? 0));
                                    }),

                                TextInput::make('unit_price')
                                    ->label('Harga Satuan')
                                    ->numeric()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        // hitung subtotal setiap kali harga berubah
                                        $set('line_total', (float)($get('qty') ?? 0) * (float)($state ?? 0));
                                    }),

                                TextInput::make('line_total')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->reactive()
                                    ->formatStateUsing(
                                        fn($state, $get) =>
                                        (float)($get('qty') ?? 0) * (float)($get('unit_price') ?? 0)
                                    ),
                            ])
                            ->columns(3)
                            ->createItemButtonLabel('Tambah Item')
                            ->live()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                // Hitung total pembelian otomatis dari semua item
                                $total = collect($get('items'))
                                    ->sum(fn($item) => (float)($item['qty'] ?? 0) * (float)($item['unit_price'] ?? 0));
                                $set('total_amount', $total);
                            })
                            ->defaultItems(1),
                    ]),

                Section::make('Ringkasan')
                    ->schema([
                        TextInput::make('total_amount')
                            ->label('Total Pembelian')
                            ->numeric()
                            ->disabled()
                            ->reactive()
                            ->dehydrated(false)
                            ->afterStateHydrated(function ($set, $get) {
                                if (is_null($get('total_amount')) && filled($get('items'))) {
                                    $total = collect($get('items'))
                                        ->sum(fn($i) => (float) ($i['qty'] ?? 0) * (float) ($i['unit_price'] ?? 0));
                                    $set('total_amount', $total);
                                }
                            }),
                    ]),

            ]);
    }
}
