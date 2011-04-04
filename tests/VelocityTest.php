<?php
class VelocityTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PayoffCalc
     */
    private $_PayoffCalc;

    /**
     * @var PaymentRepository
     */
    private $_PaymentRepo;

    public function setUp()
    {
        parent::setUp();
        $Factory = new Factory();
        $this->_PayoffCalc = $Factory->buildPayoffCalc();
        $this->_Velocity = new Velocity($this->_PayoffCalc);
    }

    public function test_construct_setsPayoffCalc()
    {
        $this->assertAttributeType(
            'PayoffCalc',
            '_PayoffCalc',
            $this->_Velocity,
            'Was expecting the PayoffCalc to be a PayoffCalc object'
        );
    }

    public function test_velocity_validDaily()
    {
        $paymentPerDay = 400;
        $expectedVelocity = $this->_getExpectedVelocity($paymentPerDay);

        $Velocity = $this->getVelocityPassingInPaymentPerDay($paymentPerDay);
        $returnedVelocity = $Velocity->getVelocity();

        $this->assertVelocitiesAreSame($expectedVelocity, $returnedVelocity);
    }

    public function test_velocity_otherValidDaily()
    {
        $paymentPerDay = 1;
        $expectedVelocity = $this->_getExpectedVelocity($paymentPerDay);

        $Velocity = $this->getVelocityPassingInPaymentPerDay($paymentPerDay);
        $returnedVelocity = $Velocity->getVelocity();

        $this->assertVelocitiesAreSame($expectedVelocity, $returnedVelocity);
    }

    public function test_velocity_anoutherValidDaily()
    {
        $paymentPerDay = 122;
        $expectedVelocity = $this->_getExpectedVelocity($paymentPerDay);

        $Velocity = $this->getVelocityPassingInPaymentPerDay($paymentPerDay);
        $returnedVelocity = $Velocity->getVelocity();

        $this->assertVelocitiesAreSame($expectedVelocity, $returnedVelocity);
    }

    /**
     * @param int
     * @param int
     */
    private function assertVelocitiesAreSame ($expectedVelocity, $returnedVelocity)
    {
        $this->assertEquals(
            $expectedVelocity,
            $returnedVelocity,
            'Was expecting that the returned Velocity: ' . $returnedVelocity
          . ' would equal the expected Velocity: ' . $expectedVelocity . '.');
    }

    /**
     * @param int
     * @return Velocity
     */
    private function getVelocityPassingInPaymentPerDay ($paymentPerDay)
    {
        $PayoffCalc = $this->_getPayoffCalcThatReturns($paymentPerDay);

        $Velocity = new Velocity($PayoffCalc);
        return $Velocity;
    }


    private function _getExpectedVelocity($paymentPerDay)
    {
        $velocity = 16.73445435 * $paymentPerDay;
        $velocity += 4;
        $velocity = (sqrt($velocity)) * 100;
        $velocity = ceil ($velocity);
        $velocity -= 400;
        return $velocity;
    }

    /**
     *@return PayoffCalc
     */
    private function _getPayoffCalcThatReturns ($paymentPerDay)
    {
        $PayoffCalc = $this->getMock(
            'PayoffCalc',
            array('getPaymentPerDay'),
            array(),
            '',
            false
        );
        $PayoffCalc->expects($this->any())
                   ->method('getPaymentPerDay')
                   ->will($this->returnValue($paymentPerDay));
        return $PayoffCalc;
    }
}
?>