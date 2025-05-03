<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

final class ProductOptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'product_options';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('option_name')
                    ->required(),
                Forms\Components\TextInput::make('option_category')
                    ->required(),
                Forms\Components\TextInput::make('additional_price')
                    ->required()
                    ->prefix('$')
                    ->step(0.01)
                    ->default(0),
                Forms\Components\TextInput::make('display_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_available')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('option_name')
            ->columns([
                Tables\Columns\TextColumn::make('option_name'),
                Tables\Columns\TextColumn::make('option_category'),
                Tables\Columns\TextColumn::make('additional_price'),
                Tables\Columns\TextColumn::make('is_available'),
                Tables\Columns\TextColumn::make('display_order'),
            ])
            ->filters([
                //
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
