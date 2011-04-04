<?php
class AuthenticationResultsTest extends PHPUnit_Framework_TestCase
{
    /**
     * General Failure
     */
    const FAILURE                        =  0;

    /**
     * Failure due to identity not being found.
     */
    const FAILURE_IDENTITY_NOT_FOUND     = -1;

    /**
     * Failure due to identity being ambiguous.
     */
    const FAILURE_IDENTITY_AMBIGUOUS     = -2;

    /**
     * Failure due to invalid credential being supplied.
     */
    const FAILURE_CREDENTIAL_INVALID     = -3;

    /**
     * Failure due to uncategorized reasons.
     */
    const FAILURE_UNCATEGORIZED          = -4;

    /**
     * Authentication success.
     */
    const SUCCESS                        =  1;

    /**
    * @var AuthenticationResults
    */
    private $_Results;

    public function setUp()
    {
        parent::setUp();
    }

    public function testPassTheAccountNotFoundResult()
    {
        $Result = new Zend_Auth_Result(self::FAILURE_IDENTITY_NOT_FOUND, '');
        $this->_Results = new AuthenticationResults($Result);

        $message = $this->_Results->getMessage();
        $this->assertEquals('Invalid Username', $message,
            'getMessage returned the wrong message'
        );
    }

    public function testPassTheAmbiguousResult()
    {
        $Result = new Zend_Auth_Result(self::FAILURE_IDENTITY_AMBIGUOUS, '');
        $this->_Results = new AuthenticationResults($Result);

        $message = $this->_Results->getMessage();
        $this->assertEquals('Invalid Username/Password combination', $message,
            'getMessage returned the wrong message'
        );
    }

    public function testPassTheInvalidCredentialResult()
    {
        $Result = new Zend_Auth_Result(self::FAILURE_CREDENTIAL_INVALID, '');
        $this->_Results = new AuthenticationResults($Result);

        $message = $this->_Results->getMessage();
        $this->assertEquals('Invalid password', $message,
            'getMessage returned the wrong message'
        );
    }

    public function testPassTheGeneralFailureResult()
    {
        $Result = new Zend_Auth_Result(self::FAILURE, '');
        $this->_Results = new AuthenticationResults($Result);

        $message = $this->_Results->getMessage();
        $this->assertEquals('Invalid username/password', $message,
            'getMessage returned the wrong message'
        );
    }

    public function testPassTheUncatFailureResult()
    {
        $Result = new Zend_Auth_Result(self::FAILURE_UNCATEGORIZED, '');
        $this->_Results = new AuthenticationResults($Result);

        $message = $this->_Results->getMessage();
        $this->assertEquals('Invalid username/password', $message,
            'getMessage returned the wrong message'
        );
    }

    public function testPassTheSucessyResult()
    {
        $Result = new Zend_Auth_Result(self::SUCCESS, '');
        $this->_Results = new AuthenticationResults($Result);

        $message = $this->_Results->getMessage();
        $this->assertEquals('Success!', $message,
            'getMessage returned the wrong message'
        );
    }


}