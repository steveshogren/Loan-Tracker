<?php

class PaymentRepositoryTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        parent::tearDown();
        $this->_removeTestLoans();

    }

    public function test_getSumOfPayments()
    {
        $amount = 500;
        $PaymentRepo = $this->_getPaymentRepoThatReturnsSomePaymentsTotaling($amount);
        $returnedSum = $PaymentRepo->getSumOfPayments();

        $this->assertEquals(
            $amount,
            $returnedSum,
            'Was expecting the returned sum to match what was passed.'
        );
    }

    public function test_resetPayments_paymentsInStorage()
    {
        $this->_addSomePaymentsToDb();
        $sql = "
            UPDATE
                payment
            SET
                soft_delete = 1
        ";

        $PaymentRepo = $this->getPaymentRepo();
        $sumBefore = $PaymentRepo->getSumOfPayments();

        $this->assertTrue($sumBefore > 0);

        $PaymentRepo->resetPayments();

        $sumAfter = $PaymentRepo->getSumOfPayments();
        $this->assertTrue(
            $sumAfter == 0,
            'PaymentRepo->getSumOfPayments is supposed to return nothing '
          . 'right after the reset.'
        );
    }

    private function _addSomePaymentsToDb()
    {
        $sql = "
            INSERT INTO
                loan (loan_id, description, amount, interest)
            VALUES
                (?, 'test', 1, 1)
        ";

        $Factory = new Factory();
        $Payment = $Factory->buildPayment();

        $Dbh = $Factory->getDbh();
        $stmt = $Dbh->prepare($sql);

        for ($loanId = 0; $loanId < 5; $loanId ++) {
            $stmt->execute(array($loanId));
            $Loan = $Factory->buildLoan($loanId);
            $Payment->addPaymentToLoan($loanId, $Loan);
        }
    }

    /**
     * @return PaymentRepository
     */
    private function getPaymentRepo()
    {
        $Factory = new Factory();
        $PaymentRepo = $Factory->buildPaymentRepository();
        return $PaymentRepo;
    }

    /**
     * @param int $amount
     * @return PaymentRepository
     */
    private function _getPaymentRepoThatReturnsSomePaymentsTotaling ($amount)
    {
        $Payments = $this->_getSomePaymentsTotaling($amount);
        $PaymentRepo = $this->getMock('PaymentRepository', array('getAllPayments'), array(), '', false);
        $PaymentRepo->expects($this->any())
                    ->method('getAllPayments')
                    ->will($this->returnValue($Payments));
        return $PaymentRepo;
    }

    /**
     * @param int $amount
     * @param array ( int => Payment, ...)
     */
    private function _getSomePaymentsTotaling($amount)
    {
        $Payments = array();
        $total = 0;
        while ($total < $amount) {
            $Payments[] = $this->_getPayment(100);
            $total += 100;
        }
        return $Payments;
    }

    /**
     * @param int $amount
     * @param Payment
     */
    private function _getPayment($amount)
    {
        $Payment = $this->getMock('Payment', array('getAmount'), array(), '', false);
        $Payment->expects($this->any())
                ->method('getAmount')
                ->will($this->returnValue($amount));
        return $Payment;
    }

    /**
     * @return void
     */
    private function _removeTestLoans ()
    {
        $sql = "
            DELETE FROM
                loan
            WHERE
                description LIKE 'test'
                AND amount =  1
                AND interest = 1
        ";
        $Factory = new Factory();
        $Dbh = $Factory->getDbh();
        $stmt = $Dbh->prepare($sql);
        $stmt->execute(array());
    }

}

?>