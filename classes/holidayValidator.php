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
  private $holidays = array();
  private $weekDaysNotUsingHolidays = [7]; //1 - 7, Monday - Sunday

  function __construct(DateTimeImmutable $holidayPeriodStart, DateTimeImmutable $holidayPeriodEnd, array $holidays, array $weekDaysNotUsingHoliday = [7], int $maxDays = 50) {
    $this->maxDays = $maxDays;
    $this->holidayPeriodStart = $holidayPeriodStart;
    $this->holidayPeriodEnd = $holidayPeriodEnd;
    $this->holidays = $holidays;
    $this->weekDaysNotUsingHolidays = $weekDaysNotUsingHoliday;
  }

  function dayShouldUseHoliday(DateTimeImmutable $date): bool {
    return !$this->dayInHolidayList($date) && $this->weekDayShouldUseHoliday($date);
  }

  private function dayInHolidayList(DateTimeImmutable $date): bool {
    return in_array($date, $this->holidays);
  }

  private function weekDayShouldUseHoliday(DateTimeImmutable $date): bool {
    return !in_array(Date('N', $date->getTimeStamp()), $this->weekDaysNotUsingHolidays);
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
