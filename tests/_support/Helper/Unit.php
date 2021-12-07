<?php

namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Unit extends \Codeception\Module
{
    public function _beforeSuite($settings = [])
    {
    }

    public function get_future_date(int $days = 30): string
    {
        return (new \DateTime())->add(
            \DateInterval::createFromDateString("$days day")
        )->format('Y-m-d');
    }

    public function get_past_date(int $days = 30): string
    {
        return (new \DateTime())->sub(
            \DateInterval::createFromDateString("$days day")
        )->format('Y-m-d');
    }
}
