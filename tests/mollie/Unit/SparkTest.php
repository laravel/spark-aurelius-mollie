<?php

namespace Tests\Feature;

use Laravel\Spark\Spark;
use Laravel\Spark\Tests\BaseTestCase;

class SparkTest extends BaseTestCase
{
    /** @test */
    public function canSetMollieAsBillingProvider()
    {
        Spark::useStripe(); // reset
        $this->assertFalse(Spark::billsUsing('mollie'));
        $this->assertFalse(Spark::billsUsingMollie());

        Spark::useMollie();

        $this->assertTrue(Spark::billsUsing('mollie'));
        $this->assertTrue(Spark::billsUsingMollie());
    }
}
