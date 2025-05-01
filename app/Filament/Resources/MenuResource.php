<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Models\Menu;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

final class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required(),

                Forms\Components\Textarea::make('description')
                    ->nullable(),

                Select::make('products')
                    ->multiple()
                    ->relationship('products', 'name')
                    ->label('Products')
                    ->preload()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Clear pivot data when products change
                        $set('pivot_data', []);
                    }),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Menu Title'),

                TextColumn::make('products')
                    ->label('Products & Prices')
                    ->formatStateUsing(function ($record) {
                        return $record->products->map(function ($product) {
                            $price = $product->pivot->custom_price
                                ?? $product->pivot->discount_price
                                ?? $product->base_price;

                            $text = "{$product->name}: {$price}€";

                            if ($product->pivot->custom_price || $product->pivot->discount_price) {
                                $text .= " (base: {$product->base_price}€)";
                            }

                            return $text;
                        })->join('<br>');
                    })
                    ->html(),
            ])
            ->filters([])
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
            // Any relations you want to include here
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}
