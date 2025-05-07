<?php

namespace App\Filament\Resources;

use App\Enum\ProductCategory;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

final class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cake';

    protected static ?string $navigationGroup = 'Menu Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Product Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('category')
                            ->options(ProductCategory::class)
                            ->required(),
                        Forms\Components\TextInput::make('base_price')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01),
                        Forms\Components\Toggle::make('is_available')
                            ->required()
                            ->default(true),
                        Forms\Components\Toggle::make('is_customizable')
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->autosize()
                            ->rows(4)
                            ->columnSpanFull(),

                        SpatieMediaLibraryFileUpload::make('product_images')
                            ->collection('product_images')
                            ->image()
                            ->responsiveImages()
                            ->conversion('thumb')
                            ->imageEditor()
                            ->directory('products')
                            ->visibility('public')
                            ->columnSpanFull(),
                    ])->columns(3),

                Forms\Components\Section::make('Product Details')
                    ->schema([
                        // Spatie Media Library
                        // Forms\Components\FileUpload::make('image_url')
                        //     ->image()
                        //     ->directory('products')
                        //     ->columnSpanFull(),

                        Forms\Components\TextInput::make('preparation_time_minutes')
                            ->numeric()
                            ->suffix('minutes')
                            ->required(),

                        Forms\Components\TextInput::make('loyalpoints_per_item')
                            ->label('Loyalty Points')
                            ->helperText('Points earned per item purchased')
                            ->numeric(),

                    ])->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('category', 'asc')
            ->columns([
                SpatieMediaLibraryImageColumn::make('product_images')
                    ->collection('product_images')
                    ->label('Image')
                    ->circular()
                    ->height(80),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->weight(FontWeight::Bold),
                Tables\Columns\TextColumn::make('category')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        ProductCategory::COFFEE->value => 'warning',
                        ProductCategory::TEA->value => 'success',
                        ProductCategory::PASTRY->value => 'danger',
                        ProductCategory::SNACK->value => 'gray',
                        ProductCategory::MERCHANDISE->value => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('base_price')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('loyalpoints_per_item')
                    ->label('Loyalty Points')
                    ->sortable()
                    ->numeric()
                    ->suffix('pts'),

                Tables\Columns\IconColumn::make('is_available')
                    ->boolean()
                    ->label('Available')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->label('Featured')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_customizable')
                    ->boolean()
                    ->label('Customizable')
                    ->sortable()
                    ->toggleable(),
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
                Tables\Filters\SelectFilter::make('category')
                    ->options(ProductCategory::class),
                Tables\Filters\TernaryFilter::make('is_available'),
                Tables\Filters\TernaryFilter::make('is_featured'),
                Tables\Filters\TernaryFilter::make('is_customizable'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('toggle_availability')
                        ->label('Toggle Availability')
                        ->icon('heroicon-o-power')
                        ->action(function (Collection $records): void {
                            foreach ($records as $record) {
                                $record->update([
                                    'is_available' => ! $record->is_available,
                                ]);
                            }
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ProductOptionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
