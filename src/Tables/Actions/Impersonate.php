<?php

namespace Dearvn\FilamentImpersonate\Tables\Actions;

use Dearvn\FilamentImpersonate\Concerns\Impersonates;
use Filament\Tables\Actions\Action;

class Impersonate extends Action
{
    use Impersonates;

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->iconButton()
            ->icon('impersonate-icon')
            ->action(fn ($record) => $this->impersonate($record))
            ->hidden(fn ($record) => ! $this->canBeImpersonated($record));
    }
}
