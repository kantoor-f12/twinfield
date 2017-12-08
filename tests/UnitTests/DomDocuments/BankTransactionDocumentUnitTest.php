<?php
namespace PhpTwinfield\UnitTests;

use Money\Money;
use PhpTwinfield\DomDocuments\BankTransactionDocument;
use PhpTwinfield\Enums\DebitCredit;
use PhpTwinfield\Enums\Destiny;
use PhpTwinfield\BankTransaction;
use PhpTwinfield\Office;
use PhpTwinfield\Transactions\BankTransactionLine\Detail;
use PhpTwinfield\Transactions\BankTransactionLine\Total;

class BankTransactionDocumentUnitTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var BankTransactionDocument
     */
    protected $document;

    protected function setUp()
    {
        parent::setUp();

        $this->document = new BankTransactionDocument();
    }

    public function testXmlIsCreatedPerSpec()
    {
        $transaction = new BankTransaction();
        $transaction->setDestiny(Destiny::TEMPORARY());
        $transaction->setAutoBalanceVat(true);
        $transaction->setOffice(Office::fromCode("DEV-10000"));
        $transaction->setStartvalue(Money::EUR(0));

        $line1 = new Total();
        $line1->setValue(Money::EUR(121));
        $line1->setId(38861);
        $line1->setVatTotal(Money::EUR(21));
        $line1->setVatBaseTotal(Money::EUR(21));
        $line1->setVatRepTotal(Money::EUR(21));

        $line2 = new Detail();
        $line2->setValue(Money::EUR(100));
        $line2->setId(38862);
        $line2->setVatValue(Money::EUR(100)); // Not sure?
        $line2->setVatBaseValue(Money::EUR(100));
        $line2->setVatRepValue(Money::EUR(100));

        $line3 = new Total();
        $line3->setValue(Money::EUR(-100));
        $line3->setId(38863);
        $line3->setVatTotal(Money::EUR(21));
        $line3->setVatBaseTotal(Money::EUR(21));
        $line3->setVatRepTotal(Money::EUR(21));

        $transaction->setTransactions([$line1, $line2, $line3]);

        $line3->setComment("Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse facilisis lobortis arcu in tincidunt. Mauris urna enim, commodo nec feugiat quis, pharetra vel sem. Etiam ullamcorper eleifend tellus non viverra. Nulla facilisi. Donec sed orci aliquam.");

        $this->document->addBankTransaction($transaction);

        $this->assertXmlStringEqualsXmlString(<<<XML
<?xml version="1.0"?>
<transactions>
	<transaction autobalancevat="true" destiny="temporary">
		<header>
			<office>dev-10000</office>
			<currency>eur</currency>
			<startvalue>0.00</startvalue>
			<closevalue>1.21</closevalue>
		</header>
		<transactions>
			<transaction id="38861" type="total">
				<debitcredit>credit</debitcredit>
				<value>1.21</value>
				<vattotal>0.21</vattotal>
				<vatbasetotal>0.21</vatbasetotal>
				<vatreptotal>0.21</vatreptotal>
			</transaction>
			<transaction id="38862" type="detail">
				<debitcredit>credit</debitcredit>
				<value>1.00</value>
				<vatcode/>
				<vatvalue>1.00</vatvalue>
				<vatbasevalue>1.00</vatbasevalue>
				<vatrepvalue>1.00</vatrepvalue>
				<performancetype/>
				<performancecountry/>
				<performancevatnumber/>
			</transaction>
			<transaction id="38863" type="total">
				<debitcredit>debit</debitcredit>
				<value>1.00</value>
				<vattotal>0.21</vattotal>
				<vatbasetotal>0.21</vatbasetotal>
				<vatreptotal>0.21</vatreptotal>
				<comment>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse facilisis lobortis arcu in tincidunt. Mauris urna enim, commodo nec feugiat quis, pharetra vel sem. Etiam ullamcorper eleifend tellus non viverra. Nulla facilisi. Donec sed orci aliquam.</comment>
			</transaction>
		</transactions>
	</transaction>
</transactions>
XML
    ,$this->document->saveXML());
    }
}