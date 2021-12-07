<?php

// $this->markTestIncomplete('This test has not been implemented yet.');

namespace QT\Admin;

require_once "library/admin/customer_statistics.php";

use DMS\PHPUnitExtensions\ArraySubset\Assert; // see https://github.com/sebastianbergmann/phpunit/issues/3494

final class CustomerStatisticsTest extends \Codeception\Test\Unit
{
    public $customer_statistics; // for real behaviour
    public $customer_statistics_stub; // for mocking/stubbing
    public $customers;

    private function get_test_customers()
    {
        $customer_1 = $this->tester->createCustomerUser($this->tester, [
            'First Name'    => 'Bobby',
            'Last Name'     => 'Brown',
            'Email Address' => 'bobby@example.com'
        ]);

        $customer_2 = $this->tester->createCustomerUser($this->tester, [
            'First Name'    => 'Mad Mary',
            'Last Name'     => '',
            'Email Address' => 'mad_mary@example.com'
        ]);

        return [$customer_1, $customer_2];
    }

    // constructor
    public function testCanBeCreatedFromPeriodTypeAndStatisticsType(): void
    {
        $period_type = Statistics::PERIOD_TYPE_SIX_MONTHS;
        $stat_type   = Statistics::STATISTIC_TYPE_LOGINS;

        $this->customer_statistics = new CustomerStatistics(
            $period_type,
            $stat_type
        );

        $this->assertInstanceOf(
            CustomerStatistics::class,
            $this->customer_statistics
        );
    }

    public function testCanBeCreatedWithDefaultValues(): void
    {
        $this->customer_statistics = new CustomerStatistics();

        $this->assertInstanceOf(
            CustomerStatistics::class,
            $this->customer_statistics
        );

        $this->assertEquals(
            Statistics::PERIOD_TYPE_SIX_MONTHS,
            $this->customer_statistics->get_period_type()
        );

        $this->assertEquals(
            Statistics::STATISTIC_TYPE_LOGINS,
            $this->customer_statistics->get_statistic_type()
        );
    }

    public function testConstructorSetsDefaultValuesIfPoorArgumentsSupplied(): void
    {
        $this->customer_statistics = new CustomerStatistics(
            'dfhgkf',
            'ljdflgj'
        );

        $this->assertInstanceOf(
            CustomerStatistics::class,
            $this->customer_statistics
        );

        $this->assertEquals(
            CustomerStatistics::PERIOD_TYPE_SIX_MONTHS,
            $this->customer_statistics->get_period_type()
        );

        $this->assertEquals(
            CustomerStatistics::STATISTIC_TYPE_LOGINS,
            $this->customer_statistics->get_statistic_type()
        );
    }

    // get_statistics
    public function testGet_statisticsEmptyArrayWhenNoCustomers(): void
    {
        $this->customer_statistics_stub = $this->getMockBuilder(
            CustomerStatistics::class
        )
            ->setMethods(['get_customers_as_array'])
            ->getMock();
        $this->customer_statistics_stub
            ->method('get_customers_as_array')
            ->willReturn([]);

        $result = $this->customer_statistics_stub->get_statistics();

        $this->assertEquals([], $result);
    }

    public function testGet_StatisticsCallsGetStatisticsHeaderAndGetStatisticsData(): void
    {
        $this->customers = $this->get_test_customers();

        $this->customer_statistics_stub = $this->getMockBuilder(
            CustomerStatistics::class
        )
            ->setMethods([
                'get_customers_as_array',
                'get_statistics_header',
                'get_statistics_data'
            ])
            ->getMock();

        $this->customer_statistics_stub
            ->method('get_customers_as_array')
            ->willReturn($this->customers);

        $this->customer_statistics_stub
            ->expects($this->once())
            ->method('get_statistics_header')
            ->will($this->returnValue(['some header']));

        $this->customer_statistics_stub
            ->expects($this->once())
            ->method('get_statistics_data')
            ->will($this->returnValue(['some data']));

        $result = $this->customer_statistics_stub->get_statistics();

        \codecept_debug($result);

        $this->assertEquals($result, [['some header'], ['some data']]);
    }

    // get_statistics_header
    public function testGet_statistics_headerContainsCorrectFields(): void
    {
        $this->customers = $this->get_test_customers();

        $this->customer_statistics_stub = $this->getMockBuilder(
            CustomerStatistics::class
        )
            ->setMethods(['get_customers'])
            ->getMock();

        $this->customer_statistics_stub
            ->method('get_customers')
            ->willReturn($this->customers);

        $result = $this->customer_statistics_stub->get_statistics_header();

        $this->assertStringContainsString(
            "Name",
            $result[0]
        );

        $this->assertStringContainsString(
            "Email Address",
            $result[1]
        );
    }

    public function testGet_statistics_headerContainsPeriodHeaders(): void
    {
        $this->customers = $this->get_test_customers();

        $this->customer_statistics_stub = $this->getMockBuilder(
            CustomerStatistics::class
        )
            ->setMethods(['get_customers_as_array', 'get_period_headers'])
            ->getMock();

        $this->customer_statistics_stub
            ->method('get_customers_as_array')
            ->willReturn($this->customers);

        $this->customer_statistics_stub
            ->method('get_period_headers')
            ->willReturn(['a period header']);

        $this->customer_statistics_stub
            ->expects($this->once())
            ->method('get_period_headers');

        $result = $this->customer_statistics_stub->get_statistics_header();
    }

