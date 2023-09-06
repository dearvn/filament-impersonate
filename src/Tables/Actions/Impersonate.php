<?php

namespace Dearvn\FilamentImpersonate\Tables\Actions;

use Filament\Tables\Actions\Action;
use Dearvn\FilamentImpersonate\Concerns\Impersonates;

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
            ->hidden(fn ($record) => !$this->canBeImpersonated($record));
    }
}
