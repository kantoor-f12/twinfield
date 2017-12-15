<?php
namespace PhpTwinfield\ApiConnectors;

use PhpTwinfield\Mappers\PercentageMapper;
use PhpTwinfield\Request\Read\VatDataTransfer;

class PercentageApiConnector extends ProcessXmlApiConnector
{
    public function getPercentage(
        string $office,
        string $code
    ){
        $vatInformation = new VatDataTransfer(
            $office,
            $code
        );
        $response = $this->sendDocument($vatInformation);
        return PercentageMapper::map($response, $code);
    }
}