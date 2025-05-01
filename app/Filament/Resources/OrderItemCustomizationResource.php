<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderItemCustomizationResource\Pages;
use App\Filament\Resources\OrderItemCustomizationResource\RelationManagers;
use App\Models\OrderItemCustomization;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderItemCustomizationResource extends Resource
{
    protected static ?string $model = OrderItemCustomization::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('order_item_id')
                    ->relationship('orderItem', 'id')
                    ->required(),
                Forms\Components\Select::make('product_option_id')
                    ->relationship('productOption', 'id')
                    ->required(),
                Forms\Components\TextInput::make('option_price')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('orderItem.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('productOption.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('option_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListOrderItemCustomizations::route('/'),
            'create' => Pages\CreateOrderItemCustomization::route('/create'),
            'edit' => Pages\EditOrderItemCustomization::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
