<?php

namespace App\Filament\Exports;

use App\Models\Member;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Str;

class MemberExporter extends Exporter
{
    protected static ?string $model = Member::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label('Name'),
            ExportColumn::make('phone')->label('Phone'),
            ExportColumn::make('gender')->label('Gender'),
            ExportColumn::make('email')->label('Email'),
            ExportColumn::make('date_of_birth')->label('Date of Birth'),
            ExportColumn::make('occupation')->label('Occupation'),
            ExportColumn::make('is_communicant')
                ->label('Communicant')
                ->getStateUsing(function (Member $record) {
                    return $record->is_communicant ? 'Yes' : 'No';
                }),
            ExportColumn::make('generationalGroup.name')->label('Generational Group'),
            ExportColumn::make('rightful_generational_group')
                ->label('Rightful Gen. Group')
                ->getStateUsing(function (Member $record) {

                    $genGroup = '';

                    if (! $record->date_of_birth) {
                        return 'Unknown';
                    }

                    $age = now()->diffInYears($record->date_of_birth, true);

                    if ($age < 12) {
                        $genGroup = 'Children Service';
                    } elseif ($age >= 12 && $age < 18) {
                        $genGroup = 'JY';
                    } elseif ($age >= 18 && $age < 30) {
                        $genGroup = 'YPG';
                    } elseif ($age >= 30 && $age < 40) {
                        $genGroup = 'YAF';
                    } else {
                        $genGroup = Str::upper($record->gender) === 'MALE' ? "Men's Fellowship" : "Women's Fellowship";
                    }

                    return $genGroup;
                }),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your member export has completed and '.number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }

    public function getFileName(Export $export): string
    {
        return 'members-export-'.now()->format('Y-m-d-H-i-s');

    }
}
