<?php
class MainPage
{
    /**
     * @var Factory
     */
    private $_Factory;

    /**
     * @var LoanRepository
     */
    private $_LoanRepo;

    public function __construct (Factory $Factory)
    {
        $this->_Factory = $Factory;
        $this->_LoanRepo = $this->_Factory->buildLoanRepository();
    }

    public function handleInput ($get, $post)
    {
        $this->_handleSortOrder($post);

        $this->_handleResetPaymentsButton($get);

        $this->_handleDeleteLoanButton($post);

        $this->_handleUpdateLoan($post);

        $this->_handleLogout($post, $get);
    }

    private function _handleLogout($post, $get)
    {
        if ($post['logout'] || $get['logout']) {
            Zend_Auth::getInstance()->clearIdentity();
        }
    }

    private function _handleSortOrder($post)
    {
        if ($post['sortSnowball']) {
            $SortOrder = new SortOrder_Snowball();
        } elseif ($post['sortDefault']) {
            $SortOrder = new SortOrder_Default();
        }

        if ($SortOrder instanceof SortOrder) {
            $User = $this->_Factory->buildUser();
            $User->setSortOrderTo($SortOrder);
        }
    }

    private function _handleUpdateLoan($post)
    {
        // update button pressed
        if (isset($post['Submit'])) {
            $cleanLoanId = preg_replace('/[^0-9]/', '', $post['loanId']);
            $isNewLoan = (! isset($cleanLoanId) || ! $cleanLoanId > 0);
            $cleanMaxAmount = $this->cleanDecimalNumber($post['max_amount']);
            $cleanAmount = $this->cleanDecimalNumber($post['amount']);
            $cleanInterest = $this->cleanDecimalNumber($post['interest']);
            $cleanName = preg_replace('/[^a-zA-Z0-9 \-\_]/', '', $post['name']);

            $Loan = $this->_getLoanToUpdate($cleanLoanId);
            if ($Loan->isSavings() || isset($post['savings'])) {
                $Loan->setSavings();
                $amount = $cleanMaxAmount - $cleanAmount;
                $Loan->setMaxAmount($cleanMaxAmount);
            } else {
                if ($isNewLoan) {
                    $Loan->setAmount($cleanMaxAmount);
                    $Loan->setAmount($amount);
                }
                $amount = $cleanAmount;
            }
            $this->_addPayment($amount, $Loan);
            $Loan->setAmount($amount);
            $Loan->setInterest($cleanInterest);
            $Loan->setName($cleanName);
            // if Loan is brand new, it needs an Id
            if ($isNewLoan) {
                $this->_LoanRepo->createNewLoan($Loan);
            }
        }
    }
    /**
     * @param post
     */
    private function cleanDecimalNumber ($number)
    {
        $cleanNumber = preg_replace('/[^0-9\.]/', '', $number);
        return $cleanNumber;
    }


    private function _handleDeleteLoanButton($post)
    {
     // Delete Loan button pressed
        if (isset($post['delete']) && $post['delete'] == 'delete') {
            $cleanLoanId = preg_replace('/[^0-9]/', '', $post['loanId']);
            $this->_LoanRepo->deleteLoan($cleanLoanId);
        }
    }

    private function _handleResetPaymentsButton($get)
    {
        //reset button is pressed
        if ($this->_resetButtonPressed($get)) {
            $PaymentRepo = $this->_Factory->buildPaymentRepository();
            $PaymentRepo->resetPayments();
        }
    }

    private function _resetButtonPressed($get)
    {
        return (isset($get['reset']));
    }

    private function _getLoanToUpdate ($loanId)
    {
        if (isset($loanId) && $loanId > 0) {
            $Loan = $this->_Factory->buildLoan($loanId);
        } else {
            $Loan = $this->_Factory->buildLoan();
        }
        return $Loan;
    }

    private function _addPayment ($amount, Loan $Loan)
    {
        if ($this->_paymentShouldBeRecorded($amount, $Loan)) {
            $Payment = $this->_Factory->buildPayment();
            $paymentAmount = $Loan->getAmount() - $amount;
            $Payment->addPaymentToLoan($paymentAmount, $Loan);
        }
    }

    private function _paymentShouldBeRecorded ($amount, Loan $Loan)
    {
        $loanAmount = $Loan->getAmount();
        $loanAmountIsDifferent = $Loan->getId() > 0 && $amount != $loanAmount;
        return $loanAmountIsDifferent;
    }
}
?>
