<?php
class Factory
{
    private $_Dbh;
    public function __construct ()
    {
        if (! $this->_Dbh instanceof PDO) {
            if (isset($GLOBALS['testing']) && $GLOBALS['testing'] == true) {
                $db = 'loan_test';
                $this->_Dbh = new PDO("mysql:host=localhost;dbname={$db}", 'root', 'mysql');
            } else {
                $db = 'loansite';
                $this->_Dbh = new PDO(
                    "mysql:host=".DatabaseString::getDatabaseHost().";dbname={$db}",
                    DatabaseString::getDatabaseUsername(),
                    DatabaseString::getDatabasePassword()
                );
            }

        }

    }

    public function getDbh()
    {
        return $this->_Dbh;
    }

    /**
     * @return Payment
     */
    public function buildPayment ()
    {
        return new Payment($this->_Dbh);
    }

    /**
     * @return PaymentRepository
     */
    public function buildPaymentRepository ()
    {
        return new PaymentRepository($this->_Dbh, $this);
    }

    /**
     * @param $loanId
     * @return Loan
     */
    public function buildLoan($loanId = 0)
    {
        return new Loan($this->_Dbh, $loanId);
    }

    /**
     * @return LoanRepository
     */
    public function buildLoanRepository ()
    {
        return new LoanRepository($this->_Dbh);
    }

    /**
     * @return PayoffCalc
     */
    public function buildPayoffCalc ()
    {
        return new PayoffCalc($this->buildPaymentRepository(), $this->buildLoanRepository());
    }

    /**
     * @return Velocity
     */
    public function buildVelocity ()
    {
        return new Velocity($this->buildPayoffCalc());
    }

    /**
     * @return User
     */
    public function buildUser()
    {
        return new User($this->_Dbh);
    }

    public function buildInterestSaved()
    {
        return new InterestSaved($this->buildLoanRepository());
    }
}
?>