<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsCodToOrders extends Migration
{
    
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('is_cod', ['yes', 'no'])->default('no')->after('payment_response_id');
            $table->text('delivery_notes')->nullable()->after('is_cod');
            $table->decimal('paid_amount', 15,2)->nullable()->after('delivery_notes');
            $table->string('delivery_otp')->nullable()->after('paid_amount');
            $table->string('delivered_by')->nullable()->after('delivery_otp');
            $table->string('delivered_mobile_no')->nullable()->after('delivered_by');
            $table->timestamp('delivered_at')->nullable()->after('delivered_mobile_no');
            $table->unsignedBigInteger('delivery_authenticate_by')->nullable()->after('delivered_at');
        });
    }
   
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('is_cod');
            $table->dropColumn('delivery_notes');
            $table->dropColumn('paid_amount');
            $table->dropColumn('delivery_otp');
            $table->dropColumn('delivered_by');
            $table->dropColumn('delivered_mobile_no');
            $table->dropColumn('delivered_at');
            $table->dropColumn('delivery_authenticate_by');
        });
    }
}
