<?php

namespace Endeavour\GroeigidsApiClient\Domain\Validator;

interface ResponseValidatorInterface
{
    public function validateData(string $classString, string $data): void;
}