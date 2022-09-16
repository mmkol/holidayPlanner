<?php 
class Validator {
  private $maxDays;
  private $holidayPeriodStart;
  private $holidayPeriodEnd;
  function __construct($maxDays = 50, $holidayPeriodStart = "01.04.2020", $holidayPeriodEnd = "31.03.2021" ) {
    $this->maxDays = $maxDays;
    $this->holidayPeriodStart = new DateTimeImmutable($holidayPeriodStart);
    $this->holidayPeriodEnd = new DateTimeImmutable($holidayPeriodEnd);
  }
  function validate_days($startDate, $endDate) {
    try{
      $this->startDate = new DateTimeImmutable($startDate);
      $this->endDate = new DateTimeImmutable($endDate);
    } catch(Throwable $e) {
      throw $e;
    } 

    if($this->startDate > $this->endDate){
      throw new Error("StartDate must be before endDate");
    }
    if($this->maxDays   < $this->startDate->diff($this->endDate)->format("%a")){ 
      throw new Error("Maximum time span is $this->maxDays");
    }
    if($this->startDate < $this->holidayPeriodStart){
      throw new Error("Days must be inside holidayPeriod ".$this->holidayPeriodStart->format('d.m.y')." ".$this->holidayPeriodEnd->format('d.m.y'));
    } 
    if($this->endDate   > $this->holidayPeriodEnd){ 
      throw new Error("Days must be inside holidayPeriod ".$this->holidayPeriodStart->format('d.m.y')." ". $this->holidayPeriodEnd->format('d.m.y'));
    }
 
    return true;
  }
}

class HolidayPlanner {
  private $holidays = array();
  private $validator;
  function __construct($holidays, Validator $validator){
    try{
      foreach ($holidays as $iterator){
      array_push($this->holidays, new DateTimeImmutable($iterator));
      }
    } catch(Throwable $error) {
      throw new Error("Invalid holidays ${error}");
    }
    $this->validator = $validator;
  }
  function getHolidaysBetween($startDate, $endDate){
    try {
      $this->validator->validate_days($startDate, $endDate);
    } catch (Throwable $e) {
      throw $e;
    }
    $startDate = new DateTimeImmutable($startDate);
    $endDate = new DateTimeImmutable($endDate);
    $holidaysBetween = 0;
    $nationalHolidaysOnWeekdays = 0;
    $dateIterator = $startDate;
    while($dateIterator <= $endDate) {
      if(Date('w', $dateIterator->getTimestamp()) != 0 ){
        $holidaysBetween++;
      }
      $dateIterator = $dateIterator->add(new DateInterval('P1D'));
    }
    foreach ($this->holidays as &$holiday) {
      if($startDate <= $holiday && $holiday <= $endDate){
        if(date('w', $holiday->getTimestamp()) != 0){
          $nationalHolidaysOnWeekdays++;
        }
      }
    }
    return $holidaysBetween - $nationalHolidaysOnWeekdays;
  }
} 


$finnishHolidays = array(
  "1.1.2020",
  "6.1.2020",
  "10.4.2020",
  "13.4.2020",
  "1.5.2020",
  "21.5.2020",
  "19.6.2020",
  "24.12.2020",
  "25.12.2020", 
  "1.1.2021",
  "6.1.2021",
  "2.4.2021",
  "5.4.2021",
  "13.5.2021",
  "20.6.2021",
  "6.12.2021",
  "24.12.2021"
);


$finnishValidator = new Validator(50, "01.4.2020", "31.3.2021");
$holidayPlanner = new HolidayPlanner($finnishHolidays, $finnishValidator);


$startDate = "1.5.2020";
$endDate = "2.6.2020";
if(isset($argv[1]) && isset($argv[2])) {
  $startDate = $argv[1];
  $endDate = $argv[2];
}
else {
  print("Käyttö ${argv[0]} ${startDate} ${endDate} \n\nEsimerkki:\n");
}
try {
  $daysBetween = $holidayPlanner->getHolidaysBetween($startDate, $endDate);
  print("Päivien ${startDate} ja ${endDate} välissä on ${daysBetween} lomapäivää\n");
} catch(Throwable $e){
  print("Error: ".$e->getMessage()."\n");
}
?>
