<?php
class LoanRepository {
    private $_Dbh;
    private $_userId;

    function __construct($Dbh) {
        $this->_Dbh = $Dbh;

        $Namespace = new Zend_Session_Namespace('Zend_Auth');
        foreach ($Namespace as $index => $value) {
            $this->_userId = $value->user_id;
//            echo $this->_userId;
        }
    }

    public function getAllLoans() {
        $sql_sort = $this->_getSortOrder();

        $sql = "
            SELECT
                *
            FROM
                loan
            WHERE
                savings = 0
                AND user_id = ?
            ORDER BY " . $sql_sort;
        $stmt = $this->_Dbh->prepare ($sql);
        $Loans = array ();
        if ($stmt->execute(array ($this->_userId))) {
            while ($row = $stmt->fetch()) {
                $Loans [] = new Loan ($this->_Dbh, $row ['loan_id'] );
            }
            return $Loans;
        }
    }

    public function getAllAccounts()
    {
        $sql_sort = $this->_getSortOrder();

        $sql = "SELECT * FROM loan WHERE savings = 1 AND user_id = ?";
        $stmt = $this->_Dbh->prepare ($sql);
        $Loans = array ();
        if ($stmt->execute (array ($this->_userId))) {
            while ($row = $stmt->fetch()) {
                $Loans [] = new Loan ($this->_Dbh, $row ['loan_id'] );
            }
            return $Loans;
        }
    }

    private function _getSortOrder ()
    {
        $Factory = new Factory();
        $User = $Factory->buildUser();
        $SortOrder = $User->getSortOrder();
        if ($SortOrder instanceof SortOrder_Snowball) {
            $sql_sort = " amount ASC, interest DESC";
        } else {
            $sql_sort = " interest DESC, amount DESC";
        }
        return $sql_sort;
    }


    public function getLoanTotalAmount() {
        $sql = "SELECT sum(amount) total FROM loan where user_id = ?";
        $stmt = $this->_Dbh->prepare ( $sql );
        if ($stmt->execute (array ($this->_userId))) {
            $row = $stmt->fetch ();
            return $row ['total'];
        }
    }

    public function deleteLoan($loanId) {
        $sql = "DELETE FROM loan WHERE loan_id = ? AND user_id = ?";
        $stmt = $this->_Dbh->prepare ( $sql );
        $stmt->execute ( array ($loanId, $this->_userId) );
    }

    public function createNewLoan(Loan $Loan) {
        $sql = "
            INSERT INTO
                loan (description, interest, amount, max_amount, savings, user_id)
                VALUES (?,?,?,?,?,?)";
        $stmt = $this->_Dbh->prepare ( $sql );
        $stmt->execute (
            array (
                $Loan->getName (),
                $Loan->getInterest (),
                $Loan->getAmount (),
                $Loan->getMaxAmount (),
                $Loan->isSavings(),
                $this->_userId
            )
         );
    }

}

?>
