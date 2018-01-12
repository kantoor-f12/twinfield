<?php
namespace PhpTwinfield\Request\Read;

use PhpTwinfield\Office;

class VatDataTransfer extends Read
{
    public function __construct(Office $office = null, $code = null)
    {
        parent::__construct();
        $this->add(
            'type',
            'vat'
        );
        if (null !== $office) {
            $this->setOffice($office->getCode());
        }

        if (null !== $code) {
            $this->setCode($code);
        }
    }

    /**
     * Sets the office code for this vat request.
     *
     * @access public
     *
     * @param string $office
     *
     * @return \PhpTwinfield\Request\Read\VatDataTransfer
     */
    public function setOffice(string $office)
    {
        $this->add(
            'office',
            $office
        );
        return $this;
    }

    /**
     * Sets the code for this vat request.
     *
     * @access public
     *
     * @param string $code
     *
     * @return \PhpTwinfield\Request\Read\VatDataTransfer
     */
    public function setCode($code)
    {
        $this->add(
            'code',
            $code
        );
        return $this;
    }
}