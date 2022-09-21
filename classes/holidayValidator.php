<?php
/**
 * Validates Holidays
 *
 *
 */
class holidayValidator {
  private $maxDays;
  private $holidayPeriodStart;
  private $holidayPeriodEnd;

  function __construct(DateTimeImmutable $holidayPeriodStart, DateTimeImmutable $holidayPeriodEnd, int $maxDays = 50) {
    $this->maxDays = $maxDays;
    $this->holidayPeriodStart = $holidayPeriodStart;
    $this->holidayPeriodEnd = $holidayPeriodEnd;
  }

  function validateDays(DateTimeImmutable $startDate, DateTimeImmutable  $endDate) {

    if($startDate > $endDate){
      throw new Error("StartDate must be before endDate");
    }
    if($this->maxDays < $startDate->diff($endDate)->format("%a")){ 
      throw new Error("Maximum time span is $this->maxDays");
    }
    if($startDate < $this->holidayPeriodStart){
      throw new Error("Days must be inside holidayPeriod ".$this->holidayPeriodStart->format('d.m.Y')." ".$this->holidayPeriodEnd->format('d.m.Y'));
    } 
    if($endDate > $this->holidayPeriodEnd){ 
      throw new Error("Days must be inside holidayPeriod ".$this->holidayPeriodStart->format('d.m.Y')." ". $this->holidayPeriodEnd->format('d.m.Y'));
    }

    return true;
  }
}
?>
