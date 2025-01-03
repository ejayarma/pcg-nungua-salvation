<?php

namespace App\Filament\Resources;

use App\Enums\GenderEnum;
use App\Filament\Resources\MemberResource\Pages;
use App\Filament\Resources\MemberResource\RelationManagers;
use App\Models\Member;
use App\Models\State;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Nnjeim\World\Models\Country;

use function Illuminate\Log\log;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
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
                                GenderEnum::FEMALE->value  => 'Female'
                            ])
                            ->enum(GenderEnum::class)
                            ->required(),
                    ]),


                Section::make('Address')
                    ->columns(2)
                    ->relationship('address')
                    ->schema([
                        // Forms\Components\Select::make('country_id')
                        //     ->label('Country')
                        //     ->options(Country::pluck('name', 'id'))
                        //     ->options(function (Get $get, Set $set) {
                        //         $stateId = $get('state_id');

                        //         if ($stateId) {
                        //             $set(
                        //                 'country_id',
                        //                 State::query()
                        //                     ->with('country')
                        //                     ->find($stateId, ['country_id'])->country_id
                        //             );
                        //         }

                        //         return Country::pluck('name', 'id');
                        //     })
                        //     ->searchable()
                        //     ->required()
                        //     ->live()
                        //     ->preload(),
                        Forms\Components\Select::make('state_id')
                            ->label('State/Province/Region')
                            ->options(function (Get $get) {
                                $countryId = $get('country_id');

                                return State::query()
                                    // ->when($countryId, function ($query) use ($countryId) {
                                    //     $query->where('country_id', $countryId);
                                    // })
                                    ->where('country_id', $countryId ?? 84)
                                    ->pluck('name', 'id');
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
                    ])


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
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'view' => Pages\ViewMember::route('/{record}'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
        ];
    }
}
