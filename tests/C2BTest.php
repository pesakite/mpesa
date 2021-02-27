<?php

namespace Pesakite\Mpesa\Tests;

use Pesakite\Mpesa\C2B;
use PHPUnit\Framework\TestCase;

class C2BTest extends TestCase
{

    public function test_simulate()
    {
        C2B::init([]);
        $return = C2B::simulate();
        $this->assertEquals('Accept the service request successfully.', $return['ResponseDescription']);
    }

}
