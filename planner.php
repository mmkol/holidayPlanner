<?php 

require "classes/holidayPlanner.php";
require "classes/holidayValidator.php";


$finnishHolidays_2020_2021 = file("assets/holidays/finland/2020-2021.txt");
$finnishHolidays_2020_2021 = array_map(fn($holiday) => new DateTimeImmutable($holiday), $finnishHolidays_2020_2021);

$startDate = new DateTimeImmutable("01.04.2020");
$endDate = new DateTimeImmutable("24.04.2020");

if(isset($argv[1]) && isset($argv[2])) { 
  try {
    $startDate = new DateTimeImmutable($argv[1]);
    $endDate = new DateTimeImmutable($argv[2]);
  } catch(Throwable $t) {
    echo t->message;
  }
}
else {
  print("Käyttö ${argv[0]} ".$startDate->format("d.m.Y")." ".$endDate->format("d.m.Y")."\n\nEsimerkki:\n");
}

$HolidaySeasonStartDate = new DateTimeImmutable("01.04.2020");
$HolidaySeasonEndDate = new DateTimeImmutable("31.3.2021");

$finnishValidator = new holidayValidator($HolidaySeasonStartDate, $HolidaySeasonEndDate, 50);
$holidayPlanner = new holidayPlanner($finnishHolidays_2020_2021, $finnishValidator);

try {
  $daysBetween = $holidayPlanner->getHolidaysBetween($startDate, $endDate);
  print("Päivien ".$startDate->format('d.m.Y')." ja ".$endDate->format('d.m.Y')." välissä on ${daysBetween} lomapäivää\n");
} catch(Throwable $t){
  print("Error: ".$t->getMessage()."\n");
}
?>
