<?php

// $this->markTestIncomplete('This test has not been implemented yet.');

namespace QT\Admin;

require_once "library/admin/statistics.php";

class StatisticsTest extends \Codeception\Test\Unit
{
    public $statistics_stub;
    public $sample_statistics = [
        ['Entity' => 'entity 1', 'Stat_1' => 1, 'Stat_2' => 2],
        ['Entity' => 'entity 2', 'Stat_1' => 10, 'Stat_2' => 20]
    ];

    // get_period_type

    public function testGetPeriodType(): void
    {
        $this->statistics_stub = $this->getMockForAbstractClass(Statistics::class, [
            Statistics::PERIOD_TYPE_FOUR_WEEKS,
            Statistics::STATISTIC_TYPE_UNIQUE_USERS
        ]);

        $this->assertEquals(
            Statistics::PERIOD_TYPE_FOUR_WEEKS,
            $this->statistics_stub->get_period_type()
        );
    }

    // get_statistic_Type

    public function testGetStatisticType(): void
    {
        $this->statistics_stub = $this->getMockForAbstractClass(Statistics::class, [
            Statistics::PERIOD_TYPE_FOUR_WEEKS,
            Statistics::STATISTIC_TYPE_UNIQUE_USERS
        ]);

        $this->assertEquals(
            Statistics::STATISTIC_TYPE_UNIQUE_USERS,
            $this->statistics_stub->get_statistic_type()
        );
    }

    // output
    public function testOutputThrowsWhenGivenUnrecognisedFormat(): void
    {
        $this->statistics_stub = $this->getMockForAbstractClass(Statistics::class, [
            Statistics::PERIOD_TYPE_FOUR_WEEKS,
            Statistics::STATISTIC_TYPE_UNIQUE_USERS
        ]);

        $invalid_format = 'fslksdl';

        $this->expectExceptionMessage("Invalid output format: $invalid_format");

        $this->statistics_stub->output($invalid_format);
    }

    public function testOutputDefaultsToJson(): void
    {
        $this->statistics_stub = $this->getMockForAbstractClass(Statistics::class, [
            Statistics::PERIOD_TYPE_FOUR_WEEKS,
            Statistics::STATISTIC_TYPE_UNIQUE_USERS
        ]);

        $this->statistics_stub->expects($this->any())
             ->method('get_statistics')
             ->will($this->returnValue($this->sample_statistics));

        $this->assertEquals(
            json_encode($this->sample_statistics),
            $this->statistics_stub->output_json()
        );
    }

    public function testOutputForJSONFormat(): void
    {
        $this->statistics_stub = $this->getMockForAbstractClass(Statistics::class, [
            Statistics::PERIOD_TYPE_FOUR_WEEKS,
            Statistics::STATISTIC_TYPE_UNIQUE_USERS
        ]);

        $this->statistics_stub->expects($this->any())
             ->method('get_statistics')
             ->will($this->returnValue($this->sample_statistics));

        $this->assertEquals(
            json_encode($this->sample_statistics),
            $this->statistics_stub->output_json()
        );
    }

    public function testOutputForCsvFormat(): void
    {
        $this->statistics_stub = $this->getMockForAbstractClass(Statistics::class, [
            Statistics::PERIOD_TYPE_FOUR_WEEKS,
            Statistics::STATISTIC_TYPE_UNIQUE_USERS
        ]);

        $this->expectExceptionMessage("CSV output not currently supported");

        $this->assertEquals(
            json_encode($this->sample_statistics),
            $this->statistics_stub->output(Statistics::OUTPUT_FORMAT_CSV)
        );
    }

    // get_table_html_shell

    public function testGet_table_html_shellContainsTableTagsWhenStatistics(): void
    {
        $this->statistics_stub = $this->getMockForAbstractClass(Statistics::class, [
            Statistics::PERIOD_TYPE_FOUR_WEEKS,
            Statistics::STATISTIC_TYPE_UNIQUE_USERS
        ]);

        $this->statistics_stub
            ->method('get_statistics')
            ->willReturn(['some statistics']);

        $html = $this->statistics_stub->get_table_html_shell();

        $this->assertStringContainsStringIgnoringCase(
            "<table id='abstract-statistics'",
            $html
        );

        $this->assertStringContainsStringIgnoringCase("</table>", $html);
    }

