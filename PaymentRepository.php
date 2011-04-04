<?php
class PaymentRepository
{
    /**
     * @var PDO
     */
    private $_Dbh;

    /**
     * @var Factory
     */
    private $_Factory;

    private $_userId;

    function __construct ($Dbh, $Factory)
    {
        $this->_Dbh = $Dbh;
        $this->_Factory = $Factory;
        $Namespace = new Zend_Session_Namespace('Zend_Auth');
        foreach ($Namespace as $index => $value) {
            $this->_userId = $value->user_id;
        }
    }

    /**
     * @return array ( int => Payment, ...)
     */
    public function getAllPayments ()
    {
        $sql = "SELECT payment_id FROM payment where soft_delete = 0 AND user_id = ?";
        $stmt = $this->_Dbh->prepare($sql);
        $Payments = array();
        if ($stmt->execute(array($this->_userId))) {
            while ($row = $stmt->fetch()) {
                $Payment = $this->_Factory->buildPayment();
                $Payment->newFromId($row['payment_id']);
                $Payments[] = $Payment;
            }
            return $Payments;
        }
    }

    public function resetPayments()
    {
        $sql = "
            UPDATE
                payment
            SET
                soft_delete = 1
            WHERE
                user_id = ?
        ";
        $stmt = $this->_Dbh->prepare($sql);
        $stmt->execute(array($this->_userId));
    }

    /**
     * @return int
     */
    public function getSumOfPayments()
    {
        $Payments = $this->getAllPayments();
        if (count($Payments) > 0) {
            $sumOfPayments = $this->_getSumOfPayments($Payments);
            return $sumOfPayments;
        }

        return false;
    }

    /**
     * @param Payment[]
     * @return int
     */
    private function _getSumOfPayments ($Payments)
    {
        foreach ($Payments as $Payment) {
            $sumOfPayments += $Payment->getAmount();
        }
        return $sumOfPayments;
    }
}
?>