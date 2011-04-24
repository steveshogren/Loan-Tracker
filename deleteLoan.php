<?php
error_reporting( E_ERROR );
Zend_Session::start();

set_include_path(
    get_include_path()
  . PATH_SEPARATOR
  . '/usr/share/php/libzend-framework-php/'
);

$GLOBALS['testing'] = true;

if (isset($_REQUEST["loanId"])) {

    $post['loanId'] = $_REQUEST["loanId"];
    $post['delete'] = "delete";
    $Factory = new Factory();
    $MainPage = new MainPage($Factory);

    // Save the deleted state
    $MainPage->_handleDeleteLoanButton($post);

    $LoanRepository = $Factory->buildLoanRepository();
    $totalLoanAmount = 0;
    $totalLoanMaxAmount = 0;

    foreach ($LoanRepository->getAllLoans() as $Loan) {
        $totalLoanAmount += $Loan->getAmount();
        $totalLoanMaxAmount += $Loan->getMaxAmount();
    }


    $marginPx = calculateProgBar($totalLoanAmount ,$totalLoanMaxAmount);

    $current_level = (($totalLoanAmount / $totalLoanMaxAmount) * 100);
    $exact_level = 100-round($current_level);

    // Payment averages
    $PayoffCalc = $Factory->buildPayoffCalc();
    $paymentPerDay = $PayoffCalc->getPaymentPerDay();
    $paymentPerMonth = floor($paymentPerDay) * 30;
    $paymentPerWeek = floor($paymentPerDay) * 7;

    // Payoff Date
    $payoffDate = ($PayoffCalc->getPayoffDate()) ? $PayoffCalc->getPayoffDate() : "";

    // Interest Saved
    $InterestSaved = $Factory->buildInterestSaved();
    $totalInterestSaved = number_format($InterestSaved->getTotalInterestSaved());

    $xml = "
        <averages>
            <totalLoanAmount>$totalLoanAmount</totalLoanAmount>
            <totalLoanMaxAmount>$totalLoanMaxAmount</totalLoanMaxAmount>
            <marginLevel>$marginPx</marginLevel>
            <percentPaid>$exact_level</percentPaid>
            <averagePerWeek>$paymentPerWeek</averagePerWeek>
            <averagePerMonth>$paymentPerMonth</averagePerMonth>
            <payoffDate>$payoffDate</payoffDate>
            <interestSaved>$totalInterestSaved</interestSaved>
        </averages>
    ";
    header('Content-type: text/xml');
    echo $xml;
}

function calculateProgBar($current_amt, $goal_level)
{
    // First step is to do a little math (our current amount divided by our goal level x 100 to get a % or current level)
    @$current_level = (($current_amt / $goal_level) * 100);

    // Here we need to round that percentage to display it before converting it to pixels (margin-bottom)
    $exact_level = round($current_level);

    /*
       Now we keep the marker down one close to the goal for exact level (% display)
       and to keep the goal from showing it's prematurely met when it's not.
       We also fix the bottom so if the level is 0 or close to it it displays as 1 so we see visible progress
    */
    if ($current_level < 100 && $exact_level == 100) {
        $exact_level = 99;
    } else if ($current_level > 0 && $exact_level == 0) {
        $exact_level = 1;
    }

    /*
       Now if we're in that 95-99.99% zone, we need to force the marker down (force it down a bit) so as to
       not obscure the goal text with its background
    */
    if ($current_level > 95 && $current_level < 100) {
        $current_level = 95;
    } else {
        $current_level = round($current_level);
    }

    // Here we keep the bottom end stable for negatives and whatever
    $divisible = ($current_level % 5);
    if ($divisible != 0) {
        $current_level -= $divisible;
    }
    // And then prevent overflow when the goal is exceeded
    if ($current_level > 100) {
        $current_level = 100;
    }

    // Now we covert that percentage into bottom margin (300px high so 1% = 3px)
    $margin_level = round($current_level * 3);
    return $margin_level;
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