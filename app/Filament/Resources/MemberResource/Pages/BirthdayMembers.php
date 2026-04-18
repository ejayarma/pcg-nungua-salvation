<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Date;

class BirthdayMembers extends ListRecords
{
    protected static string $resource = MemberResource::class;

    protected static ?string $title = 'This Week\'s Birthdays';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $startOfWeek = now()->startOfWeek();
                $endOfWeek = now()->endOfWeek();

                return $query
                    ->whereNotNull('date_of_birth')
                    ->whereRaw("TO_DATE(EXTRACT(YEAR FROM CURRENT_DATE) || '-' || EXTRACT(MONTH FROM date_of_birth) || '-' || EXTRACT(DAY FROM date_of_birth), 'YYYY-MM-DD') BETWEEN ? AND ?", [
                        $startOfWeek->format('Y-m-d'),
                        $endOfWeek->format('Y-m-d'),
                    ]);
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
                    ->color(fn ($record) => (Date::make($record->date_of_birth)->month == now()->month && Date::make($record->date_of_birth)->day == now()->day) ? 'success' : null)
                    ->sortable(),
                Tables\Columns\TextColumn::make('occupation')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('generationalGroup.name')
                    ->label('Generational Group')
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
}
