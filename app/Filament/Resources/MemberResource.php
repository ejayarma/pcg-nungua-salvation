<?php

namespace App\Filament\Resources;

use App\Enums\GenderEnum;
use App\Filament\Exports\MemberExporter;
use App\Filament\Resources\MemberResource\Pages;
use App\Models\Member;
use App\Models\State;
use CrescentPurchasing\FilamentAuditing\Filament\RelationManagers\AuditsRelationManager;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Table;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Registration';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationItems(): array
    {
        return [

            NavigationItem::make('Members')
                ->url(fn () => static::getUrl())
                ->icon(static::$navigationIcon)
                ->group(static::$navigationGroup)
                ->isActiveWhen(fn () => request()->is('*members*') && ! request()->is('*graduation*', '*birthday*')),

            NavigationItem::make('Graduation')
                ->url(fn () => static::getUrl('graduation'))
                ->icon('heroicon-o-academic-cap')
                ->group(static::$navigationGroup)
                ->isActiveWhen(fn () => request()->is('*/members/graduation')),

            NavigationItem::make("This Week's Birthdays")
                ->url(fn () => static::getUrl('this-week-birthdays'))
                ->icon('heroicon-o-cake')
                ->group(static::$navigationGroup)
                ->isActiveWhen(fn () => request()->is('*birthday*')),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Bio')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone')
                            ->alphaNum()
                            ->required()
                            ->maxLength(255),

                        Forms\Components\DatePicker::make('date_of_birth')
                            ->suffixIcon('heroicon-m-calendar')
                            ->maxDate(now())
                            ->minDate(now()->subYears(100))
                            ->native(false),
                        Forms\Components\TextInput::make('occupation')
                            ->maxLength(255),
                        Forms\Components\Select::make('generational_group_id')
                            ->relationship('generationalGroup', 'name')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                                Forms\Components\Textarea::make('description')
                                    ->required()
                                    ->maxLength(1000),
                            ])
                            ->searchable()
                            ->required()
                            ->preload(),
                        Forms\Components\Checkbox::make('is_communicant')
                            ->label('Communicant/Non-communicant'),
                        Forms\Components\Radio::make('gender')
                            ->options([
                                GenderEnum::MALE->value => 'Male',
                                GenderEnum::FEMALE->value => 'Female',
                            ])
                            ->enum(GenderEnum::class)
                            ->required(),
                    ]),

                Section::make('Address')
                    ->columns(2)
                    ->relationship('address')
                    ->schema([

                        Forms\Components\Select::make('state_id')
                            ->label('Region')
                            ->options(function (Get $get) {
                                return State::pluck('name', 'id');
                            })
                            ->searchable()
                            ->required()
                            // ->live()
                            ->preload(),
                        Forms\Components\TextInput::make('city')
                            ->label('Town/City')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('zip_code')
                            ->label('Zip/Postal Code')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('street')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('digital_address')
                            ->maxLength(255),
                    ]),
                Section::make('Contact Person')
                    ->columns(2)
                    ->relationship('contactPerson')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->maxLength(255)
                            ->required(),
                        Forms\Components\TextInput::make('phone')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_of_birth')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('occupation')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('is_communicant')
                    ->label('Communicant')
                    ->badge()
                    ->getStateUsing(function (Member $record) {
                        return $record->is_communicant ? 'Yes' : 'No';
                    })->colors([
                        'success' => fn ($state) => $state === 'Yes',
                        'danger' => fn ($state) => $state === 'No',
                    ]),
                Tables\Columns\TextColumn::make('generationalGroup.name')
                    ->label('Gen. Group')
                    ->searchable()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('rightful_generational_group')
                //     ->label('Rightful Gen. Group')
                //     ->getStateUsing(fn (Member $record): string => $record->getRightfulGroup())
                //     ->sortable(false)
                //     ->searchable(false),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(MemberExporter::class),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ExportBulkAction::make()->exporter(MemberExporter::class),
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
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'this-week-birthdays' => Pages\BirthdayMembers::route('/this-week-birthdays'),
            'graduation' => Pages\GraduationMembers::route('/graduation'),
            'view' => Pages\ViewMember::route('/{record}'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
        ];
    }
}
