<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {

            // Generic Spark
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 60);
            $table->rememberToken();
            $table->text('photo_url')->nullable();
            $table->tinyInteger('uses_two_factor_auth')->default(0);
            $table->string('authy_id')->nullable();
            $table->string('country_code', 10)->nullable();
            $table->string('phone', 25)->nullable();
            $table->string('two_factor_reset_code', 100)->nullable();
            $table->unsignedBigInteger('current_team_id')->nullable();
            $table->string('current_billing_plan')->nullable();

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

            // Cashier Mollie
            $table->string('mollie_customer_id')->nullable();
            $table->string('mollie_mandate_id')->nullable();
            $table->text('extra_billing_information')->nullable();
            $table->timestamp('trial_ends_at')->nullable();

            // Generic Spark
            $table->timestamp('last_read_announcements_at')->nullable();
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
        Schema::drop('users');
    }
}
