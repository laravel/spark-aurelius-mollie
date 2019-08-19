<?php

namespace Laravel\Spark\Events\PaymentMethod;

class TaxPercentageUpdated
{
    /**
     * The billable instance.
     *
     * @var mixed
     */
    public $billable;

    /**
     * @var float
     */
    protected $formerTaxPercentage;

    /**
     * Create a new event instance.
     *
     * @param mixed  $billable
     * @param float  $formerTaxPercentage
     * @return void
     */
    public function __construct($billable, $formerTaxPercentage)
    {
        $this->billable = $billable;
        $this->formerTaxPercentage = $formerTaxPercentage;
    }
}
