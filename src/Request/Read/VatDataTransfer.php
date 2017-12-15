<?php
namespace PhpTwinfield\Request\Read;

 use PhpTwinfield\Office;

class VatDataTransfer extends Read
{
    public function __construct($office, $code) {
        parent::__construct();
        $this->add('type', 'vat');
        $this->add('office', $office);
        $this->add('code', $code);
    }

    /**
     * Sets the office code for this vat request.
     *
     * @access public
     * @param Office $office
     * @return \PhpTwinfield\Request\Read\VatDataTransfer
     */
    public function setOffice(Office $office)
    {
        $this->add('office', $office->getCode());
        return $this;
    }

    /**
     * Sets the code for this vat request.
     *
     * @access public
     * @param string $code
     * @return \PhpTwinfield\Request\Read\VatDataTransfer
     */
    public function setCode($code)
    {
        $this->add('code', $code);
        return $this;
    }
}