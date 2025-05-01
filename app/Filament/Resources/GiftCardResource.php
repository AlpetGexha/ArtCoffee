<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GiftCardResource\Pages;
use App\Models\GiftCard;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

final class GiftCardResource extends Resource
{
    protected static ?string $model = GiftCard::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    // protected static ?string $navigationGroup = 'Shop Management';

    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Gift Card Details')
                    ->schema([
                        Forms\Components\Select::make('sender_id')
                            ->label('Sender')
                            ->relationship('sender', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('recipient_id')
                            ->label('Recipient')
                            ->relationship('recipient', 'name')
                            ->searchable()
                            ->preload()
                            ->hiddenOn('create'),

                        Forms\Components\TextInput::make('recipient_email')
                            ->label('Recipient Email')
                            ->email()
                            ->required()
                            ->hiddenOn('edit'),

                        Forms\Components\TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->prefix('$')
                            ->minValue(1)
                            ->maxValue(1000)
                            ->step(0.01)
                            ->required(),

                        Forms\Components\TextInput::make('activation_key')
                            ->label('Activation Key')
                            ->maxLength(32)
                            ->default(fn () => Str::random(32))
                            ->disabled()
                            ->dehydrated()
                            ->helperText('This is automatically generated'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Message & Occasion')
                    ->schema([
                        Forms\Components\Select::make('occasion')
                            ->options([
                                'birthday' => 'Birthday',
                                'anniversary' => 'Anniversary',
                                'thank you' => 'Thank You',
                                'holiday' => 'Holiday',
                                'congratulations' => 'Congratulations',
                                'other' => 'Other',
                            ])
                            ->placeholder('Select an occasion'),

                        Forms\Components\Textarea::make('message')
                            ->label('Personal Message')
                            ->placeholder('Enter a personal message to the recipient')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Inactive gift cards cannot be redeemed'),

                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Expiration Date')
                            ->default(now()->addYear())
                            ->displayFormat('F j, Y \a\t g:i A'),

                        Forms\Components\DateTimePicker::make('redeemed_at')
                            ->label('Redemption Date')
                            ->placeholder('Not yet redeemed')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn (?GiftCard $record = null) => $record && $record->redeemed_at !== null),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sender.name')
                    ->label('Sender')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('recipient.name')
                    ->label('Recipient')
                    ->description(fn (GiftCard $record) => $record->recipient_email)
                    ->placeholder('Email Only')
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('activation_key')
                    ->label('Activation Key')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(function (GiftCard $record): string {
                        if (! $record->is_active) {
                            return 'inactive';
                        }
                        if ($record->isRedeemed()) {
                            return 'redeemed';
                        }
                        if ($record->isExpired()) {
                            return 'expired';
                        }

                        return 'active';
                    })
                    ->colors([
                        'danger' => 'inactive',
                        'warning' => 'expired',
                        'success' => 'redeemed',
                        'primary' => 'active',
                    ]),

                Tables\Columns\TextColumn::make('occasion')
                    ->label('Occasion')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Str::title($state))
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('redeemed_at')
                    ->label('Redeemed')
                    ->dateTime()
                    ->placeholder('Not Redeemed')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('sender')
                    ->relationship('sender', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'redeemed' => 'Redeemed',
                        'expired' => 'Expired',
                        'inactive' => 'Inactive',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['value'])) {
                            return $query;
                        }

                        return match ($data['value']) {
                            'active' => $query->valid(),
                            'redeemed' => $query->redeemed(),
                            'expired' => $query->expired(),
                            'inactive' => $query->where('is_active', false),
                            default => $query,
                        };
                    }),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->placeholder('From'),
                        Forms\Components\DatePicker::make('created_until')
                            ->placeholder('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('mark_redeemed')
                    ->label('Mark as Redeemed')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->hidden(fn (GiftCard $record) => $record->isRedeemed() || ! $record->isValid())
                    ->action(function (GiftCard $record) {
                        $record->redeemed_at = now();
                        $record->save();
                    }),
                Tables\Actions\Action::make('toggle_active')
                    ->icon(fn (GiftCard $record) => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (GiftCard $record) => $record->is_active ? 'danger' : 'success')
                    ->label(fn (GiftCard $record) => $record->is_active ? 'Deactivate' : 'Activate')
                    ->requiresConfirmation()
                    ->action(function (GiftCard $record) {
                        $record->is_active = ! $record->is_active;
                        $record->save();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate')
                        ->color('success')
                        ->icon('heroicon-o-check-circle')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                if (! $record->is_active) {
                                    $record->is_active = true;
                                    $record->save();
                                }
                            });
                        }),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate')
                        ->color('danger')
                        ->icon('heroicon-o-x-circle')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                if ($record->is_active) {
                                    $record->is_active = false;
                                    $record->save();
                                }
                            });
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGiftCards::route('/'),
            'create' => Pages\CreateGiftCard::route('/create'),
            'edit' => Pages\EditGiftCard::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['sender', 'recipient']);
    }
}
