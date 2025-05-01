<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

final class ProductOptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'productOptions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('price_adjustment')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->step(0.01)
                    ->default(0)
                    ->helperText('Additional cost for this option. Use negative values for discounts.'),
                Forms\Components\Toggle::make('is_default')
                    ->default(false)
                    ->helperText('Is this the default option selection?'),
                Forms\Components\Select::make('option_group')
                    ->options([
                        'size' => 'Size',
                        'milk' => 'Milk Type',
                        'flavor' => 'Flavor Shot',
                        'temperature' => 'Temperature',
                        'topping' => 'Topping',
                        'special' => 'Special Request',
                        'other' => 'Other',
                    ])
                    ->required()
                    ->helperText('Group similar options together'),
                Forms\Components\Textarea::make('description')
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultGroup('option_group')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('option_group')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('price_adjustment')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_default')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('option_group')
                    ->options([
                        'size' => 'Size',
                        'milk' => 'Milk Type',
                        'flavor' => 'Flavor Shot',
                        'temperature' => 'Temperature',
                        'topping' => 'Topping',
                        'special' => 'Special Request',
                        'other' => 'Other',
                    ]),
                Tables\Filters\TernaryFilter::make('is_default'),
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
