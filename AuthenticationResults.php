<?php
class AuthenticationResults
{
    /**
     * @var Zend_Auth_Result
     */
    private $_Result;

    /**
     * @param Zend_Auth_Result $Result
     */
    public function __construct(Zend_Auth_Result $Result)
    {
        $this->_Result = $Result;
    }

    public function getMessage()
    {
        $code = $this->_Result->getCode();
        switch ($code) {
            case 1:
                return 'Success!';
                break;
            case 0:
                return 'Invalid username/password';
                break;
            case -4:
                return 'Invalid username/password';
                break;
            case -1:
                return 'Invalid Username';
                break;
            case -2:
                return 'Invalid Username/Password combination';
                break;
            case -3:
                return 'Invalid password';
                break;
        }
    }

}