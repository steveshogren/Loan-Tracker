<?php
class Payment
{
    private $_Dbh;
    private $_paymentId;
    private $_datePaid;
    private $_amount;
    private $_loanId;
    private $_userId;

    public function __construct ($Dbh)
    {
        $this->_Dbh = $Dbh;
        $Namespace = new Zend_Session_Namespace('Zend_Auth');
        foreach ($Namespace as $index => $value) {
            $this->_userId = $value->user_id;
        }
    }

    public function newFromId ($paymentId)
    {
        $stmt = $this->_Dbh->prepare("
            SELECT *
            FROM payment
            WHERE payment_id = ? AND user_id = ?"
        );
        if ($stmt->execute(array($paymentId, $this->_userId))) {
            while ($row = $stmt->fetch()) {
                $this->_amount = $row['amount'];
                $this->_loanId = $row['loan_id'];
                $this->_datePaid = strtotime($row['date_paid']);
                $this->_paymentId = $row['payment_id'];
            }
        }
    }

    public function getAmount ()
    {
        return $this->_amount;
    }

    public function getDate ()
    {
        return $this->_datePaid;
    }

    public function addPaymentToLoan ($amount, $Loan)
    {
        $sql = "INSERT INTO payment (amount, loan_id, user_id) VALUES (?, ?, ?)";
        $stmt = $this->_Dbh->prepare($sql);
        $stmt->execute(array($amount , $Loan->getId(), $this->_userId));
    }
}
?>