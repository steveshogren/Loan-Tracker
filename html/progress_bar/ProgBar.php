<?php /*
   +------------------------------------------------------------------+
   | Green-Beast.com                                                  |
   | MIX: Donations Gauge                                             |
   | PHP Hypertext Preprocessor                                       |
   | Copyright May 2006                                               |
   | Use with attribution by visible link please!                     |
   | Attribute to: <a href="http://green-beast.com/">Mike Cherim</a>  |
   +------------------------------------------------------------------+
*/ ?>

<?php
/*
   This file's contents can be simply placed where you want the gauge to display
   Or you can break it out into separate files. your call. If you have a secure
   admin, you can create a form to submit the variables similar to the demo
*/

#####################################################
// Begin editable variables

   $current_amt=   $totalLoanAmount;     // How much have you recieved (this moves the marker)
   $goal_level=    $totalLoanMaxAmount;     // How much are you looking for
   $contact_link=  "#";      // Contact URL or email "mailto:acct@domain.com";
   $currency=      "&#36;";  // Set currency used (See these examples or find your own)
                             // &#36;=Dollars, &#163;=Pounds, &#165;=Yen, &#8364;=Euros

    $Velocity = $Factory->buildVelocity();
    $velocityText = $Velocity->getVelocity();
// End editable variables
######################################################
echo( "<!--donation gauge begin-->" );

// First step is to do a little math (our current amount divided by our goal level x 100 to get a % or current level)
   @$current_level = ( ($current_amt / $goal_level) * 100 );

// Here we need to round that percentage to display it before converting it to pixels (margin-bottom)
   $exact_level = round( $current_level );

/*
   Now we keep the marker down one close to the goal for exact level (% display)
   and to keep the goal from showing it's prematurely met when it's not.
   We also fix the bottom so if the level is 0 or close to it it displays as 1 so we see visible progress
*/
if ( $current_level < 100 && $exact_level == 100 ) {
   $exact_level = 99;
} else if ( $current_level > 0 && $exact_level == 0 ) {
   $exact_level = 1;
}

/*
   Now if we're in that 95-99.99% zone, we need to force the marker down (force it down a bit) so as to
   not obscure the goal text with its background
*/
if ( $current_level > 95 && $current_level < 100 ) {
   $current_level = 95;
} else {
   $current_level = round( $current_level );
}

// Here we keep the bottom end stable for negatives and whatever
    $divisible = ( $current_level % 5 );
if ( $divisible != 0 ) {
    $current_level -= $divisible;
}
// And then prevent overflow when the goal is exceeded
if ( $current_level > 100 ) {
   $current_level = 100;
}

// Now we covert that percentage into bottom margin (300px high so 1% = 3px)
   $margin_level = round( $current_level * 3 );

/*
   It's okay to edit these if you simply MUST do it, but due to absolute positioning,
   do try to keep text lengths as is for guaranteed display
*/
$percentagePaid = (100-$exact_level);
$donate_head=   "Payoff Percentage: " . $percentagePaid . '%'; // The Box Heading (could be 'Donate Today')
$goal_line=     "We need";           // Second line (could be 'Our Goal')
$goal_got=      "None Paid";          // Got the goal text (could be 'Made it')
$goal_thanks=   "...get to work!";           // Thanks (need we say more? no room)
$goal_blind=    "Thanks to you we&#8217;re at"; // This is the non-visual line
                                    // This last line is the no fundraiser text (the link is below, though)
$goal_none = "Hello. To add a loan, just enter the values to your left, "
    . "and click 'Create'.";

// Here we echo the box it all comes in and we pass some variables.
echo( '<div id="cdg-shell">
  <h2 id="cdg_h2">'.$donate_head.'</h2>' );

// Goal status marker plus or the Goal met text... thanks!
$goal_met2 = "or&nbsp;".$percentagePaid."%&nbsp;<span class=\"cdg_arw\">&rarr;</span>";
if ( $margin_level == 300 ) {
    $goal_met = "<strong>".$goal_got."";
    $goal_met2 = "".$goal_thanks."</strong>";

// Else nothing, the margin level stays as is (typically 0)
} else {
    $margin_level = "$margin_level";
}

// And here is the guage body if we have a goal to meet
   $paidOff = $goal_level - $current_amt;
   $gauge_body = "
<p id=\"cdg_goal\">".$goal_line.": ".$currency."".number_format($current_amt)."</p>
  <div id=\"cdg\">
    <p title=\"So far we&#8217;ve paid off "
    .$currency."".number_format($paidOff)." or "
    .$percentagePaid."% of the total of ".$currency.""
    .$goal_level." - Mr. Roboto\" style=\"margin-bottom:"
    .$margin_level."px;\" id=\"cdg_p\"><span class=\"blind\">"
    .$goal_blind."</span> ".@$goal_met."  ".$currency.""
    .number_format($paidOff)." ".$goal_met2."</p>
    <div id=\"cdg_m\" style=\"height:".$margin_level."px\"></div>
       <img src=\"html/progress_bar/cdg_tmom.gif\" width=\"60\" height=\"300\" alt=\"\" />
  </div>";

// Here it is displayed, the gauge body, or none, all depends on if we have a goal
if ( $goal_level != 0 ) {
    echo ''.$gauge_body.'';
} else {
    echo '<div id="cdg-noshell">';
    echo '<p id="cdg_no">' . $goal_none . '</p>';
    echo '</div>';
}
    echo "</div>
<!--donation gauge output end-->";
?>








