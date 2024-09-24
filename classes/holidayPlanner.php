<?php
/**
 * Plans holidays
 *
 *
 */
class holidayPlanner {
  private $validator;

  /**
   * @param DateTimeImmutable[] $holidays
   */
  function __construct(holidayValidator $validator){
    $this->validator = $validator;
  }

  private function holidaysBetween(DateTimeImmutable $startDate, DateTimeImmutable $endDate): int {
    $holidaysBetween = 0;
    $dateIterator = $startDate;
    while($dateIterator <= $endDate) {
      if($this->validator->dayShouldUseHoliday($dateIterator)){
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
    return $this->holidaysBetween($startDate, $endDate);
  }
}
?>