    // get_periods
    public function testget_periodsForSixMonths(): void
    {
        // $this->stats = new Statistics(Statistics::PERIOD_TYPE_SIX_MONTHS);

        $this->statistics_stub = $this->getMockForAbstractClass(Statistics::class, [
            Statistics::PERIOD_TYPE_SIX_MONTHS,
            Statistics::STATISTIC_TYPE_UNIQUE_USERS
        ]);

        $periods = $this->statistics_stub->get_periods();

        $this->assertCount(6, $periods);
    }

    public function testPeriodsForSixMonthsFirstAndLastMonth(): void
    {
        $this->statistics_stub = $this->getMockForAbstractClass(Statistics::class, [
            Statistics::PERIOD_TYPE_SIX_MONTHS,
            Statistics::STATISTIC_TYPE_UNIQUE_USERS
        ]);

        $periods = $this->statistics_stub->get_periods();

        // Are they the right class?
        $this->assertInstanceOf(\DatePeriod::class, $periods[0]);
        $this->assertInstanceOf(\DatePeriod::class, $periods[5]);

        // first day of the months
        $this->assertEquals(
            (new \DateTime('first day of this month'))->format('Y-m-d'),
            $periods[5]->getStartDate()->format('Y-m-d')
        );
        $this->assertEquals(
            (new \DateTime('first day of this month'))
                ->sub(new \DateInterval('P5M'))
                ->format('Y-m-d'),
            $periods[0]->getStartDate()->format('Y-m-d')
        );

        // last day of the months
        $this->assertEquals(
            (new \DateTime())->format('Y-m-t'),
            $periods[5]->getEndDate()->format('Y-m-d')
        );
        $this->assertEquals(
            (new \DateTime())->sub(new \DateInterval('P5M'))->format('Y-m-t'),
            $periods[0]->getEndDate()->format('Y-m-d')
        );
    }

    public function testPeriodsForFourWeeks(): void
    {
        $this->statistics_stub = $this->getMockForAbstractClass(Statistics::class, [
            Statistics::PERIOD_TYPE_FOUR_WEEKS,
            Statistics::STATISTIC_TYPE_UNIQUE_USERS
        ]);

        $periods = $this->statistics_stub->get_periods();

        $this->assertCount(4, $periods);
    }

    public function testPeriodsForFourWeeksFirstAndLastWeek(): void
    {
        $this->statistics_stub = $this->getMockForAbstractClass(Statistics::class, [
            Statistics::PERIOD_TYPE_FOUR_WEEKS,
            Statistics::STATISTIC_TYPE_UNIQUE_USERS
        ]);

        $periods = $this->statistics_stub->get_periods();

        // Are they the right class?
        $this->assertInstanceOf(\DatePeriod::class, $periods[0]);
        $this->assertInstanceOf(\DatePeriod::class, $periods[3]);

        // first days of the weeks
        $this->assertEquals(
            (new \DateTime())->sub(new \DateInterval('P6D'))->format('Y-m-d'),
            $periods[3]->getStartDate()->format('Y-m-d')
        );
        $this->assertEquals(
            (new \DateTime())->sub(new \DateInterval('P27D'))->format('Y-m-d'),
            $periods[0]->getStartDate()->format('Y-m-d')
        );

        // last days of the weeks
        $this->assertEquals(
            (new \DateTime())->format('Y-m-d'),
            $periods[3]->getEndDate()->format('Y-m-d')
        );
        $this->assertEquals(
            (new \DateTime())->sub(new \DateInterval('P21D'))->format('Y-m-d'),
            $periods[0]->getEndDate()->format('Y-m-d')
        );
    }
}
