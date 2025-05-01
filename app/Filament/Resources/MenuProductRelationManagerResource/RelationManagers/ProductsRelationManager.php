<?php

namespace App\Filament\Resources\MenuProductRelationManagerResource\RelationManagers;

use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Table;

final class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('custom_price')
                    ->numeric()
                    ->label('Custom Price (€)')
                    ->nullable(),

                TextInput::make('discount_price')
                    ->numeric()
                    ->label('Discount Price (€)')
                    ->nullable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('pivot.custom_price')
                    ->label('Custom Price (€)'),
                Tables\Columns\TextColumn::make('pivot.discount_price')
                    ->label('Discount Price (€)'),
            ])
            ->headerActions([
                AttachAction::make()
                    ->form([
                        TextInput::make('menus')
                            ->required(),
                        TextInput::make('products')
                            ->label('Products')
                            ->required(),

                        Forms\Components\Select::make('product_id')
                            ->label('Product')
                            ->options(Product::query()->pluck('name', 'id'))
                            ->required(),

                        TextInput::make('pivot.custom_price')
                            ->numeric()
                            ->nullable(),

                        TextInput::make('pivot.discount_price')
                            ->numeric()
                            ->nullable(),
                    ])
                    ->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
                Tables\Actions\EditAction::make(),
            ])

            ->filters([

            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
