<?php

// Code largely taken from this GitHub issue:
// https://github.com/Codeception/Codeception/issues/4581
// As of Sept 11, 2018 this doesn't actually work - although
// it is probably more an issue of config in acceptance.suite.yml

namespace Helper;

use Codeception\Lib\Interfaces\DependsOnModule;

class TestHelper extends \Codeception\Module implements DependsOnModule
{
    public function _depends()
    {
        return ['Codeception\Module\PhpBrowser' => 'PhpBrowser is a mandatory dependency of TestHelper'];
    }

    public function _inject(\Codeception\Module\PhpBrowser $phpBrowser)
    {
        $this->phpBrowser = $phpBrowser;
    }
}
