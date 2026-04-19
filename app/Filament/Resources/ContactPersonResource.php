<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactPersonResource\Pages;
use App\Models\ContactPerson;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContactPersonResource extends Resource
{
    protected static ?string $model = ContactPerson::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'General';

        protected static bool $shouldRegisterNavigation = false;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactPeople::route('/'),
            'create' => Pages\CreateContactPerson::route('/create'),
            'edit' => Pages\EditContactPerson::route('/{record}/edit'),
        ];
    }
}
