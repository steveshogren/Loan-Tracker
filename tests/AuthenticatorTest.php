<?php
class AuthenticatorTest extends PHPUnit_Framework_TestCase
{
    /**
    * @var Authenticator
    */
    private $_Authenticator;

    public function setUp()
    {
        parent::setUp();
        $this->_Authenticator = new Authenticator();
    }

    public function testNewUsernameCannontHaveUnseemlyCharacters()
    {
        $AccountData = new Account_Data();
        $AccountData->username = "hmmmmm%'";
        $AccountData->password = 'doesnotmatter';
        $AccountData->password2 = 'doesnotmatter';

        $message = $this->_Authenticator->createAccount($AccountData);
        $this->assertEquals('Your username must be only numbers and letters.', $message);
    }

    public function testNewPasswordCannontHaveUnseemlyCharacters()
    {
        $AccountData = new Account_Data();
        $AccountData->username = "hmmmmm";
        $AccountData->password = "doesnotmatter'";
        $AccountData->password2 = "doesnotmatter'";

        $message = $this->_Authenticator->createAccount($AccountData);
        $this->assertEquals('Your password must be only numbers and letters.', $message);
    }

    public function testUsernameMustBeLongEnough()
    {
        $AccountData = new Account_Data();
        $AccountData->username = "hmm";
        $AccountData->password = "doesnotmatter";
        $AccountData->password2 = "doesnotmatter";

        $message = $this->_Authenticator->createAccount($AccountData);
        $this->assertEquals('Your username must be larger then five letters.', $message);
    }

    public function testPasswordMustBeLongEnough()
    {
        $AccountData = new Account_Data();
        $AccountData->username = "hmmmmmm";
        $AccountData->password = "does";
        $AccountData->password2 = "does";

        $message = $this->_Authenticator->createAccount($AccountData);
        $this->assertEquals('Your password must be larger then five letters.', $message);
    }
}