<?php

return [
    'site_management' => 'مدیریت سایت',
    'banks' => 'بانک‌ها',
    'bank' => 'بانک',
    'name' => 'نام',
    'en_name' => 'نام انگلیسی',
    'bank_id' => 'شناسه بانک',
    'logo' => 'لوگو',
    'card_prefixes' => 'پیش‌شماره‌های کارت',
    'color' => 'رنگ',
    'is_active' => 'فعال',
    'created_at' => 'تاریخ ایجاد',
    'updated_at' => 'تاریخ به‌روزرسانی',
    'contact_messages' => 'پیام‌های تماس',
    'contact_message' => 'پیام تماس',
    'contact_name' => 'نام',
    'contact_email' => 'ایمیل',
    'contact_subject' => 'موضوع',
    
    // Token Management Translations
    'tokens' => 'توکن‌های API',
    'token' => 'توکن API',
    'api_tokens' => 'توکن‌های API',
    'provider' => 'ارائه‌دهنده',
    'access_token' => 'توکن دسترسی',
    'refresh_token' => 'توکن تازه‌سازی',
    'expires_at' => 'تاریخ انقضا',
    'refresh_expires_at' => 'تاریخ انقضای توکن تازه‌سازی',
    'last_used_at' => 'آخرین استفاده',
    'metadata' => 'فراداده',
    'token_name' => 'نام توکن',
    'token_status' => 'وضعیت توکن',
    'active_status' => 'وضعیت فعال',
    'token_health' => 'سلامت توکن',
    'access_expires' => 'انقضای دسترسی',
    'last_used' => 'آخرین استفاده',
    'token_details' => 'جزئیات توکن',
    'expiry_information' => 'اطلاعات انقضا',
    'basic_information' => 'اطلاعات پایه',
    'additional_information' => 'اطلاعات اضافی',
    
    // Token Actions
    'refresh_token' => 'تازه‌سازی توکن',
    'test_token' => 'تست توکن',
    'clear_cache' => 'پاک کردن کش',
    'refresh_all_tokens' => 'تازه‌سازی همه توکن‌ها',
    'health_check' => 'بررسی سلامت',
    'auto_generate_missing_tokens' => 'تولید خودکار توکن‌های گمشده',
    'refresh_selected' => 'تازه‌سازی انتخاب‌شده‌ها',
    'deactivate_selected' => 'غیرفعال کردن انتخاب‌شده‌ها',
    
    // Token Status
    'healthy' => 'سالم',
    'needs_refresh' => 'نیاز به تازه‌سازی',
    'expired' => 'منقضی شده',
    'inactive' => 'غیرفعال',
    'missing' => 'موجود نیست',
    'no_token' => 'بدون توکن',
    'active' => 'فعال',
    
    // Token Providers
    'jibit' => 'جیبیت',
    'finnotech' => 'فین‌تک',
    
    // Token Stats
    'total_tokens' => 'کل توکن‌ها',
    'active_tokens' => 'توکن‌های فعال',
    'expired_tokens' => 'توکن‌های منقضی',
    'tokens_need_refresh' => 'توکن‌های نیازمند تازه‌سازی',
    'all_tokens_in_system' => 'همه توکن‌ها در سیستم',
    'currently_active_tokens' => 'توکن‌های فعال فعلی',
    'tokens_that_have_expired' => 'توکن‌هایی که منقضی شده‌اند',
    'tokens_expiring_soon' => 'توکن‌هایی که به زودی منقضی می‌شوند',
    
    // Messages
    'token_created_successfully' => 'توکن با موفقیت ایجاد شد',
    'token_updated_successfully' => 'توکن با موفقیت به‌روزرسانی شد',
    'token_refreshed_successfully' => 'توکن با موفقیت تازه‌سازی شد',
    'token_refresh_failed' => 'تازه‌سازی توکن ناموفق بود',
    'token_test_successful' => 'تست توکن موفق بود',
    'token_test_failed' => 'تست توکن ناموفق بود',
    'cache_cleared' => 'کش پاک شد',
    'no_missing_tokens' => 'توکن گمشده‌ای وجود ندارد',
    'auto_generation_completed' => 'تولید خودکار کامل شد',
    'bulk_refresh_completed' => 'تازه‌سازی دسته‌ای کامل شد',
    'tokens_deactivated' => 'توکن‌ها غیرفعال شدند',
    'token_health_check' => 'بررسی سلامت توکن',
    
    // Helper Texts
    'unique_identifier_for_token' => 'شناسه منحصربه‌فرد برای توکن (مثلاً jibit، fino)',
    'whether_token_is_active' => 'آیا این توکن فعال است و قابل استفاده باشد',
    'access_token_from_api_provider' => 'توکن دسترسی از ارائه‌دهنده API',
    'refresh_token_from_api_provider' => 'توکن تازه‌سازی از ارائه‌دهنده API',
    'when_access_token_expires' => 'زمان انقضای توکن دسترسی',
    'when_refresh_token_expires' => 'زمان انقضای توکن تازه‌سازی',
    'last_time_token_was_used' => 'آخرین بار که این توکن استفاده شد',
    'additional_metadata_for_token' => 'فراداده اضافی برای توکن (فرمت JSON)',
    'click_to_copy_full_token' => 'برای کپی کردن توکن کامل کلیک کنید',
    
    // Descriptions
    'attempt_to_refresh_token' => 'این کار سعی می‌کند توکن را با استفاده از ارائه‌دهنده API تازه‌سازی کند',
    'automatically_generate_tokens' => 'این کار به‌طور خودکار توکن‌هایی را برای ارائه‌دهندگانی که توکن فعال ندارند تولید می‌کند',
    'attempt_to_refresh_all_tokens' => 'این کار سعی می‌کند همه توکن‌هایی را که نیاز به تازه‌سازی دارند تازه‌سازی کند',
    'update_form_with_new_token_values' => 'فرم با مقادیر جدید توکن به‌روزرسانی خواهد شد',
    'clear_cache_description' => 'این کار کش مربوط به توکن را پاک خواهد کرد',
    
    // Additional View Page Translations
    'copy_access_token' => 'توکن دسترسی کپی شد!',
    'copy_refresh_token' => 'توکن تازه‌سازی کپی شد!',
    'token_health_status' => 'وضعیت سلامت توکن',
    'never' => 'هرگز',
    'unknown' => 'نامشخص',
    
    // Status Labels for View
    'status_healthy' => 'سالم',
    'status_needs_refresh' => 'نیاز به تازه‌سازی',
    'status_expired' => 'منقضی شده',
    'status_inactive' => 'غیرفعال',
    
    // Empty State Messages
    'no_token_health_data' => 'داده‌ای برای سلامت توکن وجود ندارد',
    'unable_to_retrieve_health_info' => 'امکان دریافت اطلاعات سلامت توکن وجود ندارد',
    
    // Navigation & Panel
    'api_management' => 'مدیریت API',
    'token_management' => 'مدیریت توکن‌ها',
    
    // Error Messages
    'token_refresh_error' => 'خطا در تازه‌سازی توکن',
    'token_test_error' => 'خطا در تست توکن',
    'auto_generation_failed' => 'تولید خودکار ناموفق بود',
    'bulk_refresh_failed' => 'تازه‌سازی دسته‌ای ناموفق بود',
    'error_occurred' => 'خطایی رخ داده است',
    'check_logs_for_details' => 'لطفاً لاگ‌ها را برای جزئیات بیشتر بررسی کنید',
    
    // Success Messages
    'token_cache_cleared' => 'کش توکن پاک شده است',
    'tokens_refreshed_successfully' => 'توکن‌ها با موفقیت تازه‌سازی شدند',
    'form_updated_with_new_values' => 'فرم با مقادیر جدید توکن به‌روزرسانی شد',
    'all_providers_have_tokens' => 'همه ارائه‌دهندگان از قبل دارای توکن فعال هستند',
    'tokens_generated_successfully' => 'توکن‌ها با موفقیت تولید شدند',
    
    // Additional Action Labels
    'view_token' => 'مشاهده توکن',
    'edit_token' => 'ویرایش توکن',
    'delete_token' => 'حذف توکن',
    'create_new_token' => 'ایجاد توکن جدید',
    
    // Confirmation Messages
    'are_you_sure_refresh' => 'آیا مطمئن هستید که می‌خواهید این توکن را تازه‌سازی کنید؟',
    'are_you_sure_delete' => 'آیا مطمئن هستید که می‌خواهید این توکن را حذف کنید؟',
    'are_you_sure_clear_cache' => 'آیا مطمئن هستید که می‌خواهید کش توکن را پاک کنید؟',
    'are_you_sure_bulk_refresh' => 'آیا مطمئن هستید که می‌خواهید توکن‌های انتخاب شده را تازه‌سازی کنید؟',
    'are_you_sure_deactivate' => 'آیا مطمئن هستید که می‌خواهید توکن‌های انتخاب شده را غیرفعال کنید؟',
    
    // Modal Titles
    'refresh_token_modal' => 'تازه‌سازی توکن',
    'test_token_modal' => 'تست توکن',
    'clear_cache_modal' => 'پاک کردن کش',
    'delete_token_modal' => 'حذف توکن',
    'bulk_actions_modal' => 'عملیات دسته‌ای',
    
    // Form Validation Messages
    'token_name_required' => 'نام توکن الزامی است',
    'provider_required' => 'انتخاب ارائه‌دهنده الزامی است',
    'access_token_required' => 'توکن دسترسی الزامی است',
    'refresh_token_required' => 'توکن تازه‌سازی الزامی است',
    'token_name_unique' => 'نام توکن باید منحصر به فرد باشد',
    
    // Filter Labels
    'filter_by_provider' => 'فیلتر بر اساس ارائه‌دهنده',
    'filter_by_status' => 'فیلتر بر اساس وضعیت',
    'show_all' => 'نمایش همه',
    'show_active_only' => 'فقط فعال‌ها',
    'show_inactive_only' => 'فقط غیرفعال‌ها',
    
    // Table Actions
    'actions' => 'عملیات',
    'no_actions_available' => 'عملیاتی در دسترس نیست',
    'bulk_actions' => 'عملیات دسته‌ای',
    'select_all' => 'انتخاب همه',
    'deselect_all' => 'لغو انتخاب همه',
    
    // Pagination
    'showing_results' => 'نمایش نتایج',
    'of_total' => 'از',
    'results' => 'نتیجه',
    'no_records_found' => 'رکوردی یافت نشد',
    
    // Search & Sort
    'search_tokens' => 'جستجو در توکن‌ها',
    'sort_by' => 'مرتب‌سازی بر اساس',
    'sort_ascending' => 'صعودی',
    'sort_descending' => 'نزولی',
]; 