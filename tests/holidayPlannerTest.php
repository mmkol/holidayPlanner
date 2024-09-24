<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class holidayPlannerTest extends TestCase
{
    private function createValidator(string $startDate = "01.04.2020", string $endDate = "31.3.2021"): holidayValidator {
        $HolidaySeasonStartDate = new DateTimeImmutable($startDate);
        $HolidaySeasonEndDate = new DateTimeImmutable($endDate);
        
        $holidays = $this->getNationalHolidays();
        return new holidayValidator($HolidaySeasonStartDate, $HolidaySeasonEndDate, $holidays, [7], 50);
    }

    private function getNationalHolidays() {
        $holidays = array(
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
        $holidays = array_map(fn($holiday) => new DateTimeImmutable($holiday), $holidays);
        return $holidays;
    }
    private function getHolidayPlanner(): holidayPlanner {
        $validValidator = $this->createValidator();
        return new holidayPlanner($validValidator);


    }
    public function testValidValidator(): void
    {
        $validValidator = $this->createValidator();
        $holidayPlanner = new holidayPlanner($validValidator);

        $this->assertInstanceOf($this->getHolidayPlanner()::class, $holidayPlanner);
    }
    public function testInvalidValidator(): void
    {
        $startDate = new DateTimeImmutable("23.4.2021");
        $endDate = new DateTimeImmutable("22.4.2021");
        $this->expectException(Throwable::class);
        $holidayPlanner = new holidayPlanner("invalidValidator");
        $holidays = $holidayPlanner->getHolidaysBetween($startDate, $endDate);
        $this->assertEmpty($holidays);
    }
    public function testOneDayHoliday(): void
    {
        $startDate = new DateTimeImmutable("3.6.2020");
        $endDate = new DateTimeImmutable("3.6.2020");
        $holidayPlanner = $this->getHolidayPlanner();
        $holidays = $holidayPlanner->getHolidaysBetween($startDate, $endDate);
        $this->assertEquals(1, $holidays);

    }
    public function testTwoDayHoliday(): void
    {
        $startDate = new DateTimeImmutable("3.6.2020");
        $endDate = new DateTimeImmutable("4.6.2020");
        $holidayPlanner = $this->getHolidayPlanner();
        $holidays = $holidayPlanner->getHolidaysBetween($startDate, $endDate);
        $this->assertEquals(2, $holidays);

    }
    public function testWeekHoliday(): void
    {
        $startDate = new DateTimeImmutable("1.6.2020");
        $endDate = new DateTimeImmutable("7.6.2020");
        $holidayPlanner = $this->getHolidayPlanner();
        $holidays = $holidayPlanner->getHolidaysBetween($startDate, $endDate);
        $this->assertEquals(6, $holidays);

    }
    public function testNationalHoliday(): void
    {
        $startDate = new DateTimeImmutable("14.6.2020");
        $endDate = new DateTimeImmutable("21.6.2020");
        $holidayPlanner = $this->getHolidayPlanner();
        $holidays = $holidayPlanner->getHolidaysBetween($startDate, $endDate);
        $this->assertEquals(5, $holidays);
    }
    public function testWith50Holiday(): void
    {
        $startDate = new DateTimeImmutable("1.6.2020");
        $endDate = new DateTimeImmutable("21.7.2020");
        $holidayPlanner = $this->getHolidayPlanner();
        $holidays = $holidayPlanner->getHolidaysBetween($startDate, $endDate);
        $this->assertEquals(43, $holidays);
    }
    public function testWith51Holiday(): void
    {
        $startDate = new DateTimeImmutable("1.6.2020");
        $endDate = new DateTimeImmutable("22.7.2020");
        $holidayPlanner = $this->getHolidayPlanner();
        $this->expectException(Throwable::class);
        $this->expectExceptionMessage('Maximum time span is 50');
        $holidays = $holidayPlanner->getHolidaysBetween($startDate, $endDate);
        $this->assertEmpty($holidays);
    }
    public function testStartsBeforeHolidaySeason(): void
    {
        $startDate = new DateTimeImmutable("1.3.2020");
        $endDate = new DateTimeImmutable("14.4.2020");
        $holidayPlanner = $this->getHolidayPlanner();
        $this->expectException(Throwable::class);
        $this->expectExceptionMessage('Days must be inside holidayPeriod 01.04.2020 31.03.2021');
        $holidays = $holidayPlanner->getHolidaysBetween($startDate, $endDate);
        $this->assertEmpty($holidays);
    }
     public function testEndsAfterHolidaySeason(): void
    {
        $startDate = new DateTimeImmutable("19.3.2021");
        $endDate = new DateTimeImmutable("22.4.2021");
        $holidayPlanner = $this->getHolidayPlanner();
        $this->expectException(Throwable::class);
        $this->expectExceptionMessage('Days must be inside holidayPeriod 01.04.2020 31.03.2021');
        $holidays = $holidayPlanner->getHolidaysBetween($startDate, $endDate);
        $this->assertEmpty($holidays);
    }
    public function testStartDateAfterEndDate(): void
    {
        $startDate = new DateTimeImmutable("23.4.2021");
        $endDate = new DateTimeImmutable("22.4.2021");
        $holidayPlanner = $this->getHolidayPlanner();
        $this->expectException(Throwable::class);
        $this->expectExceptionMessage('StartDate must be before endDate');
        $holidays = $holidayPlanner->getHolidaysBetween($startDate, $endDate);
        $this->assertEmpty($holidays);
    }
    
}
?>
