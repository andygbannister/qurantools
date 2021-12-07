<?php

namespace QT\Admin;

/**
 * Reports statistics - currently only used for customers
 */

abstract class Statistics
{
    // Period Types
    public const PERIOD_TYPE_SIX_MONTHS = 'six_months';
    public const PERIOD_TYPE_FOUR_WEEKS = 'four_weeks';

    // Stat Types
    public const STATISTIC_TYPE_LOGINS       = 'logins';
    public const STATISTIC_TYPE_NEW_USERS    = 'new_users';
    public const STATISTIC_TYPE_UNIQUE_USERS = 'unique_users';
    public const STATISTIC_TYPE_ACTIVITY     = 'activity';

    // Output Formats
    public const OUTPUT_FORMAT_CSV  = 'csv';
    public const OUTPUT_FORMAT_JSON = 'json';

    protected $period_type;
    protected $statistic_type;
    protected $periods; // cached
    protected $statistics; // actual array of statistics; cached
    public $entity_type;

    /**
     * An array of header and data that can be pumped into DataTables
     *
     * Use: return json_encode($this->get_statistics());
     */
    abstract public function get_statistics(): array;

    /**
     * An array of column headings that can be pumped into DataTables
     *
     * e.g ["Name", "Email Address", "Nov 2020", ...]
     */
    abstract public function get_statistics_header(): array;

    /**
     * An array of arrays that contain the actual statistical data used by DataTables
     *
     * [
     *      ["Bob",5,3,5, ... ],
     *      ["Mary",5,3,5, ... ],
     * ]
     */
    abstract public function get_statistics_data(): array;

    /**
     * Create a Statistics object
     *
     * @param string $period_type        - Are we showing the last six months or four weeks?
     * @param string $statistic_type          - Are we showing logins, new users or
     *                                     activity? Currently only logins suppported
     * @return object instance of class
     *
     * Technically, we don't actually need this function since the child
     * classes have their own consturctors, but in order to test methods in
     * this class, it needs a constructor.
     */
    public function __construct(
        string $period_type = Statistics::PERIOD_TYPE_SIX_MONTHS,
        string $statistic_type = Statistics::STATISTIC_TYPE_LOGINS
    ) {
        $this->entity_type    = 'abstract';
        $this->period_type    = $period_type;
        $this->statistic_type = $statistic_type;
    }

    public function get_period_type(): string
    {
        return $this->period_type;
    }

    public function get_statistic_type(): string
    {
        return $this->statistic_type;
    }

    /**
     * Returns the dates that the statistics are for
     */
    public function get_period_headers(array $header): array
    {
        foreach ($this->get_periods() as $period)
        {
            switch ($this->period_type) {
                case Statistics::PERIOD_TYPE_SIX_MONTHS:
                    $header[] = $period->getStartDate()->format('M Y');

                    break;

                case Statistics::PERIOD_TYPE_FOUR_WEEKS:
                    $header[] = $period->getStartDate()->format('d M') .
                        ' - ' .
                        $period->getEndDate()->format('d M');

                    break;
            }
        }
        return $header;
    }

    /**
     * Returns an empty HTML table ready for data injection from DataTables
     *
     * Use: echo $customer_statistics->get_table_html_shell();
     */
    public function get_table_html_shell(): string
    {
        if (empty($this->get_statistics()))
        {
            return "<h3>There are no " . $this->entity_type . " statistics to show</h3>";
        }

        $html = "";
        $html .=
            "<table id='" . $this->entity_type . "-statistics' class='log-statistics hoverTable qt-table'>";

        $html .= "</table>";

        return $html;
    }

    /**
     * Outputs the statistics in the given format
     *
     * Use: $json_statistics = $customer_statistics->output(Statistics::OUTPUT_FORMAT_JSON);
     *
     * @param string $format - one of Statistics::OUTPUT_FORMAT_JSON (default)
     *                                Statistics::OUTPUT_FORMAT_CSV (not supported)
     */
    public function output(?string $format = Statistics::OUTPUT_FORMAT_JSON)
    {
        switch ($format) {
            case Statistics::OUTPUT_FORMAT_CSV:
                throw new \Exception("CSV output not currently supported", 1);
                // return output_csv();
                break;
            case Statistics::OUTPUT_FORMAT_JSON:
                return $this->output_json();
                break;

            default:
                throw new \Exception("Invalid output format: $format", 1);
                break;
        }
    }

    public function output_json(): string
    {
        return \json_encode($this->get_statistics());
    }

    public function get_periods(): array
    {
        if (isset($this->periods))
        {
            return $this->periods;
        }

        $this->periods = [];

        if ($this->period_type == Statistics::PERIOD_TYPE_SIX_MONTHS)
        {
            for ($i = 0; $i <= 5; $i++)
            {
                $first_day_of_month = (new \DateTime(
                    'first day of this month'
                ))->sub(new \DateInterval('P' . $i . 'M'));

                // Y-m-t returns last day of given month
                $last_day_of_month_Ymd = (new \DateTime())
                    ->sub(new \DateInterval('P' . $i . 'M'))
                    ->format('Y-m-t');
                $last_day_of_month = \DateTime::createFromFormat(
                    'Y-m-d',
                    $last_day_of_month_Ymd
                );

                \array_unshift(
                    $this->periods,
                    new \DatePeriod(
                        $first_day_of_month,
                        new \DateInterval('P1M'),
                        $last_day_of_month,
                        1
                    )
                );
            }
        }
        else
        {
            for ($i = 0; $i <= 3; $i++)
            {
                $days_to_subtract  = ($i + 1) * 7 - 1;
                $first_day_of_week = (new \DateTime())->sub(
                    new \DateInterval('P' . $days_to_subtract . 'D')
                );
                $last_day_of_week = (new \DateTime())->sub(
                    new \DateInterval('P' . $i * 7 . 'D')
                );
                \array_unshift(
                    $this->periods,
                    new \DatePeriod(
                        $first_day_of_week,
                        new \DateInterval('P1D'),
                        $last_day_of_week,
                        1
                    )
                );
            }
        }

        return $this->periods;
    }
}
