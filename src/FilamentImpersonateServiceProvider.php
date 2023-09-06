<?php

namespace Dearvn\FilamentImpersonate;

use Dearvn\FilamentImpersonate\Commands\FilamentImpersonateCommand;
use Dearvn\FilamentImpersonate\Testing\TestsFilamentImpersonate;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Filament\Support\Facades\FilamentView;
use Dearvn\FilamentImpersonate\Tables\Actions\Impersonate;
use BladeUI\Icons\Factory;


class FilamentImpersonateServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-impersonate';

    public static string $viewNamespace = 'filament-impersonate';

    
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasRoute('web')
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews()
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('dearvn/filament-impersonate');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function registeringPackage(): void
    {
        $this->clearStorage();

        $this->registerIcon();
    }

    protected function clearStorage(): void
    {
        session()->forget([
            'impersonate',
            'impersonate.impersonator',
            'impersonate.impersonated',
        ]);
    }

    public function packageRegistered(): void
    {
    }

    public function bootingPackage(): void
    {
        FilamentView::registerRenderHook(
            'panels::body.start',
            static fn (): string => Blade::render("<x-filament-impersonate::banner/>")
        );

        // For backwards compatibility we're going to load our views into the namespace we used to use as well.
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'impersonate');

        // Alias our table action for backwards compatibility.
        if (!class_exists(\Dearvn\FilamentImpersonate\Impersonate::class)) {
            class_alias(Impersonate::class, \Dearvn\FilamentImpersonate\Impersonate::class);
        }
    }

    public function packageBooted(): void
    {
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filament-impersonate/{$file->getFilename()}"),
                ], 'filament-impersonate-stubs');
            }
        }

        // Testing
        Testable::mixin(new TestsFilamentImpersonate());
    }

    protected function getAssetPackageName(): ?string
    {
        return 'dearvn/filament-impersonate';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            // AlpineComponent::make('filament-impersonate', __DIR__ . '/../resources/dist/components/filament-impersonate.js'),
            Css::make('filament-impersonate-styles', __DIR__ . '/../resources/dist/filament-impersonate.css'),
            Js::make('filament-impersonate-scripts', __DIR__ . '/../resources/dist/filament-impersonate.js'),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            FilamentImpersonateCommand::class,
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    protected function registerIcon(): void
    {
        $this->callAfterResolving(Factory::class, function (Factory $factory) {
            $factory->add('impersonate', [
                'path' => __DIR__.'/../resources/views/icons',
                'prefix' => 'impersonate',
            ]);
        });
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            'create_filament-impersonate_table',
        ];
    }
}
