<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Customer;
use App\Models\Item;
use App\Models\Unit;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Order')
                    ->schema([
                        Select::make('customer_id')
                            ->label('Customer')
                            ->options(Customer::where('is_active', 1)->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        // TextInput::make('order_no')
                        //     ->label('Nomor Order')
                        //     ->default(fn() => 'ORD-' . now()->format('Ymd-His'))
                        //     ->disabled(),
                        TextInput::make('order_no')
                            ->disabled()
                            ->dehydrated(true)
                            ->label('Order No'),
                        DatePicker::make('order_date')
                            ->label('Tanggal')
                            ->default(now())
                            ->required(),

                        TextInput::make('total_amount')
                            ->disabled()
                            ->numeric()
                            ->dehydrated(true),
                        TextInput::make('discount')
                            ->numeric()
                            ->default(0)
                            ->reactive()
                            ->afterStateUpdated(
                                fn($state, $set, $get) =>
                                $set('grand_total', max(0, $get('total_amount') - $state))
                            ),
                        TextInput::make('grand_total')
                            ->disabled()
                            ->numeric()
                            ->dehydrated(true),
                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'confirmed' => 'Confirmed',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('draft')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state === 'completed') {
                                    $set('notes', 'Order diselesaikan pada ' . now());
                                }
                            }),
                        // TextInput::make('created_by')
                        //     ->numeric(),
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(2),
                    ])->columns(2),
                //
                Section::make('Detail Order')
                    ->schema([
                        Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Select::make('item_id')
                                    ->label('Item')
                                    ->options(Item::pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(
                                        fn($state, $set) =>
                                        $set('unit_price', Item::find($state)?->selling_price ?? 0)
                                    ),

                                Select::make('unit_id')
                                    ->label('Satuan')
                                    ->options(Unit::pluck('name', 'id'))
                                    ->required(),

                                TextInput::make('qty')
                                    ->numeric()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(
                                        fn($state, $set, $get) =>
                                        $set('line_total', (float)$state * (float)($get('unit_price') ?? 0))
                                    ),

                                TextInput::make('unit_price')
                                    ->numeric()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(
                                        fn($state, $set, $get) =>
                                        $set('line_total', (float)$get('qty') * (float)$state)
                                    ),

                                TextInput::make('line_total')
                                    ->disabled()
                                    ->numeric(),
                            ])
                            ->columns(3)
                            ->live()
                            ->afterStateUpdated(function ($set, $get) {
                                $total = collect($get('items'))
                                    ->sum(
                                        fn($i) =>
                                        (float) ($i['qty'] ?? 0) * (float) ($i['unit_price'] ?? 0)
                                    );
                                $set('total_amount', $total);
                                $set('grand_total', $total - (float)($get('discount') ?? 0));
                            })
                    ])
                    ->columnSpanFull(),

            ]);
    }
}
