<?php

namespace App\Http\Controllers;

use Spatie\LaravelMobilePass\Builders\PassBuilder;
use Spatie\LaravelMobilePass\Enums\PassType;
use Spatie\LaravelMobilePass\Validators\GenericPassValidator;
use Spatie\LaravelMobilePass\Validators\PassValidator;

class EventTicketPassBuilder extends PassBuilder
{
    protected PassType $type = PassType::EventTicket;

    protected static function validator(): PassValidator
    {
        return new GenericPassValidator; // Usar el validador genérico
    }

    protected function compileData(): array
    {
        return array_merge(
            parent::compileData(),
            [
                'eventTicket' => array_filter([
                    'primaryFields' => $this->primaryFields?->values()->toArray(),
                    'secondaryFields' => $this->secondaryFields?->values()->toArray(),
                    'headerFields' => $this->headerFields?->values()->toArray(),
                    'auxiliaryFields' => $this->auxiliaryFields?->values()->toArray(),
                    'backFields' => $this->backFields?->values()->toArray(),
                ]),
            ],
        );
    }
}
