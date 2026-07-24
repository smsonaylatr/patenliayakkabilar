<?php

namespace App\Filament\Resources\PotentialCustomers;

use App\Filament\Resources\PotentialCustomers\Pages\CreatePotentialCustomer;
use App\Filament\Resources\PotentialCustomers\Pages\EditPotentialCustomer;
use App\Filament\Resources\PotentialCustomers\Pages\ListPotentialCustomers;
use App\Filament\Resources\PotentialCustomers\Schemas\PotentialCustomerForm;
use App\Filament\Resources\PotentialCustomers\Tables\PotentialCustomersTable;
use App\Models\PotentialCustomer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PotentialCustomerResource extends Resource
{
    protected static ?string $model = PotentialCustomer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PotentialCustomerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PotentialCustomersTable::configure($table);
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
            'index' => ListPotentialCustomers::route('/'),
            'create' => CreatePotentialCustomer::route('/create'),
            'edit' => EditPotentialCustomer::route('/{record}/edit'),
        ];
    }
}
