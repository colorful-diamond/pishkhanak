<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\TicketTemplate;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        TicketTemplate::create([
            'name' => 'عودت کیف پول',
            'content' => 'سلام وقت بخیر کاربر عزیز
توجه داشته باشید شما با استفاده از کیف پول خود می توانید بیش از ۴۰ خدمت متفاوت را دریافت کنید
ولی در صورتی که نیاز به عودت کیف پول دارید می توانید درخواست عودت خود را از طریق همین پیام پشتیبانی ثبت کنید
فقط در نظر داشته باشید که از مبلغ کیف پول ۱۰ درصد بابت مالیات کسر شده و الباقی به حساب شما واریز می شود
در صورت تایید شماره کارتی که با آن خرید داشته اید را ارسال کنید تا عودت انجام بگیرد
اگر شماره کارت یکسان نباشد عودت شما ثبت نخواهد شد
پس از تایید ۷۲بعد از ساعت کاری مبلغ به حساب شما واریز خواهد شد.',
            'is_public' => true,
            'is_active' => true,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        TicketTemplate::where('name', 'عودت کیف پول')->delete();
    }
}; 