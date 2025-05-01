<?php

namespace App\Filament\Resources;

use App\Enum\OrderStatus;
use App\Enum\PaymentStatus;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    // protected static ?string $navigationGroup = 'Order Management';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'order_number';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Information')
                    ->schema([
                        // Forms\Components\TextInput::make('order_number')
                        //     ->required()
                        //     ->maxLength(255)
                        //     ->default(fn () => 'ORD-' . mb_strtoupper(mb_substr(md5(time()), 0, 8)))
                        //     ->disabled()
                        //     ->dehydrated(),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('branch_id')
                            ->relationship('branch', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('table_id')
                            ->relationship('table', 'table_number')
                            ->searchable()
                            ->preload()
                            ->label('Table Number'),
                    ])->columns(2),

                Forms\Components\Section::make('Order Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->required()
                            ->options(OrderStatus::class)
                            ->default(OrderStatus::PENDING),
                        Forms\Components\Select::make('payment_status')
                            ->required()
                            ->options(PaymentStatus::class)
                            ->default(PaymentStatus::PENDING),
                        Forms\Components\TextInput::make('total_amount')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->default(0)
                            ->disabled()
                            ->dehydrated(),
                    ])->columns(3),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        // Forms\Components\Toggle::make('is_takeaway')
                        //     ->required()
                        //     ->default(false)
                        //     ->label('Takeaway Order'),
                        Forms\Components\Textarea::make('special_instructions')
                            ->maxLength(500),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('branch.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('table.table_number')
                    ->label('Table')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->badge(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_takeaway')
                    ->boolean()
                    ->label('Takeaway'),
                Tables\Columns\TextColumn::make('ordered_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(OrderStatus::class),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options(PaymentStatus::class),
                Tables\Filters\SelectFilter::make('branch_id')
                    ->relationship('branch', 'name')
                    ->label('Branch'),
                Tables\Filters\TernaryFilter::make('is_takeaway')
                    ->label('Takeaway Orders'),
                Tables\Filters\Filter::make('ordered_at')
                    ->form([
                        Forms\Components\DatePicker::make('ordered_from'),
                        Forms\Components\DatePicker::make('ordered_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['ordered_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('ordered_at', '>=', $date),
                            )
                            ->when(
                                $data['ordered_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('ordered_at', '<=', $date),
                            );
                    }),
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
            // RelationManagers\OrderItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
