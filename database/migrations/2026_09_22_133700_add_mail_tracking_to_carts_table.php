<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            if (!Schema::hasColumn('carts', 'reminder_mail_sent_at')) {
                $table->timestamp('reminder_mail_sent_at')->nullable()->after('abandoned_sms_sent_at')
                    ->comment('Kuponsuz hatırlatma maili gönderim zamanı');
            }
            if (!Schema::hasColumn('carts', 'coupon_mail_sent_at')) {
                $table->timestamp('coupon_mail_sent_at')->nullable()->after('reminder_mail_sent_at')
                    ->comment('%10 kuponlu mail gönderim zamanı');
            }
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('carts', 'reminder_mail_sent_at')) {
                $columns[] = 'reminder_mail_sent_at';
            }
            if (Schema::hasColumn('carts', 'coupon_mail_sent_at')) {
                $columns[] = 'coupon_mail_sent_at';
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
