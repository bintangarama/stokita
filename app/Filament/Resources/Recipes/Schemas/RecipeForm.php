<?php

namespace App\Filament\Resources\Recipes\Schemas;

use App\Models\Item;
use App\Models\Unit;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RecipeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Resep')
                    ->schema([
                        Select::make('item_id')
                            ->label('Item Jadi / Produk')
                            ->options(Item::pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        TextInput::make('name')
                            ->label('Nama Resep')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('yield_qty')
                            ->label('Hasil Produksi (Yield)')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->suffix(function (callable $get) {
                                $item = Item::find($get('item_id'));
                                return $item?->baseUnit?->name ?? '';
                            })
                            ->helperText('Jumlah hasil produksi yang dihasilkan oleh resep ini.'),
                        TextInput::make('version')
                            ->label('Versi Resep')
                            ->numeric()
                            ->default(1)
                            ->required(),
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->helperText('Nonaktifkan jika resep sudah tidak digunakan.'),
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(2),
                    ]),

                Section::make('Bahan / Igredients')
                    ->schema([
                        Repeater::make('ingredients')
                            ->relationship('ingredients')
                            ->label('Bahan dalam Resep')
                            ->schema([
                                Select::make('ingredient_item_id')
                                    ->label('Bahan')
                                    ->options(Item::whereIn('type', ['raw_material', 'component'])->pluck('name', 'id'))
                                    ->searchable()
                                    ->columnSpanFull()
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
                                    ->reactive(),
                                TextInput::make('cost')
                                    ->label('Perkiraan Biaya')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->reactive()
                                    ->formatStateUsing(
                                        fn($state, $get) =>
                                        number_format(
                                            (float)(($get('ingredientItem.average_cost') ?? 0) * ($get('qty') ?? 0)),
                                            2,
                                            ',',
                                            '.'
                                        )
                                    ),
                            ])
                            ->columns(2)
                            ->defaultItems(1)
                            ->createItemButtonLabel('Tambah Bahan'),
                    ]),
            ]);
    }
}
