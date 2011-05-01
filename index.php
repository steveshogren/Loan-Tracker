<?php
error_reporting( E_ERROR );
Zend_Session::start();

set_include_path(
    get_include_path()
  . PATH_SEPARATOR
  . '/usr/share/php/libzend-framework-php/'
);

$GLOBALS['testing'] = true;

$DirectInput = new DirectInput();
$DirectInput->directInputs();

class DirectInput
{
    public function directInputs()
    {
        $Authenticator = new Authenticator();
        $createANewAcccount = false;

        if (! $this->_loggedIn()) {

            $Inputs = new Inputs();
            if (isset($_POST['showCreateAccount']) || isset($_GET['showCreateAccount'])){
                $createANewAcccount = true;
            } else {
                $AccountData = $Inputs->buildAccountData($_POST);
                if ($AccountData instanceof Account_Data) {

                    if ( $this->_accountNeedsToBeCreated($AccountData)) {
                        $message = $Authenticator->createAccount($AccountData);
                        if (! $Authenticator->accountWasCreated) {
                            $createANewAcccount = true;
                        }
                    } else {
                        $Result = $Authenticator->authenticateAccount($AccountData);
                        if (!$Result->isValid()) {
                            $Results = new AuthenticationResults($Result);
                            $message = $Results->getMessage();
                        }
                    }
                }
            }

            if (! $this->_loggedIn()) {
                include 'html/login2.php';
            } else {
                $this->_displayMainPage();
            }

        } else {
            $this->_displayMainPage();
        }
    }

    private function _displayMainPage ()
    {
        $Factory = new Factory();
        $LoanRepository = $Factory->buildLoanRepository();

        $MainPage = new MainPage($Factory, $LoanRepository);
        $MainPage->handleInput($_GET, $_POST);

        if (! $this->_loggedIn()) {
            include 'html/login2.php';
        } else {
            $PayoffCalc = $Factory->buildPayoffCalc();
            $paymentPerDay = $PayoffCalc->getPaymentPerDay();
            $paymentPerMonth = floor($paymentPerDay) * 30;
            $paymentPerWeek = floor($paymentPerDay) * 7;

            $Loans = $LoanRepository->getAllLoans();
            // add in an empty Loan class (that has an Id)
            // for the Add New Loan section
            $EmptyLoan = $Factory->buildLoan();
            $EmptyLoan->setName('New Loan Name');
            //Dont even get me started about how hill-william this is
            $EmptyLoan->setAmount('Original Principal');
            $EmptyLoan->setAmount('Current Principal');
            $EmptyLoan->setInterest('Interest');
            $Loans[] = $EmptyLoan;
            $LoanAccounts = $LoanRepository->getAllAccounts();
            $LoanAccounts[] = $Factory->buildLoan();

            include 'html/template2.html';
        }
    }


    private function _accountNeedsToBeCreated($AccountData)
    {
        $accountNeedsToBeCreated = (isset($AccountData->password2));
        return $accountNeedsToBeCreated;
    }

    /**
     * @param auth
     */
    private function _loggedIn()
    {
        $loggedIn = false;
        $Namespace = new Zend_Session_Namespace('Zend_Auth');
        foreach ($Namespace as $index => $value) {
            $loggedIn = ($value->user_id);
        }
        return $loggedIn;
    }
}

function __autoload($class_name) {
    include_once $class_name . '.php';

    $fullclasspath="";

    // get separated directories
    $pathchunks=explode("_",$class_name);

    //re-build path without last item
    for($i = 0; $i < (count($pathchunks)-1); $i++) {
        $fullclasspath .= $pathchunks[$i] . '/';
        $class_name = $pathchunks[$i+1];
    }

    include_once $fullclasspath . $class_name . '.php';
}

?>
