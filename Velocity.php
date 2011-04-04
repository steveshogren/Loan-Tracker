<?php
class Velocity
{
    /**
     * @var PayoffCalc
     */
    private $_PayoffCalc;

    public function __construct (PayoffCalc $PayoffCalc)
    {
        $this->_PayoffCalc  = $PayoffCalc;
    }

    public function getVelocity()
    {
        $paymentPerDay = $this->_PayoffCalc->getPaymentPerDay();
        $velocity = $this->_getVelocity($paymentPerDay);
        return $velocity;
    }

    private function _getVelocity($paymentPerDay)
    {
        $velocity = 16.73445435 * $paymentPerDay;
        $velocity += 4;
        $velocity = (sqrt($velocity)) * 100;
        $velocity = ceil ($velocity);
        $velocity -= 400;
        return $velocity;
    }
}
?>