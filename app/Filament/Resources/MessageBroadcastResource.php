<?php

namespace App\Filament\Resources;

use App\Enums\MessageBroadcastRecipientEnum;
use App\Enums\MessageBroadcastStatusEnum;
use App\Filament\Resources\MessageBroadcastResource\Pages;
use App\Models\GenerationalGroup;
use App\Models\Member;
use App\Models\MessageBroadcast;
use App\Services\SmsDispatchService;
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
use Illuminate\Support\HtmlString;

class MessageBroadcastResource extends Resource
{
    protected static ?string $model = MessageBroadcast::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Communications';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('message')
                    ->required()
                    ->live()
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
                    ])
                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state, callable $get) => $set('recipients', [])),

                Forms\Components\Select::make('recipients')
                    ->visible(fn (callable $get) => $get('recipient_group') === 'GENERATIONAL_GROUP')
                    ->requiredIf('recipient_group', 'GENERATIONAL_GROUP')
                    ->multiple()
                    ->live()
                    ->options(GenerationalGroup::pluck('name', 'id')),

                Forms\Components\Select::make('recipients')
                    ->visible(fn (callable $get) => $get('recipient_group') === 'CUSTOM')
                    ->requiredIf('recipient_group', 'CUSTOM')
                    ->live()
                    ->multiple()
                    ->options(Member::pluck('name', 'id')),

                Forms\Components\Placeholder::make('sms_cost')
                    ->content(fn (callable $get) => self::getSmsCostMessage(
                        $get('message'),
                        $get('recipient_group'),
                        $get('recipients'),
                    ))
                    ->visible(fn (callable $get) => ($get('status') === \App\Enums\MessageBroadcastStatusEnum::PENDING->value || $get('status') === null) && $get('medium') === 'SMS')
                    ->columnSpanFull(),

                Forms\Components\DateTimePicker::make('scheduled_at')
                    ->required()
                    ->default(now()->addMinutes(10)->startOfMinute())
                    ->minDate(now()->addMinutes(5)->startOfMinute()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->header(fn () => view('filament.message-broadcast-sms-balance-card', [
                'balance' => app(SmsDispatchService::class)->getBalance(),
            ]))
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

    protected static function getSmsCostMessage(?string $message, ?string $recipientGroup, ?array $recipients): HtmlString|string
    {
        if (! filled($message)) {
            return 'Enter the SMS message to calculate the estimated cost and balance status.';
        }

        $recipientCount = self::getSmsRecipientCount($recipientGroup, $recipients);

        if ($recipientCount === 0) {
            return 'Select recipients to calculate SMS cost and balance status.';
        }

        $smsService = app(SmsDispatchService::class);
        $balance = $smsService->getBalance();
        $estimatedCost = $smsService->getEstimatedCost($recipientCount, $message);
        $hasEnoughBalance = $balance >= $estimatedCost;
        $statusColor = $hasEnoughBalance ? '!text-green-600 !dark:text-green-400' : '!text-red-600 !dark:text-red-400';
        $statusBgColor = $hasEnoughBalance ? '!bg-green-50 !dark:bg-green-900/20' : '!bg-red-50 !dark:bg-red-900/20';
        $statusBorderColor = $hasEnoughBalance ? '!border-green-200 !dark:border-green-900/40' : '!border-red-200 !dark:border-red-900/40';
        $statusText = $hasEnoughBalance ? 'Enough balance' : 'Insufficient balance';

        return new HtmlString(
            sprintf(
                '<div class="rounded-lg %s border %s p-4 space-y-2">'.
                '<div class="flex flex-wrap gap-6 text-sm">'.
                '<div><span class="font-medium text-gray-600 dark:text-gray-400">Recipients:</span> <span class="font-semibold text-gray-900 dark:text-white">%d</span></div>'.
                '<div><span class="font-medium text-gray-600 dark:text-gray-400">Cost:</span> <span class="font-semibold text-gray-900 dark:text-white">GHS %s</span></div>'.
                '<div><span class="font-medium text-gray-600 dark:text-gray-400">Balance:</span> <span class="font-semibold text-gray-900 dark:text-white">GHS %s</span></div>'.
                '</div>'.
                '<div class="flex items-center gap-2 pt-2 border-t %s">'.
                '<span class="inline-block w-3 h-3 rounded-full %s"></span>'.
                '<span class="font-semibold %s text-base">%s</span>'.
                '</div>'.
                '</div>',
                $statusBgColor,
                $statusBorderColor,
                $recipientCount,
                number_format($estimatedCost, 2),
                number_format($balance, 2),
                $hasEnoughBalance ? '!border-green-200 !dark:border-green-900/40' : '!border-red-200 !dark:border-red-900/40',
                $statusColor,
                $statusColor,
                $statusText,
            )
        );
    }

    protected static function getSmsRecipientCount(?string $recipientGroup, ?array $recipients): int
    {
        return match ($recipientGroup) {
            MessageBroadcastRecipientEnum::ALL->value => Member::count(),
            MessageBroadcastRecipientEnum::GENERATIONAL_GROUP->value => Member::whereIn('generational_group_id', $recipients ?? [])->count(),
            MessageBroadcastRecipientEnum::CUSTOM->value => count($recipients ?? []),
            default => 0,
        };
    }
}
