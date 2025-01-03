<?php

namespace App\Filament\Widgets;

use App\Enums\GenderEnum;
use App\Models\Member;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = -2;

    protected function getStats(): array
    {
        $newRegistrations = Member::whereDate('created_at', today()->subDay())->count();
        return [
            Stat::make('Total', Member::count())
                ->description($newRegistrations ? "$newRegistrations new registrations" : null)
                ->color('success')
                ->icon('heroicon-m-user-group')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->descriptionIcon($newRegistrations ? "heroicon-m-arrow-trending-up" : null),


            Stat::make('Males', Member::where('gender', GenderEnum::MALE->value)->count())
                ->icon('heroicon-m-user')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Females', Member::where('gender', GenderEnum::FEMALE->value)->count())
                ->icon('heroicon-m-user-circle')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

        ];
    }
}
