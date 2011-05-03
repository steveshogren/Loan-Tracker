<?php
class Authenticator
{

    public $accountWasCreated;

    private $_dbAdapter;
    private $_AuthAdapter;
    private $_staticSalt;

    private $_Auth;

    public function __construct()
    {
        if (isset($GLOBALS['testing']) && $GLOBALS['testing'] == true) {
            $conArray = array(
                'host'     => 'localhost',
                'username' => 'root',
                'password' => 'mysql',
                'dbname'   => $db
            );
        } else {
            $conArray = array(
                'host'     => DatabaseString::getDatabaseHost(),
                'username' => DatabaseString::getDatabaseUsername(),
                'password' => DatabaseString::getDatabasePassword(),
                'dbname'   => 'loansite'
            );
        }

        $this->_staticSalt = '';
        $this->_dbAdapter = new Zend_Db_Adapter_Pdo_Mysql($conArray);

        $this->_AuthAdapter = new Zend_Auth_Adapter_DbTable(
            $this->_dbAdapter,
            'users',
            'username',
            'password',
            "MD5(CONCAT('"
            . $this->_staticSalt
            . "', ?, password_salt))"
        );

        $this->_Auth = Zend_Auth::getInstance();

        $this->accountWasCreated = false;
    }

    public function createAccount(Account_Data $AccountData)
    {
        $userName = $AccountData->username;
        $password = $AccountData->password;
        $password2 = $AccountData->password2;

        if ($password !== $password2) {
            $message = 'Your passwords did not match. Please try again.';
            return $message;
        }

        if (strlen($password) < 6) {
            return 'Your password must be larger then five letters.';
        }
        $passwordWithoutSpecialChars = preg_replace('/[^a-zA-Z0-9]/', '', $password);
        if ($passwordWithoutSpecialChars != $password) {
            return 'Your password must be only numbers and letters.';
        }

        if (strlen($userName) < 6) {
            return 'Your username must be larger then five letters.';
        }
        $usernameWithoutSpecialChars = preg_replace('/[^a-zA-Z0-9]/', '', $userName);
        if ($usernameWithoutSpecialChars != $userName) {
            return 'Your username must be only numbers and letters.';
        }
        $dynamicSalt = $this->_createDynamicSalt();

        $passwordHash = md5($this->_staticSalt . $password . $dynamicSalt);
        $sqlInsert = "INSERT INTO users (username, password, password_salt, real_name) "
                   . "VALUES ('{$userName}', '{$passwordHash}', '{$dynamicSalt}', 'My Real Name')";

        // Insert the data
        $this->_dbAdapter->query($sqlInsert);

        $message = 'Account created successfully. Please log in.';
        $this->accountWasCreated = true;

        return $message;
    }

    private function _createDynamicSalt()
    {
        $dynamicSalt = '';
        for ($i = 0; $i < 50; $i++) {
            $rand = rand(33, 126);
            if ($rand == 34 || $rand == 39) {
                $rand = 35;
            }
            $dynamicSalt .= chr($rand);
        }
        $dynamicSalt = MD5($dynamicSalt);
//        echo $dynamicSalt;
        return $dynamicSalt;
    }


    /**
     * Performs an authentication attempt
     *
     * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
     * @return Zend_Auth_Result
     */
    public function authenticateAccount(Account_Data $AccountData)
    {
        $this->_AuthAdapter
            ->setIdentity($AccountData->username)
            ->setCredential($AccountData->password)
        ;
        try {
            $Result = $this->_Auth->authenticate($this->_AuthAdapter);
            $storage = $this->_Auth->getStorage();
                $storage->write($this->_AuthAdapter->getResultRowObject(
                    array(
                        'username',
                        'real_name',
                        'user_id'
                    )
                )
            );

        } catch(Exception $e) {}
        return $Result;
    }

}
