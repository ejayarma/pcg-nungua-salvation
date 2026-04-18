<?php

namespace App\Filament\Resources;

use App\Enums\MessageBroadcastStatusEnum;
use App\Filament\Resources\MessageBroadcastResource\Pages;
use App\Models\GenerationalGroup;
use App\Models\Member;
use App\Models\MessageBroadcast;
use CrescentPurchasing\FilamentAuditing\Filament\RelationManagers\AuditsRelationManager;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

class MessageBroadcastResource extends Resource
{
    protected static ?string $model = MessageBroadcast::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('message')
                    ->required()
                    ->rows(5)
                    ->columnSpanFull()
                    ->visible(fn (callable $get) => $get('medium') === 'SMS'),

                Forms\Components\RichEditor::make('message')
                    ->required()
                    ->columnSpanFull()
                    ->visible(fn (callable $get) => $get('medium') === 'EMAIL'),

                Forms\Components\Select::make('medium')
                    ->required()
                    ->options([
                        'SMS' => 'SMS',
                        'EMAIL' => 'Email',
                    ])
                    ->live()
                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state, callable $get) => $state === 'SMS' && $set('message', strip_tags($get('message')))),

                Forms\Components\Select::make('recipient_group')
                    ->required()
                    ->live()
                    ->options([
                        'ALL' => 'All Members',
                        'GENERATIONAL_GROUP' => 'Generational Group',
                        'CUSTOM' => 'Custom',
                    ]),

                Forms\Components\Select::make('recipients')
                    ->visible(fn (callable $get) => $get('recipient_group') === 'GENERATIONAL_GROUP')
                    ->requiredIf('recipient_group', 'GENERATIONAL_GROUP')
                    ->multiple()
                    ->options(GenerationalGroup::pluck('name', 'id')),

                Forms\Components\Select::make('recipients')
                    ->visible(fn (callable $get) => $get('recipient_group') === 'CUSTOM')
                    ->requiredIf('recipient_group', 'CUSTOM')
                    ->multiple()
                    ->options(Member::pluck('name', 'id')),

                Forms\Components\DateTimePicker::make('scheduled_at')
                    ->required()
                    ->default(now()->addMinutes(10)->startOfMinute())
                    ->minDate(now()->addMinutes(5)->startOfMinute()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('medium')->searchable()->sortable(),
                TextColumn::make('created_by')->getStateUsing(function (MessageBroadcast $record) {
                    return $record->creator?->name;
                })->label('Created By')->searchable()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('scheduled_at')->dateTime()->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (MessageBroadcastStatusEnum $state): string => match ($state) {
                        MessageBroadcastStatusEnum::SENT => 'success',
                        MessageBroadcastStatusEnum::FAILED => 'danger',
                        default => 'warning',
                    }),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn (MessageBroadcast $record) => $record->status === \App\Enums\MessageBroadcastStatusEnum::PENDING && Date::make($record->scheduled_at)->isFuture()),
                Tables\Actions\Action::make('retry')
                    ->label('Retry')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn (MessageBroadcast $record) => $record->status === \App\Enums\MessageBroadcastStatusEnum::FAILED)
                    ->form([
                        Forms\Components\DateTimePicker::make('scheduled_at')
                            ->label('Reschedule For')
                            ->required()
                            ->default(now()->addMinutes(5)->startOfMinute())
                            ->minDate(now()->addMinutes(5)->startOfMinute())
                            ->native(false),
                    ])
                    ->action(function (MessageBroadcast $record, array $data) {
                        $record->update([
                            'scheduled_at' => $data['scheduled_at'],
                            'status' => MessageBroadcastStatusEnum::PENDING,
                        ]);
                    })
                    ->successNotificationTitle('Broadcast will be retried at the scheduled time'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('retry')
                        ->label('Retry Failed Broadcasts')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(function (MessageBroadcast $record) {
                                if ($record->status === MessageBroadcastStatusEnum::FAILED) {
                                    $record->update([
                                        'scheduled_at' => now()->addMinutes(5)->startOfMinute(),
                                        'status' => MessageBroadcastStatusEnum::PENDING,
                                    ]);
                                }
                            });
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Selected broadcasts will be retried'),
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn (MessageBroadcast $record) => $record->status === \App\Enums\MessageBroadcastStatusEnum::PENDING && Date::make($record->scheduled_at)->isFuture()),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AuditsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMessageBroadcasts::route('/'),
            'create' => Pages\CreateMessageBroadcast::route('/create'),
            'edit' => Pages\EditMessageBroadcast::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
