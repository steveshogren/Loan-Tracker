<?php
class InterestSaved
{
    /**
     * @var LoanRepository
     */
    private $_LoanRepository;

    public function __construct(LoanRepository $LoanRepo)
    {
        $this->_LoanRepository = $LoanRepo;
    }

    /**
     * @return int
     */
    public function getTotalInterestSaved()
    {
        $interestSaved = 0;
        $Loans = $this->_LoanRepository->getAllLoans();
        foreach ($Loans as $Loan) {
            $saved = $this->getInterestSaved($Loan);
            $interestSaved += $saved;
        }
        return $interestSaved;
    }

    /**
     * @param Loan $Loan
     * @return int
     */
    public function getInterestSaved(Loan $Loan)
    {
        $currentPrincipal = $Loan->getAmount();
        $originalPrincipal = $Loan->getMaxAmount();
        $interest = $this->_getInterest($Loan);
        $numberOfPayments = $this->_getNumberOfPayments();


        $currentInterestToBeLost = $this->_getInterestToBeLost(
            $currentPrincipal,
            $interest,
            $numberOfPayments
        );

        $originalInterestToBeLost = $this->_getInterestToBeLost(
            $originalPrincipal,
            $interest,
            $numberOfPayments
        );

        $interestSaved = floor($originalInterestToBeLost - $currentInterestToBeLost);
        return $interestSaved;
    }

    /**
     * @param Loan
     * @return int
     */
    private function _getInterest($Loan)
    {
        $loanInterest = $Loan->getInterest();
        if ($loanInterest == 0) {
            $loanInterest = .001;
        }
        $monthsInAYear = 12;
        $interestAsAFraction = ($loanInterest/100);
        $interest = $interestAsAFraction/$monthsInAYear;
        return $interest;
    }

    /**
     *@return int
     */
    private function _getNumberOfPayments()
    {
        $paymentsInATenYearLoan = 180;
        $numberOfPayments = $paymentsInATenYearLoan;
        return $numberOfPayments;
    }

    /**
     * @param int $principal
     * @param int $interest
     * @param int $numberOfPayments
     * @return int
     */
    private function _getInterestToBeLost($principal, $interest, $numberOfPayments)
    {
        $const = pow((1 + $interest),$numberOfPayments);

        $monthlyPayments = ($principal*($interest*$const)/($const -1));
        $totalToPay = $monthlyPayments * $numberOfPayments;
        $interestAccrued = $totalToPay - $principal;
        return $interestAccrued;
    }
}