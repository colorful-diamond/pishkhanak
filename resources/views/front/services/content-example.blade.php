@extends('front.layouts.app')

@push('styles')
@vite(['resources/css/service-content.css'])
@endpush

@section('title', 'نمونه محتوای سرویس')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    
    <!-- Headers Example -->
    <h1>عنوان اصلی سرویس</h1>
    <p>این یک نمونه از محتوای سرویس است که با استفاده از استایل‌های جدید طراحی شده است.</p>
    
    <h2>عنوان فرعی با خط زرد</h2>
    <p>این بخش نشان می‌دهد که چگونه <strong>متن‌های مهم</strong> و <b>تاکیدی</b> نمایش داده می‌شوند.</p>
    
    <h3>عنوان سطح سوم</h3>
    <p>محتوای معمولی با <a href="#">لینک‌های داخلی</a> که هنگام هاور زیرشان خط زرد می‌آید.</p>
    
    <!-- Lists Example -->
    <h3>لیست‌ها</h3>
    <ul>
        <li>آیتم اول با علامت زرد</li>
        <li>آیتم دوم</li>
        <li>آیتم سوم</li>
    </ul>
    
    <ol>
        <li>مرحله اول با شماره در دایره آبی</li>
        <li>مرحله دوم</li>
        <li>مرحله سوم</li>
    </ol>
    
    <!-- Blockquote Example -->
    <blockquote>
        این یک نقل قول است که با پس‌زمینه آبی روشن و خط آبی در سمت راست نمایش داده می‌شود.
    </blockquote>
    
    <!-- Code Example -->
    <p>برای نمایش کد می‌توانید از <code>تگ کد</code> استفاده کنید.</p>
    
    <pre>
// نمونه کد با پس‌زمینه تیره
function calculateSheba(cardNumber) {
    return 'IR' + cardNumber;
}
    </pre>
    
    <!-- Table Example -->
    <h3>جدول اطلاعات</h3>
    <table>
        <thead>
            <tr>
                <th>نام سرویس</th>
                <th>قیمت</th>
                <th>وضعیت</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>تبدیل کارت به شبا</td>
                <td>۵,۰۰۰ تومان</td>
                <td>فعال</td>
            </tr>
            <tr>
                <td>استعلام چک</td>
                <td>۱۰,۰۰۰ تومان</td>
                <td>فعال</td>
            </tr>
        </tbody>
    </table>
    
    <!-- Info Boxes -->
    <div class="info-box">
        <strong>اطلاعات مهم:</strong> این یک باکس اطلاعاتی با پس‌زمینه آبی روشن است.
    </div>
    
    <div class="highlight-box">
        <strong>نکته:</strong> این یک باکس هایلایت با پس‌زمینه زرد و آیکون لامپ است.
    </div>
    
    <div class="warning-box">
        <strong>هشدار:</strong> این یک باکس هشدار با رنگ نارنجی است.
    </div>
    
    <div class="success-box">
        <strong>موفقیت:</strong> عملیات با موفقیت انجام شد!
    </div>
    
    <!-- Service Result -->
    <div class="service-result">
        <div class="service-result-header">
            <div class="service-result-title">نتیجه تبدیل</div>
        </div>
        <div class="service-result-row">
            <span class="service-result-label">شماره شبا:</span>
            <span class="service-result-value">IR123456789012345678901234</span>
        </div>
        <div class="service-result-row">
            <span class="service-result-label">نام بانک:</span>
            <span class="service-result-value">بانک ملی ایران</span>
        </div>
    </div>
    
    <!-- Buttons -->
    <h3>دکمه‌ها</h3>
    <a href="#" class="btn btn-primary">دکمه اصلی آبی</a>
    <a href="#" class="btn btn-secondary">دکمه ثانویه زرد</a>
    
    <!-- Form Example -->
    <h3>فرم نمونه</h3>
    <form>
        <label>شماره کارت</label>
        <input type="text" placeholder="شماره کارت ۱۶ رقمی">
        
        <label>انتخاب بانک</label>
        <select>
            <option>بانک ملی</option>
            <option>بانک ملت</option>
            <option>بانک صادرات</option>
        </select>
        
        <label>توضیحات</label>
        <textarea rows="3" placeholder="توضیحات خود را وارد کنید"></textarea>
        
        <button type="submit" class="btn btn-primary">ارسال</button>
    </form>
    
    <!-- Service Card -->
    <div class="service-card">
        <h4>کارت سرویس</h4>
        <p>این یک کارت سرویس است که هنگام هاور سایه آبی می‌گیرد.</p>
    </div>
    
    <!-- FAQ Example -->
    <div class="service-faq">
        <h3>سوالات متداول</h3>
        <div class="service-faq-item">
            <div class="service-faq-question">
                چگونه می‌توانم شماره شبا را محاسبه کنم؟
            </div>
            <div class="service-faq-answer">
                برای محاسبه شماره شبا، کافی است شماره کارت ۱۶ رقمی خود را وارد کنید.
            </div>
        </div>
        <div class="service-faq-item active">
            <div class="service-faq-question">
                آیا این سرویس رایگان است؟
            </div>
            <div class="service-faq-answer">
                خیر، هزینه استفاده از این سرویس ۵,۰۰۰ تومان است.
            </div>
        </div>
    </div>
    
</div>

<script>
// FAQ Toggle
document.querySelectorAll('.service-faq-question').forEach(question => {
    question.addEventListener('click', () => {
        const item = question.parentElement;
        item.classList.toggle('active');
    });
});
</script>
@endsection 