<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use App\Models\Member;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GraduationMembers extends ListRecords
{
    protected static string $resource = MemberResource::class;

    protected static ?string $title = 'Graduation';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query
                    ->whereNotNull('date_of_birth')
                    ->where(function (Builder $query) {
                        $query->whereDoesntHave('generationalGroup')
                            ->orWhere(function (Builder $query) {
                                $query->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 12')
                                    ->whereHas('generationalGroup', fn ($query) => $query->where('name', '!=', 'Children Service'));
                            })
                            ->orWhere(function (Builder $query) {
                                $query->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) >= 12')
                                    ->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 18')
                                    ->whereHas('generationalGroup', fn ($query) => $query->where('name', '!=', 'JY'));
                            })
                            ->orWhere(function (Builder $query) {
                                $query->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) >= 18')
                                    ->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 30')
                                    ->whereHas('generationalGroup', fn ($query) => $query->where('name', '!=', 'YPG'));
                            })
                            ->orWhere(function (Builder $query) {
                                $query->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) >= 30')
                                    ->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 40')
                                    ->whereHas('generationalGroup', fn ($query) => $query->where('name', '!=', 'YAF'));
                            })
                            ->orWhere(function (Builder $query) {
                                $query->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) >= 40')
                                    ->where(function (Builder $query) {
                                        $query->whereHas('generationalGroup', fn ($query) => $query->where('name', '!=', "Men's Fellowship"))
                                            ->where('gender', 'MALE');
                                    })
                                    ->orWhere(function (Builder $query) {
                                        $query->whereHas('generationalGroup', fn ($query) => $query->where('name', '!=', "Women's Fellowship"))
                                            ->where('gender', 'FEMALE');
                                    });
                            });
                    });
            })
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
                    ->label('Birthday')
                    ->date()
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
                Tables\Columns\TextColumn::make('rightful_generational_group')
                    ->label('Rightful Gen. Group')
                    ->getStateUsing(fn (Member $record): string => $record->getRightfulGroup())
                    ->sortable(false)
                    ->searchable(false),
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
}
