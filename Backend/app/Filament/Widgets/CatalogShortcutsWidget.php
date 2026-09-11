<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\AnimalCategoryResource;
use App\Filament\Resources\AnimalResource;
use App\Filament\Resources\CityResource;
use App\Filament\Resources\CountryResource;
use App\Filament\Resources\GovernorateResource;
use App\Filament\Resources\RegionResource;
use App\Filament\Resources\VaccineResource;
use Filament\Widgets\Widget;

class CatalogShortcutsWidget extends Widget
{
    protected static bool $isDiscovered = false;

    protected static string $view = 'filament.widgets.catalog-shortcuts-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    /**
     * @return array{items: list<array{url: string, label: string, icon: string}>}
     */
    protected function getViewData(): array
    {
        $items = [
            [
                'url' => CountryResource::getUrl(),
                'label' => __('filament.widgets.catalog.country'),
                'icon' => 'heroicon-o-globe-alt',
            ],
            [
                'url' => GovernorateResource::getUrl(),
                'label' => __('filament.widgets.catalog.governorate'),
                'icon' => 'heroicon-o-building-office',
            ],
            [
                'url' => CityResource::getUrl(),
                'label' => __('filament.widgets.catalog.city'),
                'icon' => 'heroicon-o-building-office-2',
            ],
            [
                'url' => RegionResource::getUrl(),
                'label' => __('filament.widgets.catalog.region'),
                'icon' => 'heroicon-o-map',
            ],
            [
                'url' => AnimalCategoryResource::getUrl(),
                'label' => __('filament.widgets.catalog.animal_category'),
                'icon' => 'heroicon-o-tag',
            ],
            [
                'url' => AnimalResource::getUrl(),
                'label' => __('filament.widgets.catalog.animal'),
                'icon' => 'heroicon-o-heart',
            ],
            [
                'url' => VaccineResource::getUrl(),
                'label' => __('filament.widgets.catalog.vaccine'),
                'icon' => 'heroicon-o-beaker',
            ],
        ];

        return ['items' => $items];
    }
}
