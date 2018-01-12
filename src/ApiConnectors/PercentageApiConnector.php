<?php
namespace PhpTwinfield\ApiConnectors;

use PhpTwinfield\Mappers\PercentageMapper;
use PhpTwinfield\Office;
use PhpTwinfield\Request\Read\VatDataTransfer;

class PercentageApiConnector extends ProcessXmlApiConnector
{
    public function getPercentage(
        string $code,
        Office $office
    ){
        $vatInformation = new VatDataTransfer();
        $vatInformation->setOffice($office->getCode())
            ->setCode($code);
        $response = $this->sendDocument($vatInformation);

        return PercentageMapper::map(
            $response,
            $code
        );
    }
}