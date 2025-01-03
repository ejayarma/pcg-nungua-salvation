<?php

namespace App\Filament\Widgets;

use App\Models\GenerationalGroup;
use Filament\Widgets\ChartWidget;

class MemberPerGroupBarChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';
    protected static ?string $maxHeight = '210px';


    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Members',
                    'data' => [
                        ...GenerationalGroup::withCount('members')->pluck('members_count')->toArray()
                    ],
                    'backgroundColor' => [
                        // 'rgb(255, 99, 132)',
                        // 'rgb(54, 162, 235)',
                        // 'rgb(255, 205, 86)'
                        "#FF6384", // Soft Red
                        "#36A2EB", // Bright Blue
                        "#FFCE56", // Warm Yellow
                        "#4BC0C0", // Teal Green
                        "#9966FF", // Soft Purple
                        "#FF9F40"  // Orange
                    ],
                ],
            ],
            'labels' => [
                ...GenerationalGroup::pluck('name')->toArray()
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
