<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {

            // Generic Spark
            $table->bigIncrements('id');
            $table->unsignedBigInteger('owner_id')->index();
            $table->string('name');
            $table->string('slug')->nullable()->unique();
            $table->text('photo_url')->nullable();
            $table->string('current_billing_plan')->nullable();

            // Cashier Mollie
            $table->string('mollie_customer_id')->nullable();
            $table->string('mollie_mandate_id')->nullable();
            $table->text('extra_billing_information')->nullable();
            $table->timestamp('trial_ends_at')->nullable();

            // Spark Payment Method
            $table->string('card_brand')->nullable();
            $table->string('card_last_four')->nullable();
            $table->string('card_country')->nullable();

            // Spark Billing Address
            $table->string('billing_address')->nullable();
            $table->string('billing_address_line_2')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_zip', 25)->nullable();
            $table->string('billing_country', 2)->nullable();
            $table->string('vat_id', 50)->nullable();

            // Generic Spark
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('teams');
    }
}
