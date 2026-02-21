<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MessageBroadcastResource\Pages;
use App\Models\GenerationalGroup;
use App\Models\Member;
use App\Models\MessageBroadcast;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                    ->live()
                    ->options([
                        'ALL' => 'All Users',
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
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'SENT' => 'success',
                        'FAILED' => 'danger',
                        default => 'warning',
                    }),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
