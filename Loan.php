<?php
class Loan
{
    private $_Dbh;
    private $_loanId;
    private $_name;
    private $_interest;
    private $_amount;
    private $_maxAmount;
    private $_savings;
    private $_userId;

    function __construct ($Dbh, $loanId = 0)
    {
        $this->_Dbh = $Dbh;
        if ($loanId) {
            $Loan = $this->newFromId($loanId);
        }
        $Namespace = new Zend_Session_Namespace('Zend_Auth');
        foreach ($Namespace as $index => $value) {
            $this->_userId = $value->user_id;
        }
        $this->_savings = 0;
    }

    public function newFromId ($loanId)
    {
        $sql = "
            SELECT
                *
            FROM
                loan
            WHERE
                loan_id = ?
            ";
        $stmt = $this->_Dbh->prepare($sql);
        if ($stmt->execute(array($loanId))) {
            while ($row = $stmt->fetch()) {
                $this->_interest = $row['interest'];
                $this->_name = $row['description'];
                $this->_amount = $row['amount'];
                $this->_maxAmount = $row['max_amount'];
                $this->_savings = $row['savings'];
                $this->_loanId = $row['loan_id'];
            }
        }
    }

    public function getPercentPaid ()
    {
        return ($this->_amount * 100) / $this->_maxAmount;
    }

    //setters
    public function setInterest ($interest)
    {
        $this->_interest = $interest;
        $this->_save();
    }

    public function setName ($name)
    {
        $this->_name = $name;
        $this->_save();
    }

    public function setAmount ($amount)
    {
        $this->_amount = $amount;
        if ($this->_amount > $this->_maxAmount) {
            $this->_maxAmount = $this->_amount;
        }
        $this->_save();
    }

    public function setMaxAmount($maxAmount)
    {
        if ($this->isSavings()) {
            $this->_maxAmount = $maxAmount;
        }
    }

    public function setSavings()
    {
        $this->_savings = true;
    }

    // getters
    public function getInterest ()
    {
        return $this->_interest;
    }

    public function getName ()
    {
        return $this->_name;
    }

    public function getAmount ()
    {
        return $this->_amount;
    }

    public function getMaxAmount ()
    {
        return $this->_maxAmount;
    }

    public function getId ()
    {
        return $this->_loanId;
    }

    public function isSavings()
    {
        return $this->_savings;
    }

    private function _save()
    {
        if ($this->_loanId) {
            $this->_updateLoanInDatabase();
        }
    }

    private function _updateLoanInDatabase()
    {
        $sql = "
            UPDATE
                loan
            SET
                description = ?,
                interest = ?,
                amount = ?,
                max_amount = ?,
                savings = ?
            WHERE
                loan_id = ?
                AND user_id = ?
        ";
        $stmt = $this->_Dbh->prepare($sql);
        $stmt->execute(
            array(
                $this->_name ,
                $this->_interest ,
                $this->_amount ,
                $this->_maxAmount ,
                $this->_savings,
                $this->_loanId,
                $this->_userId
            )
        );
    }

}
?>