<?php

namespace App\Filament\Resources\UnitConversions;

use App\Filament\Resources\UnitConversions\Pages\CreateUnitConversion;
use App\Filament\Resources\UnitConversions\Pages\EditUnitConversion;
use App\Filament\Resources\UnitConversions\Pages\ListUnitConversions;
use App\Filament\Resources\UnitConversions\Pages\ViewUnitConversion;
use App\Filament\Resources\UnitConversions\Schemas\UnitConversionForm;
use App\Filament\Resources\UnitConversions\Schemas\UnitConversionInfolist;
use App\Filament\Resources\UnitConversions\Tables\UnitConversionsTable;
use App\Models\UnitConversion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class UnitConversionResource extends Resource
{
    protected static ?string $model = UnitConversion::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationLabel = 'Konversi Satuan';
    protected static string | UnitEnum | null $navigationGroup = 'DATA MASTER';
    protected static ?string $modelLabel = 'Konversi Satuan';
    protected static ?string $slug = 'konversi-satuan';

    protected static ?string $recordTitleAttribute = 'unit_from_id';

    public static function form(Schema $schema): Schema
    {
        return UnitConversionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UnitConversionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UnitConversionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUnitConversions::route('/'),
            'create' => CreateUnitConversion::route('/create'),
            'view' => ViewUnitConversion::route('/{record}'),
            'edit' => EditUnitConversion::route('/{record}/edit'),
        ];
    }
}
