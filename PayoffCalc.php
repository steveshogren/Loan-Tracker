<?php
class PayoffCalc
{
    /**
     * @var PaymentRepository
     */
    public $_PaymentRepository;

    /**
     * @var LoanRepository
     */
    public $_LoanRepository;

    public function __construct (
        PaymentRepository $PaymentRepository,
        LoanRepository $LoanRepository
    ) {
        $this->_PaymentRepository = $PaymentRepository;
        $this->_LoanRepository = $LoanRepository;
    }

    /**
     * @return string
     */
    public function getPayoffDate ()
    {
        $paymentPerDay = $this->getPaymentPerDay();
        if ($paymentPerDay) {
            $payoffDate = $this->_findPayoffDate($paymentPerDay);
            return $payoffDate;
        }
        return 0;
    }

    /**
     * @param paymentPerDay
     */
    private function _findPayoffDate ($paymentPerDay)
    {
        $paymentPerSecond = $paymentPerDay / 24 / 60 / 60;
        $secondsTillPayoff = $this->_LoanRepository->getLoanTotalAmount() / $paymentPerSecond;
        $payoffDate = date("Y-m-d", (time() + $secondsTillPayoff));
        return $payoffDate;
    }

    /**
     * @return int
     */
    public function getPaymentPerDay ()
    {
        $sumOfPayments = $this->_PaymentRepository->getSumOfPayments();

        $Payments = $this->_PaymentRepository->getAllPayments();
        if ($this->_getCountOfValidPayments($Payments) > 1 && $sumOfPayments > 0) {
            $paymentPerSecond = $this->_getPaymentPerSecond($Payments, $sumOfPayments);
            $paymentPerDay = $paymentPerSecond * 60 * 60 * 24;
            return $paymentPerDay;
        }
    }


    private function _getPaymentPerSecond ($Payments, $sumOfPayments)
    {
        $FirstPayment = array_shift($Payments);
        $oldestPayment = $FirstPayment;
        $newestPayment = $FirstPayment;
        foreach ($Payments as $Payment) {
            if ($Payment->getDate() < $oldestPayment->getDate()) {
                $oldestPayment = $Payment;
            }
            if ($Payment->getDate() > $newestPayment->getDate()) {
                $newestPayment = $Payment;
            }
        }
        $differenceFromFirstToLastPayment = $newestPayment->getDate() - $oldestPayment->getDate();
        $sumOfPayments = $sumOfPayments - $oldestPayment->getAmount();
        $paymentPerSecond = $sumOfPayments / $differenceFromFirstToLastPayment;
        return $paymentPerSecond;
    }

    private function _getCountOfValidPayments ($Payments)
    {
        $count = 0;
        foreach ($Payments as $Payment) {
            if ($Payment->getAmount() > 0) {
                $count ++;
            }
        }
        return $count;
    }

}
?>