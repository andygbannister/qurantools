<?php

namespace QT\Admin;

/**
 * Reports customer statistics
 */

require_once 'statistics.php';

class CustomerStatistics extends Statistics
{
    private $customers; // cached

    /**
     * Create the CustomerStatistics object
     *
     * Use:     $customer_statistics = new CustomerStatistics($period_type, $statistics_type);
     *
     * @param string $period_type        - Are we showing the last six months or four weeks?
     * @param string $statistic_type          - Are we showing logins, new users or
     *                                     activity? Currently only logins suppported
     * @return object instance of class
     */
    public function __construct(
        string $period_type = Statistics::PERIOD_TYPE_SIX_MONTHS,
        string $statistic_type = Statistics::STATISTIC_TYPE_LOGINS
    ) {
        $this->entity_type = 'customer';

        switch ($period_type) {
            case Statistics::PERIOD_TYPE_SIX_MONTHS:
            case Statistics::PERIOD_TYPE_FOUR_WEEKS:
                $this->period_type = $period_type;
                break;

            default:
                $this->period_type = Statistics::PERIOD_TYPE_SIX_MONTHS;
                break;
        }

        switch ($statistic_type) {
            case Statistics::STATISTIC_TYPE_ACTIVITY:
            case Statistics::STATISTIC_TYPE_LOGINS:
                $this->statistic_type = $statistic_type;
                break;

            default:
                $this->statistic_type = Statistics::STATISTIC_TYPE_LOGINS;
                break;
        }
    }

    /**
     * Return array of statistics header followed by data
     *
     * Use: return json_encode($this->get_statistics());
     */
    public function get_statistics(): array
    {
        if (isset($this->statistics))
        {
            return $this->statistics;
        }

        if (empty($this->get_customers_as_array()))
        {
            $this->statistics = [];
            return $this->statistics;
        }

        $this->statistics = [
            $this->get_statistics_header(),
            $this->get_statistics_data()
        ];

        return $this->statistics;
    }

    /**
     * an array of column headings
     *
     * ["Bobby Brown", "bobby@example.com",  "Nov 2020", ...]
     */
    public function get_statistics_header(): array
    {
        $header[] = 'Name';
        $header[] = 'Email Address';

        $header = $this->get_period_headers($header);

        return $header;
    }

    public function get_statistics_data(): array
    {
        $data = [];

        foreach ($this->get_customers_as_array() as $customer)
        {
            $row   = [];
            $row[] = $customer['User Name'];
            $row[] = $customer['Email Address'];

            foreach ($this->get_periods() as $period)
            {
                $row[] = $this->get_statistic($customer['User ID'], $period);
            }

            $data[] = $row;
        }

        return $data;
    }

    /**
     * Returns a specific statistic for a customer and period
     *
     * Use: $row[] = $this->get_statistic($customer['User ID'], $period);
     *
     * @param int $user_id       - The user we are interested in
     * @param DatePeriod $period - Period we want the stat for
     *
     * @return string            - The statistic, or null if no stat found
     *
     */
    public function get_statistic(int $user_id, \DatePeriod $period): string
    {
        $start_date = $period->getStartDate()->format('Y-m-d');
        $end_date   = $period
            ->getEndDate()
            ->add(new \DateInterval('P1D'))
            ->format('Y-m-d');

        switch ($this->statistic_type) {
            case CustomerStatistics::STATISTIC_TYPE_LOGINS:
                $sql = "SELECT COUNT(*) 'statistic'
                  FROM `LOGIN-LOGS`
                 WHERE `User ID` = $user_id
                   AND `DATE AND TIME` >= '$start_date' 
                   AND `DATE AND TIME` < '$end_date'";
                break;

            case CustomerStatistics::STATISTIC_TYPE_ACTIVITY:
                $sql = "SELECT COUNT(*) 'statistic'
                  FROM `USAGE-VERSES-SEARCHES`
                 WHERE `User ID` = $user_id
                   AND `DATE AND TIME` >= '$start_date' 
                   AND `DATE AND TIME` < '$end_date'";
                break;

            default:
                return null;
                break;
        }

        $query_result = db_query($sql);

        if (db_rowcount($query_result) > 0)
        {
            $result = db_return_row($query_result)['statistic'];
            return $result;
        }
        else
        {
            return null;
        }
    }

    public function get_customers_as_array(): array
    {
        if (isset($this->customers))
        {
            return $this->customers;
        }
        $sql = "SELECT `User ID`,
                   `User Name`,
                   `Email Address`
              FROM `USERS`";

        $result = db_query($sql);

        $this->customers = [];
        while ($row = mysqli_fetch_array($result))
        {
            $this->customers[] = $row;
        }

        return $this->customers;
    }
}
