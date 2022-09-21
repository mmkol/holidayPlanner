<?php
/**
 * Plans holidays
 *
 *
 */
class holidayPlanner {
  private $holidays = array();
  private $validator;

  /**
   * @param DateTimeImmutable[] $holidays
   */
  function __construct(array $holidays, holidayValidator $validator){
    $this->validator = $validator;
    $this->holidays = $holidays;
  }

  private function dayShouldUseHoliday(DateTimeImmutable $date): bool {
    $weekDaysNotUsingHolidays = [7]; //1 - 7, Monday - Sunday
    return !in_array(Date('N', $date->getTimeStamp()), $weekDaysNotUsingHolidays);
  }

  private function nationalHolidaysOnDaysWhichUsesHolidaysBetween(DateTimeImmutable $startDate, DateTimeImmutable $endDate): int{
    $nationalHolidaysOnWeekdays = 0;
    foreach ($this->holidays as &$holiday) {
      if($startDate <= $holiday && $holiday <= $endDate){
        if($this->dayShouldUseHoliday($holiday)) {
          $nationalHolidaysOnWeekdays++;
        }
      }
    }
    return $nationalHolidaysOnWeekdays;
  }

  private function holidaysBetween(DateTimeImmutable $startDate, DateTimeImmutable $endDate): int {
    $holidaysBetween = 0;
    $dateIterator = $startDate;
    while($dateIterator <= $endDate) {
      if($this->dayShouldUseHoliday($dateIterator)){
        $holidaysBetween++;
      }
      $dateIterator = $dateIterator->add(new DateInterval('P1D'));
    }
    return $holidaysBetween;
  }

  function getHolidaysBetween(DateTimeImmutable $startDate, DateTimeImmutable $endDate): int {
    try {
      $this->validator->validateDays($startDate, $endDate);
    }
    catch ( Throwable $t ) {
      throw $t;
    }
    $holidaysBetween = $this->holidaysBetween($startDate, $endDate);
    $nationalHolidaysOnDaysWhichUsesHolidays = $this->nationalHolidaysOnDaysWhichUsesHolidaysBetween($startDate, $endDate);
    return $holidaysBetween - $nationalHolidaysOnDaysWhichUsesHolidays;
  }
}
?>
