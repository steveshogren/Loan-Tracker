<?php
class MonthlyPaymentRepository 
{
  private $_Dbh;

  public function __construct($Dbh)
  {
    $this->_Dbh = $Dbh;
  }

  public function find($monthlyPaymentId) 
  {
    $stmt = $this->_Dbh->prepare("SELECT * FROM monthly_payment WHERE monthly_payment_id = ?");
    if ($stmt->execute(array($monthlyPaymentId))) {
      while ($row = $stmt->fetch()) {
	$MonthlyPayment = new MonthlyPayment();
	$MonthlyPayment->build($monthlyPaymentId, $row['amount'], $row['day_of_month']);
      }
    }
    return $MonthlyPayment;
  }

  public function getAll()
  {
    $stmt = $this->_Dbh->prepare("SELECT * FROM monthly_payment ORDER BY day_of_month ASC");
    $MonthlyPayments = array();
    if ($stmt->execute(array())) {
      while ($row = $stmt->fetch()) {
	$MonthlyPayment = new MonthlyPayment();
	$MonthlyPayment->build($monthlyPaymentId, $row['amount'], $row['day_of_month']);
	$MonthlyPayments[] = $MonthlyPayment;
      }
    }
    return $MonthlyPayments;
  } 

  public function save(MonthlyPayment $MonthlyPayment)
  {
    if ($MonthlyPayment->getId()) {
      $this->_update($MonthlyPayment);
    } else {
      $this->_insert($MonthlyPayment);
    }
    
  }

  private function _insert(MonthlyPayment $MonthlyPayment) 
  {
    $stmt = $this->_Dbh->prepare("INSERT INTO monthly_payment (amount, day_of_month) VALUES (?,?)");
    $stmt->execute(array($MonthlyPayment->getPayment(), $MonthlyPayment->getDayOfPayment()));
  }

  private function _update(MonthlyPayment $MonthlyPayment) 
  {
    $stmt = $this->_Dbh->prepare(
				 "UPDATE monthly_payment SET amount = ?, day_of_month = ? WHERE monthly_payment_id = ?"
				 );
    $stmt->execute(array(
			 $MonthlyPayment->getPayment(), 
			 $MonthlyPayment->getDayOfPayment(), 
			 $MonthlyPayment->getId()
			 ));
  }
}

?>