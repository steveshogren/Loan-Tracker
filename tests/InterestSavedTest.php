<?php
class InterestSavedTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var InterestSaved
     */
    private $_InterestSaved;

    /**
     * @var Factory
     */
    private $_Factory;

    public function setUp()
    {
        parent::setUp();
        $this->_Factory = new Factory();
        $this->_InterestSaved = $this->_Factory->buildInterestSaved();
    }

    public function test_construct_setsValue()
    {
        $InterestSaved = $this->_Factory->buildInterestSaved();
        $LoanRepository = $this->_Factory->buildLoanRepository();
        $this->assertAttributeEquals($LoanRepository, '_LoanRepository', $InterestSaved);
    }

    public function test_getInterestSaved()
    {
        $Loan = $this->_Factory->buildLoan();
        $Loan->setInterest(5);
        $Loan->setAmount(100000);

        $returnedInterestSaved = $this->_InterestSaved->getInterestSaved($Loan);
        $expectedInterestSaved = 0;

        $this->assertEquals(
            $expectedInterestSaved,
            $returnedInterestSaved,
            'Was expecting to see that no interest was saved.'
        );
    }

    public function test_getInterestSaved_interestZero()
    {
        $Loan = $this->_Factory->buildLoan();
        $Loan->setInterest(0);
        $Loan->setAmount(100000);

        $returnedInterestSaved = $this->_InterestSaved->getInterestSaved($Loan);
        $expectedInterestSaved = 0;

        $this->assertEquals(
            $expectedInterestSaved,
            $returnedInterestSaved,
            'Was expecting to see that no interest was saved.'
        );
    }

    public function test_getInterestSaved_someSaved()
    {
        $Loan = $this->_Factory->buildLoan();
        $Loan->setInterest(5);
        $Loan->setAmount(100000);
        $Loan->setAmount(75000);

        $returnedInterestSaved = $this->_InterestSaved->getInterestSaved($Loan);
        $expectedInterestSaved = 10585;

        $this->assertEquals(
            $expectedInterestSaved,
            $returnedInterestSaved,
            "Was expecting to see that {$expectedInterestSaved} interest was saved."
        );
    }

    public function test_getInterestSaved_allSaved()
    {
        $Loan = $this->_Factory->buildLoan();
        $Loan->setInterest(5);
        $Loan->setAmount(100000);
        $Loan->setAmount(0);

        $returnedInterestSaved = $this->_InterestSaved->getInterestSaved($Loan);
        $expectedInterestSaved = 42342;

        $this->assertEquals(
            $expectedInterestSaved,
            $returnedInterestSaved,
            "Was expecting to see that {$expectedInterestSaved} interest was saved."
        );
    }

    public function test_totalInterestSaved_noLoansExist()
    {
        $Loans = array();
        $LoanRepository = $this->_getLoanRepoThatReturns($Loans);

        $InterestSaved = new InterestSaved($LoanRepository);
        $totalSaved = $InterestSaved->getTotalInterestSaved();
        $expectedInterestSaved = 0;
        $this->assertEquals(
            $expectedInterestSaved,
            $totalSaved,
            'Was expecting that no interest would be saved if no Loans exist.'
        );
    }

    public function test_totalInterestSaved_aLoanExists()
    {
        $Loans = array();
        $Loan = $this->_Factory->buildLoan();
        $Loan->setInterest(5);
        $Loan->setAmount(100000);
        $Loan->setAmount(0);
        $Loans[] = $Loan;

        $LoanRepository = $this->_getLoanRepoThatReturns($Loans);

        $InterestSaved = new InterestSaved($LoanRepository);
        $totalSaved = $InterestSaved->getTotalInterestSaved();
        $expectedInterestSaved = 42342;

        $this->assertEquals(
            $expectedInterestSaved,
            $totalSaved,
            'Was expecting that no interest would be saved if no Loans exist.'
        );
    }

    /**
     * @group 1
     */
    public function test_totalInterestSaved_someLoansExist()
    {
        $Loans = array();
        $Loan = $this->_Factory->buildLoan();
        $Loan->setInterest(5);
        $Loan->setAmount(100000);
        $Loan->setAmount(1500);
        $Loans[] = $Loan;

        $Loan2 = $this->_Factory->buildLoan();
        $Loan2->setInterest(8);
        $Loan2->setAmount(50000);
        $Loan2->setAmount(0);
        $Loans[] = $Loan2;

        $LoanRepository = $this->_getLoanRepoThatReturns($Loans);

        $InterestSaved = new InterestSaved($LoanRepository);
        $totalSaved = $InterestSaved->getTotalInterestSaved();
        $expectedInterestSaved = 77715;

        $this->assertEquals(
            $expectedInterestSaved,
            $totalSaved,
            'Was expecting that no interest would be saved if no Loans exist.'
        );
    }

    /**
     *@param array
     */
    private function _getLoanRepoThatReturns(array $returns)
    {
        $LoanRepository = $this->getMock('LoanRepository', array('getAllLoans'), array(), '', false);
        $LoanRepository->expects($this->once())
                       ->method('getAllLoans')
                       ->will($this->returnValue($returns));
        return $LoanRepository;
    }
}