    // get_statistics_data
    public function testGet_statistics_dataContainsUserNameAndEmail(): void
    {
        $this->customers = $this->get_test_customers();

        \codecept_debug($this->customers);

        $this->customer_statistics_stub = $this->getMockBuilder(
            CustomerStatistics::class
        )
            ->setMethods(['get_customers_as_array'])
            ->getMock();

        $this->customer_statistics_stub
            ->method('get_customers_as_array')
            ->willReturn($this->customers);

        $result = $this->customer_statistics_stub->get_statistics_data();

        $this->assertStringContainsString("Bobby Brown", $result[0][0]);
        $this->assertStringContainsString("bobby@example.com", $result[0][1]);
        $this->assertStringContainsString("Mad Mary", $result[1][0]);
        $this->assertStringContainsString(
            "mad_mary@example.com",
            $result[1][1]
        );
    }

    public function testGet_statistics_dataCallsGet_statistic(): void
    {
        $this->customers = $this->get_test_customers();

        $this->customer_statistics_stub = $this->getMockBuilder(
            CustomerStatistics::class
        )
            ->setMethods(['get_customers_as_array', 'get_statistic'])
            ->getMock();

        $this->customer_statistics_stub
            ->method('get_customers_as_array')
            ->willReturn($this->customers);

        $this->customer_statistics_stub
            ->expects($this->exactly(2 * 6)) // 2 customers, 1 statistic for the last 6 months
            ->method('get_statistic')
            ->will($this->returnValue("2"));

        $result = $this->customer_statistics_stub->get_statistics_data();
    }

    // get_customers_as_array
    public function testGet_customersIsCached(): void
    {
        $customer = $this->tester->createCustomerUser($this->tester);

        $this->customer_statistics = new CustomerStatistics();
        $customers                 = $this->customer_statistics->get_customers_as_array();

        // now, add another customer to the database and ensure that
        // customer_statistics->customers doesn't change
        $customer_name = 'Prince ' . rand(1, 100000);
        $customer      = $this->tester->createCustomerUser($this->tester, [
            'User Name' => $customer_name
        ]);

        $new_customers = $this->customer_statistics->get_customers_as_array();

        $this->assertEquals($new_customers, $customers);
    }

    public function testCustomersHasRightColumns(): void
    {
        $customer = $this->tester->createCustomerUser($this->tester);

        $this->customer_statistics = new CustomerStatistics();
        $customers                 = $this->customer_statistics->get_customers_as_array();

        \codecept_debug($customers);

        $first_customer = $customers[0];

        \codecept_debug($first_customer);

        $this->assertArrayHasKey('User ID', $first_customer);
        $this->assertArrayHasKey('User Name', $first_customer);
    }

    // get_statistic
    public function testGet_statisticThrowsWhenMissingData(): void
    {
        $this->expectExceptionMessage(
            'Too few arguments to function QT\Admin\CustomerStatistics::get_statistic(), 0 passed'
        );

        $this->customer_statistics = new CustomerStatistics(
            CustomerStatistics::PERIOD_TYPE_SIX_MONTHS,
            CustomerStatistics::STATISTIC_TYPE_LOGINS
        );

        $this->customer_statistics->get_statistic();
    }

    public function testGet_statisticGetsLoginStatistics(): void
    {
        $activity_date_time = (new \DateTime())->add(new \DateInterval('P1Y'));

        $customer_name = 'customer ' . rand(1, 100000);
        $customer      = $this->tester->createCustomerUser($this->tester, [
            'User Name' => $customer_name
        ]);

        $this->tester->haveInDatabase('LOGIN-LOGS', [
            'User ID'       => $customer['User ID'],
            'Email Address' => $customer['Email Address'],
            'Login IP'      => '1.1.1.1',
            'Login Date'    => $activity_date_time->format('Y-m-d'),
            'Login Time'    => $activity_date_time->format('H:i:s'),
            'DATE AND TIME' => $activity_date_time->format('Y-m-d')
        ]);

        $this->customer_statistics = new CustomerStatistics(
            CustomerStatistics::PERIOD_TYPE_SIX_MONTHS,
            CustomerStatistics::STATISTIC_TYPE_LOGINS
        );

        $statistic = $this->customer_statistics->get_statistic(
            $customer['User ID'],
            new \DatePeriod(
                $activity_date_time->sub(new \DateInterval('P2D')),
                new \DateInterval('P4D'),
                $activity_date_time->add(new \DateInterval('P2D')),
                1
            )
        );
        $this->assertEquals('1', $statistic);
    }

    public function testGet_statisticGetsActivityStatistics(): void
    {
        $activity_date_time = new \DateTime();

        $consumer = $this->tester->createCustomerUser($this->tester);

        $this->tester->haveInDatabase('USAGE-VERSES-SEARCHES', [
            'USER ID'          => $consumer['User ID'],
            'DATE AND TIME'    => $activity_date_time->format('Y-m-d'),
            'VERSES OR SEARCH' => 'S',
            'LOOKED UP'        => 2,
            'REFERRING PAGE'   => 'index.php'
        ]);

        $this->customer_statistics = new CustomerStatistics(
            CustomerStatistics::PERIOD_TYPE_SIX_MONTHS,
            CustomerStatistics::STATISTIC_TYPE_ACTIVITY
        );

        $statistic = $this->customer_statistics->get_statistic(
            $consumer['User ID'],
            new \DatePeriod(
                $activity_date_time->sub(new \DateInterval('P2D')),
                new \DateInterval('P4D'),
                $activity_date_time->add(new \DateInterval('P2D')),
                1
            )
        );

        $this->assertEquals('1', $statistic);
    }
}
