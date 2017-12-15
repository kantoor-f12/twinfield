<?php

namespace PhpTwinfield\Mappers;

use PhpTwinfield\Percentage;
use PhpTwinfield\Response\Response;

class PercentageMapper
{
    public static function map(Response $response, string $code)
    {
        $responseDOM = $response->getResponseDocument();

        $percentages = [];
        foreach ($responseDOM->getElementsByTagName('percentage') as $percentage) {
            $dateField = $percentage->getElementsByTagName('date')
                ->item(0);
            $percentageField = $percentage->getElementsByTagName('percentage')
                ->item(0);
            $createdField = $percentage->getElementsByTagName('created')
                ->item(0);
            $nameField = $percentage->getElementsByTagName('name')
                ->item(0);
            $shortnameField = $percentage->getElementsByTagName('shortname')
                ->item(0);
            $userField = $percentage->getElementsByTagName('user')
                ->item(0);

            $vatPercentage = new Percentage();
            $vatPercentage->vatCode = $code;
            $vatPercentage->status = $percentage->getAttribute('status');
            $vatPercentage->inuse = $percentage->getAttribute('inuse');
            $vatPercentage->date = $dateField->textContent;
            $vatPercentage->percentage = $percentageField->textContent;
            $vatPercentage->created = $createdField->textContent;
            $vatPercentage->name = $nameField->textContent;
            $vatPercentage->shortname = $shortnameField->textContent;
            $vatPercentage->user = $userField->textContent;

            $percentages[] = $vatPercentage;
        }
        return $percentages;
    }
}