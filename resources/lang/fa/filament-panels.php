<?php

return [
    // Filament form component translations
    'forms' => [
        'components' => [
            'repeater' => [
                'actions' => [
                    'add' => [
                        'label' => 'افزودن به :label',
                    ],
                    'delete' => [
                        'label' => 'حذف',
                    ],
                    'reorder' => [
                        'label' => 'بازچینش',
                    ],
                    'move_down' => [
                        'label' => 'انتقال به پایین',
                    ],
                    'move_up' => [
                        'label' => 'انتقال به بالا',
                    ],
                ],
            ],
            'file_upload' => [
                'placeholder' => 'هیچ فایلی انتخاب نشده',
            ],
            'key_value' => [
                'actions' => [
                    'add' => [
                        'label' => 'افزودن ردیف',
                    ],
                    'delete' => [
                        'label' => 'حذف ردیف',
                    ],
                    'reorder' => [
                        'label' => 'بازچینش ردیف',
                    ],
                ],
            ],
            'rich_editor' => [
                'dialogs' => [
                    'link' => [
                        'actions' => [
                            'link' => 'لینک',
                            'unlink' => 'حذف لینک',
                        ],
                        'label' => 'URL',
                        'placeholder' => 'URL را وارد کنید',
                    ],
                ],
                'toolbar_buttons' => [
                    'attach_files' => 'پیوست فایل',
                    'blockquote' => 'نقل قول',
                    'bold' => 'ضخیم',
                    'bullet_list' => 'لیست نقطه‌ای',
                    'code_block' => 'بلوک کد',
                    'h1' => 'عنوان',
                    'h2' => 'زیرعنوان',
                    'h3' => 'زیرزیرعنوان',
                    'italic' => 'کج',
                    'link' => 'لینک',
                    'ordered_list' => 'لیست شماره‌دار',
                    'redo' => 'بازگرد',
                    'strike' => 'خط‌دار',
                    'underline' => 'زیرخط‌دار',
                    'undo' => 'بازگشت',
                ],
            ],
            'select' => [
                'actions' => [
                    'create_option' => [
                        'modal' => [
                            'heading' => 'ایجاد',
                            'actions' => [
                                'create' => [
                                    'label' => 'ایجاد',
                                ],
                            ],
                        ],
                    ],
                ],
                'boolean' => [
                    'true' => 'بله',
                    'false' => 'خیر',
                ],
                'loading_message' => 'در حال بارگذاری...',
                'max_items_message' => 'تنها :count آیتم قابل انتخاب است.',
                'no_search_results_message' => 'هیچ گزینه‌ای با جستجوی شما مطابقت ندارد.',
                'placeholder' => 'یک گزینه انتخاب کنید',
                'searching_message' => 'در حال جستجو...',
                'search_prompt' => 'برای جستجو تایپ کنید...',
            ],
            'tags_input' => [
                'placeholder' => 'تگ جدید',
            ],
            'wizard' => [
                'actions' => [
                    'previous_step' => [
                        'label' => 'قبلی',
                    ],
                    'next_step' => [
                        'label' => 'بعدی',
                    ],
                ],
            ],
        ],
    ],

    // Table translations
    'tables' => [
        'actions' => [
            'attach' => [
                'label' => 'پیوست',
            ],
            'bulk_actions' => [
                'label' => 'عملیات گروهی',
            ],
            'delete' => [
                'label' => 'حذف',
            ],
            'detach' => [
                'label' => 'جدا کردن',
            ],
            'edit' => [
                'label' => 'ویرایش',
            ],
            'export' => [
                'label' => 'صادرات',
            ],
            'filter' => [
                'label' => 'فیلتر',
            ],
            'open_bulk_actions' => [
                'label' => 'عملیات گروهی',
            ],
            'toggle_columns' => [
                'label' => 'تغییر ستون‌ها',
            ],
            'view' => [
                'label' => 'مشاهده',
            ],
        ],
        'bulk_actions' => [
            'delete' => [
                'label' => 'حذف انتخاب شده‌ها',
                'modal' => [
                    'heading' => 'حذف :label انتخاب شده',
                    'actions' => [
                        'delete' => [
                            'label' => 'حذف',
                        ],
                    ],
                ],
                'notifications' => [
                    'deleted' => [
                        'title' => 'حذف شد',
                    ],
                ],
            ],
        ],
        'columns' => [
            'text' => [
                'more_list_items' => 'و :count مورد دیگر',
            ],
        ],
        'fields' => [
            'bulk_select_page' => [
                'label' => 'انتخاب/عدم انتخاب همه آیتم‌ها برای عملیات گروهی.',
            ],
            'bulk_select_record' => [
                'label' => 'انتخاب آیتم :key برای عملیات گروهی.',
            ],
            'search' => [
                'label' => 'جستجو',
                'placeholder' => 'جستجو',
                'indicator' => 'جستجو',
            ],
        ],
        'pagination' => [
            'label' => 'ناوبری صفحه‌بندی',
            'overview' => 'نمایش :first تا :last از :total نتیجه',
            'fields' => [
                'records_per_page' => [
                    'label' => 'در هر صفحه',
                ],
            ],
            'actions' => [
                'go_to_page' => [
                    'label' => 'رفتن به صفحه :page',
                ],
                'next' => [
                    'label' => 'بعدی',
                ],
                'previous' => [
                    'label' => 'قبلی',
                ],
            ],
        ],
        'sorting' => [
            'fields' => [
                'column' => [
                    'label' => 'مرتب‌سازی بر اساس',
                ],
                'direction' => [
                    'label' => 'جهت مرتب‌سازی',
                    'options' => [
                        'asc' => 'صعودی',
                        'desc' => 'نزولی',
                    ],
                ],
            ],
        ],
        'filters' => [
            'actions' => [
                'remove' => [
                    'label' => 'حذف فیلتر',
                ],
                'remove_all' => [
                    'label' => 'حذف همه فیلترها',
                    'tooltip' => 'حذف همه فیلترها',
                ],
                'reset' => [
                    'label' => 'بازنشانی',
                ],
            ],
            'indicator' => 'فیلترهای فعال',
            'multi_select' => [
                'placeholder' => 'همه',
            ],
            'select' => [
                'placeholder' => 'همه',
            ],
            'trashed' => [
                'label' => 'رکوردهای حذف شده',
                'only_trashed' => 'فقط رکوردهای حذف شده',
                'with_trashed' => 'با رکوردهای حذف شده',
                'without_trashed' => 'بدون رکوردهای حذف شده',
            ],
        ],
        'empty' => [
            'heading' => 'هیچ :model یافت نشد',
            'description' => 'یک :model ایجاد کنید تا شروع کنید.',
        ],
    ],

    // Actions translations
    'actions' => [
        'attach' => [
            'label' => 'پیوست :label',
            'modal' => [
                'heading' => 'پیوست :label',
                'fields' => [
                    'record_id' => [
                        'label' => ':label',
                    ],
                ],
                'actions' => [
                    'attach' => [
                        'label' => 'پیوست',
                    ],
                    'attach_another' => [
                        'label' => 'پیوست و پیوست مجدد',
                    ],
                ],
            ],
            'notifications' => [
                'attached' => [
                    'title' => 'پیوست شد',
                ],
            ],
        ],
        'associate' => [
            'label' => 'ارتباط :label',
            'modal' => [
                'heading' => 'ارتباط :label',
                'fields' => [
                    'record_id' => [
                        'label' => ':label',
                    ],
                ],
                'actions' => [
                    'associate' => [
                        'label' => 'ارتباط',
                    ],
                    'associate_another' => [
                        'label' => 'ارتباط و ارتباط مجدد',
                    ],
                ],
            ],
            'notifications' => [
                'associated' => [
                    'title' => 'ارتباط برقرار شد',
                ],
            ],
        ],
        'create' => [
            'label' => 'ایجاد :label',
            'modal' => [
                'heading' => 'ایجاد :label',
                'actions' => [
                    'create' => [
                        'label' => 'ایجاد',
                    ],
                    'create_another' => [
                        'label' => 'ایجاد و ایجاد مجدد',
                    ],
                ],
            ],
            'notifications' => [
                'created' => [
                    'title' => 'ایجاد شد',
                ],
            ],
        ],
        'delete' => [
            'label' => 'حذف',
            'modal' => [
                'heading' => 'حذف :label',
                'actions' => [
                    'delete' => [
                        'label' => 'حذف',
                    ],
                ],
            ],
            'notifications' => [
                'deleted' => [
                    'title' => 'حذف شد',
                ],
            ],
        ],
        'detach' => [
            'label' => 'جدا کردن',
            'modal' => [
                'heading' => 'جدا کردن :label',
                'actions' => [
                    'detach' => [
                        'label' => 'جدا کردن',
                    ],
                ],
            ],
            'notifications' => [
                'detached' => [
                    'title' => 'جدا شد',
                ],
            ],
        ],
        'dissociate' => [
            'label' => 'قطع ارتباط',
            'modal' => [
                'heading' => 'قطع ارتباط :label',
                'actions' => [
                    'dissociate' => [
                        'label' => 'قطع ارتباط',
                    ],
                ],
            ],
            'notifications' => [
                'dissociated' => [
                    'title' => 'ارتباط قطع شد',
                ],
            ],
        ],
        'edit' => [
            'label' => 'ویرایش',
            'modal' => [
                'heading' => 'ویرایش :label',
                'actions' => [
                    'save' => [
                        'label' => 'ذخیره تغییرات',
                    ],
                ],
            ],
            'notifications' => [
                'saved' => [
                    'title' => 'ذخیره شد',
                ],
            ],
        ],
        'force_delete' => [
            'label' => 'حذف دائمی',
            'modal' => [
                'heading' => 'حذف دائمی :label',
                'actions' => [
                    'delete' => [
                        'label' => 'حذف دائمی',
                    ],
                ],
            ],
            'notifications' => [
                'deleted' => [
                    'title' => 'حذف دائمی شد',
                ],
            ],
        ],
        'restore' => [
            'label' => 'بازیابی',
            'modal' => [
                'heading' => 'بازیابی :label',
                'actions' => [
                    'restore' => [
                        'label' => 'بازیابی',
                    ],
                ],
            ],
            'notifications' => [
                'restored' => [
                    'title' => 'بازیابی شد',
                ],
            ],
        ],
        'view' => [
            'label' => 'مشاهده',
        ],
    ],

    // Global Filament translations
    'global' => [
        'confirm' => 'تأیید',
        'cancel' => 'لغو',
        'save' => 'ذخیره',
        'submit' => 'ارسال',
        'create' => 'ایجاد',
        'edit' => 'ویرایش',
        'delete' => 'حذف',
        'remove' => 'حذف',
        'view' => 'مشاهده',
        'add' => 'افزودن',
        'close' => 'بستن',
        'open' => 'باز کردن',
        'loading' => 'در حال بارگذاری...',
        'search' => 'جستجو',
        'filter' => 'فیلتر',
        'sort' => 'مرتب‌سازی',
        'no_results' => 'نتیجه‌ای یافت نشد',
        'yes' => 'بله',
        'no' => 'خیر',
        'optional' => 'اختیاری',
        'required' => 'اجباری',
        'select_option' => 'یک گزینه انتخاب کنید',
        'select_all' => 'انتخاب همه',
        'deselect_all' => 'عدم انتخاب همه',
        'actions' => 'عملیات',
        'bulk_actions' => 'عملیات گروهی',
        'export' => 'صادرات',
        'import' => 'وارد کردن',
        'refresh' => 'بازخوانی',
        'reset' => 'بازنشانی',
        'apply' => 'اعمال',
        'clear' => 'پاک کردن',
        'previous' => 'قبلی',
        'next' => 'بعدی',
        'first' => 'اول',
        'last' => 'آخر',
    ],

    // Navigation translations
    'navigation' => [
        'label' => 'منو',
        'groups' => [
            'settings' => 'تنظیمات',
            'content' => 'محتوا',
            'users' => 'کاربران',
            'system' => 'سیستم',
        ],
    ],

    'resources' => [
        'service' => [
            'navigation_group' => 'مدیریت محتوا',
            'label' => 'سرویس',
            'plural_label' => 'سرویس‌ها',
            
            'sections' => [
                'main_content' => 'محتوای اصلی',
                'media' => 'رسانه',
                'publishing' => 'انتشار',
                'seo' => 'سئو',
                'schema' => 'نشانه‌گذاری ساختاری',
                'services' => 'سرویس‌های مرتبط',
                'faqs' => 'سوالات متداول',
            ],
            
            'fields' => [
                'id' => 'شناسه',
                'title' => 'عنوان',
                'ai_title' => 'عنوان سرویس را وارد کنید',
                'slug' => 'نامک (Slug)',
                'price' => 'قیمت',
                'category' => 'دسته‌بندی',
                'parent' => 'سرویس والد',
                'tags' => 'برچسب‌ها',
                'content' => 'محتوا',
                'content_type' => 'نوع محتوا',
                'content_helper' => 'می‌توانید محتوای معمولی وارد کنید یا شناسه محتوای هوش مصنوعی (عدد) را وارد کنید',
                'use_ai_content' => 'استفاده از محتوای هوش مصنوعی',
                'use_ai_content_helper' => 'در صورت فعال‌سازی، می‌توانید محتوای تولید شده توسط هوش مصنوعی را انتخاب کنید',
                'ai_content' => 'محتوای هوش مصنوعی',
                'ai_content_preview' => 'پیش‌نمایش محتوای هوش مصنوعی',
                'no_ai_content_selected' => 'هیچ محتوای هوش مصنوعی انتخاب نشده است',
                'summary' => 'خلاصه',
                'ai_short_description' => 'توضیح کوتاه سرویس را وارد کنید',
                'description' => 'توضیحات',
                'status' => 'وضعیت',
                'active' => 'فعال',
                'inactive' => 'غیرفعال',
                'featured' => 'ویژه',
                'author' => 'نویسنده',
                'views' => 'بازدید',
                'likes' => 'پسندیده‌ها',
                'shares' => 'اشتراک‌گذاری‌ها',
                'published_at' => 'تاریخ انتشار',
                'created_at' => 'تاریخ ایجاد',
                'updated_at' => 'تاریخ به‌روزرسانی',
                
                // Media fields
                'thumbnail' => 'تصویر شاخص',
                'thumbnail_helper_text' => 'تصویر شاخص سرویس (حداکثر 5 مگابایت)',
                'images' => 'تصاویر',
                'video_type' => 'نوع ویدیو',
                'video' => 'ویدیو',
                'local' => 'محلی',
                'youtube' => 'یوتیوب',
                'aparat' => 'آپارات',
                'vimeo' => 'ویمئو',
                'local_video_helper_text' => 'فایل ویدیوی محلی را آپلود کنید',
                'video_url_helper_text' => 'لینک ویدیو را وارد کنید',
                
                // SEO fields
                'meta_title' => 'عنوان متا',
                'meta_description' => 'توضیحات متا',
                'meta_keywords' => 'کلمات کلیدی متا',
                'og_title' => 'عنوان Open Graph',
                'og_description' => 'توضیحات Open Graph',
                'og_image' => 'تصویر Open Graph',
                'twitter_title' => 'عنوان توییتر',
                'twitter_description' => 'توضیحات توییتر',
                'twitter_image' => 'تصویر توییتر',
                
                // Schema fields
                'schema' => 'نشانه‌گذاری ساختاری',
                'property' => 'ویژگی',
                'value' => 'مقدار',
                
                // FAQ fields
                'faqs' => 'سوالات متداول',
                'question' => 'سوال',
                'answer' => 'پاسخ',
                
                // Related services
                'services' => 'سرویس‌های مرتبط',
                'name' => 'نام',
                
                // AI Content Generation
                'generate_ai_content' => 'تولید محتوای هوش مصنوعی',
                'generate_ai_content_error' => 'خطایی در تولید محتوای هوش مصنوعی رخ داد. لطفاً مجدداً تلاش کنید.',
                
                // Additional fields
                'collection' => 'مجموعه',
                'thumbnails' => 'تصاویر کوچک',
                'gallery' => 'گالری',
                'public' => 'عمومی',
                'private' => 'خصوصی',
                'manual' => 'دستی',
                'automatic' => 'خودکار',
                
                // Form and UI elements
                'required' => 'اجباری',
                'optional' => 'اختیاری',
                'select_option' => 'انتخاب کنید',
                'no_options_available' => 'هیچ گزینه‌ای در دسترس نیست',
                'search_placeholder' => 'جستجو...',
                'add_item' => 'افزودن آیتم',
                'remove_item' => 'حذف آیتم',
                'save' => 'ذخیره',
                'cancel' => 'لغو',
                'edit' => 'ویرایش',
                'delete' => 'حذف',
                'view' => 'مشاهده',
                'create' => 'ایجاد',
                'update' => 'به‌روزرسانی',
                'copy' => 'کپی',
                'duplicate' => 'کپی',
                'export' => 'صادرات',
                'import' => 'وارد کردن',
                'filter' => 'فیلتر',
                'sort' => 'مرتب‌سازی',
                'ascending' => 'صعودی',
                'descending' => 'نزولی',
                'loading' => 'در حال بارگذاری...',
                'no_records_found' => 'هیچ رکوردی یافت نشد',
                'total_records' => 'کل رکوردها',
                'page' => 'صفحه',
                'of' => 'از',
                'showing' => 'نمایش',
                'to' => 'تا',
                'results' => 'نتیجه',
                
                // Stats
                'total_services' => 'کل سرویس‌ها',
                'published_services' => 'سرویس‌های منتشر شده',
                'draft_services' => 'پیش‌نویس‌ها',
                'featured_services' => 'سرویس‌های ویژه',
                'services_last_30_days' => 'سرویس‌های 30 روز گذشته',
                'average_service_length' => 'متوسط طول سرویس',
                'characters' => 'کاراکتر',
                'from_last_month' => 'از ماه گذشته',
                'from_previous_30_days' => 'از 30 روز قبل',
                'of_total_services' => 'از کل سرویس‌ها',
                'based_on_content' => 'بر اساس محتوا',
            ],
            
            'actions' => [
                'duplicate' => 'کپی',
                'update_status' => 'به‌روزرسانی وضعیت',
                'generate_seo_meta' => 'تولید متای سئو',
                'generate_schema' => 'تولید نشانه‌گذاری ساختاری',
                'set_ai_content' => 'انتخاب محتوای هوش مصنوعی',
            ],
            
            'notifications' => [
                'Service_duplicated' => 'سرویس با موفقیت کپی شد',
                'status_updated' => 'وضعیت با موفقیت به‌روزرسانی شد',
                'ai_content_set' => 'محتوای هوش مصنوعی با موفقیت تنظیم شد',
                
                // Structured notifications (used in both simple and structured form)
                'seo_meta_generated' => [
                    'title' => 'متای سئو تولید شد',
                    'body' => 'متای سئو با موفقیت تولید شد و در سرویس ذخیره شد.',
                ],
                'schema_generated' => [
                    'title' => 'نشانه‌گذاری ساختاری تولید شد',
                    'body' => 'نشانه‌گذاری ساختاری با موفقیت تولید شد و در سرویس ذخیره شد.',
                ],
                'updated' => [
                    'title' => 'سرویس به‌روزرسانی شد',
                    'body' => 'سرویس با موفقیت به‌روزرسانی شد.',
                ],
            ],
        ],
        
        // Pages
        'pages' => [
            'service' => [
                'list' => [
                    'title' => 'لیست سرویس‌ها',
                    'navigation_label' => 'سرویس‌ها',
                ],
                'create' => [
                    'title' => 'ایجاد سرویس',
                    'navigation_label' => 'ایجاد سرویس',
                ],
                'edit' => [
                    'title' => 'ویرایش سرویس',
                    'navigation_label' => 'ویرایش',
                ],
                'view' => [
                    'title' => 'مشاهده سرویس',
                    'navigation_label' => 'مشاهده',
                ],
            ],
        ],
        
        // Widgets
        'widgets' => [
            'service' => [
                'overview' => [
                    'title' => 'آمار کلی سرویس‌ها',
                    'description' => 'نمای کلی از آمار سرویس‌ها',
                ],
            ],
        ],
    ],
]; 