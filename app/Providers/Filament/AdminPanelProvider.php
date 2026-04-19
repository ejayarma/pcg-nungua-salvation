<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\WelcomeWidget;
use CrescentPurchasing\FilamentAuditing\FilamentAuditingPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->registration()
            ->databaseNotifications()
            ->passwordReset()
            ->emailVerification()
            ->profile()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->navigationGroups([
                NavigationGroup::make('General'),
                NavigationGroup::make('Registration'),
                NavigationGroup::make('Communications'),
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                WelcomeWidget::class,
                Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
                StatsOverview::class,
            ])
            ->navigationItems([
                NavigationItem::make('This Week\'s Birthdays')
                    ->url(fn () => \App\Filament\Resources\MemberResource::getUrl('this-week-birthdays'))
                    ->icon('heroicon-o-cake')
                    ->group('Registration'),
                NavigationItem::make('Graduation')
                    ->url(fn () => \App\Filament\Resources\MemberResource::getUrl('graduation'))
                    ->icon('heroicon-o-arrow-up-right')
                    ->group('Registration'),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentAuditingPlugin::make()
                    ->restorePermission('restoreAudit'),
            ]);
    }
}
