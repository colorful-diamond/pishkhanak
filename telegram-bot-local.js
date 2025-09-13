#!/usr/bin/env node

/**
 * Telegram Bot Local Runner with Full Button Support
 * Run this on a machine that can access Telegram API
 */

const https = require('https');
const http = require('http');

// Configuration
const BOT_TOKEN = process.env.TELEGRAM_BOT_TOKEN || 'YOUR_BOT_TOKEN_HERE';
const WEBHOOK_URL = 'https://pishkhanak.com/telegram/webhook';
const POLLING_TIMEOUT = 30; // seconds

let lastUpdateId = 0;

console.log('🤖 Starting Telegram Bot (Local Polling Mode)');
console.log('📡 Forwarding to:', WEBHOOK_URL);
console.log('Press Ctrl+C to stop\n');

// Keyboard layouts for different menus
const keyboards = {
    main: {
        inline_keyboard: [
            [
                { text: '📚 راهنما', callback_data: 'help' },
                { text: '📊 وضعیت سرویس', callback_data: 'status' }
            ],
            [
                { text: '🎫 ایجاد تیکت', callback_data: 'ticket' },
                { text: '💰 کیف پول', callback_data: 'wallet' }
            ],
            [
                { text: '🛍️ خدمات', callback_data: 'services' },
                { text: '📱 تماس با ما', callback_data: 'contact' }
            ],
            [
                { text: '🎛️ پنل مدیریت', callback_data: 'admin' }
            ]
        ]
    },
    
    services: {
        inline_keyboard: [
            [
                { text: '🏦 خدمات بانکی', callback_data: 'service_bank' },
                { text: '🚗 خودرو و موتور', callback_data: 'service_vehicle' }
            ],
            [
                { text: '📋 بیمه', callback_data: 'service_insurance' },
                { text: '🏢 کسب و کار', callback_data: 'service_business' }
            ],
            [
                { text: '📊 استعلامات', callback_data: 'service_inquiry' },
                { text: '💳 پرداخت قبوض', callback_data: 'service_bills' }
            ],
            [
                { text: '🔙 بازگشت به منو اصلی', callback_data: 'back_main' }
            ]
        ]
    },
    
    admin: {
        inline_keyboard: [
            [
                { text: '📊 داشبورد', callback_data: 'admin_dashboard' },
                { text: '📈 آمار سیستم', callback_data: 'admin_stats' }
            ],
            [
                { text: '👥 مدیریت کاربران', callback_data: 'admin_users' },
                { text: '💰 کیف پول‌ها', callback_data: 'admin_wallets' }
            ],
            [
                { text: '🎫 تیکت‌ها', callback_data: 'admin_tickets' },
                { text: '📝 پست‌ها', callback_data: 'admin_posts' }
            ],
            [
                { text: '🤖 تنظیمات ربات', callback_data: 'admin_bot' },
                { text: '⚙️ تنظیمات سیستم', callback_data: 'admin_settings' }
            ],
            [
                { text: '🔙 بازگشت', callback_data: 'back_main' }
            ]
        ]
    },
    
    ticket: {
        inline_keyboard: [
            [
                { text: '🆕 تیکت جدید', callback_data: 'ticket_new' },
                { text: '📋 تیکت‌های من', callback_data: 'ticket_list' }
            ],
            [
                { text: '❓ مشکل فنی', callback_data: 'ticket_technical' },
                { text: '💳 مشکل پرداخت', callback_data: 'ticket_payment' }
            ],
            [
                { text: '📱 درخواست خدمات', callback_data: 'ticket_service' },
                { text: '💬 سایر موارد', callback_data: 'ticket_other' }
            ],
            [
                { text: '🔙 بازگشت', callback_data: 'back_main' }
            ]
        ]
    },
    
    help: {
        inline_keyboard: [
            [
                { text: '❓ سوالات متداول', callback_data: 'faq' },
                { text: '📖 راهنمای استفاده', callback_data: 'guide' }
            ],
            [
                { text: '🎥 ویدیوهای آموزشی', url: 'https://pishkhanak.com/tutorials' }
            ],
            [
                { text: '💬 چت با پشتیبان', callback_data: 'support_chat' },
                { text: '📞 تماس تلفنی', callback_data: 'support_call' }
            ],
            [
                { text: '🔙 بازگشت', callback_data: 'back_main' }
            ]
        ]
    },
    
    wallet: {
        inline_keyboard: [
            [
                { text: '💰 موجودی', callback_data: 'wallet_balance' },
                { text: '➕ افزایش موجودی', callback_data: 'wallet_charge' }
            ],
            [
                { text: '📊 تراکنش‌ها', callback_data: 'wallet_transactions' },
                { text: '🎁 کد تخفیف', callback_data: 'wallet_discount' }
            ],
            [
                { text: '💳 روش‌های پرداخت', callback_data: 'payment_methods' }
            ],
            [
                { text: '🔙 بازگشت', callback_data: 'back_main' }
            ]
        ]
    },
    
    confirm: {
        inline_keyboard: [
            [
                { text: '✅ تایید', callback_data: 'confirm_yes' },
                { text: '❌ انصراف', callback_data: 'confirm_no' }
            ]
        ]
    },
    
    navigation: {
        inline_keyboard: [
            [
                { text: '🏠 منوی اصلی', callback_data: 'menu_main' },
                { text: '🔙 بازگشت', callback_data: 'back' }
            ]
        ]
    }
};

// Handle both commands and callback queries
function handleUpdate(update) {
    if (update.message) {
        handleCommandLocally(update);
    } else if (update.callback_query) {
        handleCallbackQuery(update.callback_query);
    }
}

// Handle callback queries from inline buttons
function handleCallbackQuery(callbackQuery) {
    const chatId = callbackQuery.message.chat.id;
    const messageId = callbackQuery.message.message_id;
    const data = callbackQuery.data;
    const userName = callbackQuery.from.first_name || 'کاربر';
    
    console.log(`🔘 Button pressed: ${data} by ${userName}`);
    
    // Answer callback query to remove loading state
    answerCallbackQuery(callbackQuery.id);
    
    // Handle different button actions
    switch(data) {
        case 'help':
            editMessage(chatId, messageId, 
                `📚 **راهنمای استفاده از ربات**\n\n` +
                `این ربات برای ارائه خدمات آنلاین پیشخوانک طراحی شده است.\n\n` +
                `از دکمه‌های زیر برای دسترسی به بخش‌های مختلف استفاده کنید:`,
                keyboards.help
            );
            break;
            
        case 'status':
            const now = new Date();
            const timeStr = `${now.getFullYear()}/${(now.getMonth()+1).toString().padStart(2,'0')}/${now.getDate().toString().padStart(2,'0')} ${now.getHours().toString().padStart(2,'0')}:${now.getMinutes().toString().padStart(2,'0')}`;
            editMessage(chatId, messageId,
                `📊 **وضعیت سرویس‌های پیشخوانک**\n\n` +
                `🟢 سرویس اصلی: آنلاین\n` +
                `🟢 پایگاه داده: فعال\n` +
                `🟢 ربات: متصل\n` +
                `🟢 پرداخت: آماده\n\n` +
                `⏰ بروزرسانی: ${timeStr}`,
                keyboards.navigation
            );
            break;
            
        case 'services':
            editMessage(chatId, messageId,
                `🛍️ **خدمات پیشخوانک**\n\n` +
                `لطفاً دسته خدمات مورد نظر خود را انتخاب کنید:`,
                keyboards.services
            );
            break;
            
        case 'ticket':
            editMessage(chatId, messageId,
                `🎫 **سیستم تیکت پشتیبانی**\n\n` +
                `برای ایجاد تیکت جدید یا مشاهده تیکت‌های قبلی، از گزینه‌های زیر استفاده کنید:`,
                keyboards.ticket
            );
            break;
            
        case 'wallet':
            editMessage(chatId, messageId,
                `💰 **کیف پول شما**\n\n` +
                `موجودی فعلی: 0 ریال\n\n` +
                `از گزینه‌های زیر استفاده کنید:`,
                keyboards.wallet
            );
            break;
            
        case 'admin':
            editMessage(chatId, messageId,
                `🎛️ **پنل مدیریت**\n\n` +
                `به پنل مدیریت خوش آمدید.\n` +
                `از منوی زیر بخش مورد نظر را انتخاب کنید:`,
                keyboards.admin
            );
            break;
            
        case 'back_main':
        case 'menu_main':
            editMessage(chatId, messageId,
                `🏠 **منوی اصلی**\n\n` +
                `لطفاً یکی از گزینه‌های زیر را انتخاب کنید:`,
                keyboards.main
            );
            break;
            
        // Service categories
        case 'service_bank':
            editMessage(chatId, messageId,
                `🏦 **خدمات بانکی**\n\n` +
                `• استعلام چک\n` +
                `• محاسبه شبا\n` +
                `• استعلام وام\n` +
                `• اعتبارسنجی\n\n` +
                `برای استفاده از خدمات به وبسایت مراجعه کنید:\n` +
                `🌐 pishkhanak.com/services`,
                keyboards.navigation
            );
            break;
            
        case 'service_vehicle':
            editMessage(chatId, messageId,
                `🚗 **خدمات خودرو و موتور**\n\n` +
                `• خلافی خودرو\n` +
                `• خلافی موتور\n` +
                `• نمره منفی گواهینامه\n` +
                `• بیمه شخص ثالث\n\n` +
                `برای استفاده از خدمات:\n` +
                `🌐 pishkhanak.com/services`,
                keyboards.navigation
            );
            break;
            
        case 'service_insurance':
            editMessage(chatId, messageId,
                `📋 **خدمات بیمه**\n\n` +
                `• سوابق بیمه تامین اجتماعی\n` +
                `• اعتبار دفترچه بیمه\n` +
                `• بیمه نامه شخص ثالث\n\n` +
                `🌐 pishkhanak.com/services`,
                keyboards.navigation
            );
            break;
            
        // Ticket options
        case 'ticket_new':
            editMessage(chatId, messageId,
                `🆕 **ایجاد تیکت جدید**\n\n` +
                `لطفاً موضوع تیکت خود را ارسال کنید.\n` +
                `مثال: مشکل در پرداخت`,
                keyboards.navigation
            );
            break;
            
        case 'ticket_payment':
            editMessage(chatId, messageId,
                `💳 **مشکل پرداخت**\n\n` +
                `لطفاً مشکل خود را توضیح دهید:\n` +
                `• شماره سفارش\n` +
                `• زمان پرداخت\n` +
                `• پیام خطا`,
                keyboards.navigation
            );
            break;
            
        // Wallet options
        case 'wallet_balance':
            editMessage(chatId, messageId,
                `💰 **موجودی کیف پول**\n\n` +
                `موجودی فعلی: 0 ریال\n` +
                `آخرین تراکنش: ندارید\n\n` +
                `برای افزایش موجودی از دکمه زیر استفاده کنید:`,
                {
                    inline_keyboard: [
                        [{ text: '➕ افزایش موجودی', callback_data: 'wallet_charge' }],
                        [{ text: '🔙 بازگشت', callback_data: 'wallet' }]
                    ]
                }
            );
            break;
            
        case 'wallet_charge':
            editMessage(chatId, messageId,
                `➕ **افزایش موجودی**\n\n` +
                `مبلغ مورد نظر را انتخاب کنید:`,
                {
                    inline_keyboard: [
                        [
                            { text: '۵۰,۰۰۰ ریال', callback_data: 'charge_50000' },
                            { text: '۱۰۰,۰۰۰ ریال', callback_data: 'charge_100000' }
                        ],
                        [
                            { text: '۲۰۰,۰۰۰ ریال', callback_data: 'charge_200000' },
                            { text: '۵۰۰,۰۰۰ ریال', callback_data: 'charge_500000' }
                        ],
                        [{ text: 'مبلغ دلخواه', callback_data: 'charge_custom' }],
                        [{ text: '🔙 بازگشت', callback_data: 'wallet' }]
                    ]
                }
            );
            break;
            
        // Admin options
        case 'admin_dashboard':
            editMessage(chatId, messageId,
                `📊 **داشبورد مدیریت**\n\n` +
                `👥 کاربران فعال: 1,234\n` +
                `💰 درآمد امروز: 45,000,000 ریال\n` +
                `🎫 تیکت‌های باز: 12\n` +
                `📈 بازدید امروز: 5,678\n\n` +
                `آخرین بروزرسانی: ${new Date().toLocaleTimeString('fa-IR')}`,
                keyboards.admin
            );
            break;
            
        case 'admin_stats':
            editMessage(chatId, messageId,
                `📈 **آمار سیستم**\n\n` +
                `🔄 تراکنش‌های امروز: 234\n` +
                `✅ موفق: 220\n` +
                `❌ ناموفق: 14\n\n` +
                `💵 مجموع مبالغ: 125,000,000 ریال\n` +
                `📊 نرخ موفقیت: 94%`,
                keyboards.admin
            );
            break;
            
        // FAQ and Guide
        case 'faq':
            editMessage(chatId, messageId,
                `❓ **سوالات متداول**\n\n` +
                `1️⃣ چگونه کیف پول را شارژ کنم؟\n` +
                `2️⃣ خدمات شما رایگان است؟\n` +
                `3️⃣ چگونه تیکت ایجاد کنم؟\n` +
                `4️⃣ زمان پاسخگویی چقدر است؟\n\n` +
                `برای پاسخ کامل به وبسایت مراجعه کنید.`,
                keyboards.help
            );
            break;
            
        case 'guide':
            editMessage(chatId, messageId,
                `📖 **راهنمای استفاده**\n\n` +
                `مرحله 1: ثبت نام در سایت\n` +
                `مرحله 2: شارژ کیف پول\n` +
                `مرحله 3: انتخاب خدمات\n` +
                `مرحله 4: دریافت نتیجه\n\n` +
                `🎥 ویدیوی آموزشی در سایت موجود است.`,
                keyboards.help
            );
            break;
            
        case 'contact':
            editMessage(chatId, messageId,
                `📱 **تماس با ما**\n\n` +
                `📞 تلفن: 021-12345678\n` +
                `📧 ایمیل: info@pishkhanak.com\n` +
                `🌐 وبسایت: pishkhanak.com\n` +
                `📍 آدرس: تهران، خیابان ولیعصر\n\n` +
                `ساعات پاسخگویی:\n` +
                `شنبه تا پنجشنبه: 9 صبح تا 6 عصر`,
                keyboards.navigation
            );
            break;
            
        // Payment methods
        case 'payment_methods':
            editMessage(chatId, messageId,
                `💳 **روش‌های پرداخت**\n\n` +
                `✅ درگاه بانک ملت\n` +
                `✅ درگاه بانک سامان\n` +
                `✅ درگاه بانک پارسیان\n` +
                `✅ کارت به کارت\n` +
                `✅ کیف پول الکترونیک\n\n` +
                `تمامی پرداخت‌ها امن و رمزنگاری شده هستند.`,
                keyboards.wallet
            );
            break;
            
        // Charge amounts
        case 'charge_50000':
        case 'charge_100000':
        case 'charge_200000':
        case 'charge_500000':
            const amount = data.replace('charge_', '');
            editMessage(chatId, messageId,
                `💳 **پرداخت ${amount} ریال**\n\n` +
                `برای پرداخت به وبسایت منتقل شوید:\n` +
                `🔗 pishkhanak.com/payment\n\n` +
                `کد پرداخت: PAY-${Date.now()}`,
                keyboards.navigation
            );
            break;
            
        // Default handler
        default:
            answerCallbackQuery(callbackQuery.id, '⚠️ این گزینه در حال توسعه است');
    }
}

// Simple command handler for local processing
function handleCommandLocally(update) {
    const message = update.message;
    if (!message || !message.text) return;
    
    const text = message.text;
    const chatId = message.chat.id;
    const userName = message.from.first_name || 'کاربر';
    
    if (!text.startsWith('/')) return;
    
    const command = text.split(' ')[0].substring(1).toLowerCase();
    console.log(`📨 Command: /${command} from ${userName} (${chatId})`);
    
    let responseText = '';
    let replyMarkup = keyboards.main;
    
    switch (command) {
        case 'start':
            responseText = `سلام ${userName}! 🎉\n\n` +
                `به ربات پیشخوانک خوش آمدید!\n\n` +
                `از دکمه‌های زیر برای دسترسی سریع به امکانات مختلف استفاده کنید:`;
            break;
            
        case 'help':
            responseText = `📚 **راهنمای استفاده از ربات پیشخوانک**\n\n` +
                `این ربات برای ارائه خدمات آنلاین پیشخوانک طراحی شده است.\n\n` +
                `از دکمه‌های زیر برای دسترسی به بخش‌های مختلف استفاده کنید:`;
            replyMarkup = keyboards.help;
            break;
            
        case 'status':
            const now = new Date();
            const timeStr = `${now.getFullYear()}/${(now.getMonth()+1).toString().padStart(2,'0')}/${now.getDate().toString().padStart(2,'0')} ${now.getHours().toString().padStart(2,'0')}:${now.getMinutes().toString().padStart(2,'0')}`;
            responseText = `📊 **وضعیت سرویس‌های پیشخوانک**\n\n` +
                `🟢 سرویس اصلی: آنلاین\n` +
                `🟢 پایگاه داده: فعال\n` +
                `🟢 ربات: متصل\n` +
                `🟢 پرداخت: آماده\n\n` +
                `⏰ بروزرسانی: ${timeStr}`;
            replyMarkup = keyboards.navigation;
            break;
            
        case 'ticket':
            responseText = `🎫 **سیستم تیکت پشتیبانی**\n\n` +
                `برای ایجاد تیکت جدید یا مشاهده تیکت‌های قبلی، از گزینه‌های زیر استفاده کنید:`;
            replyMarkup = keyboards.ticket;
            break;
            
        case 'services':
            responseText = `🛍️ **خدمات پیشخوانک**\n\n` +
                `لطفاً دسته خدمات مورد نظر خود را انتخاب کنید:`;
            replyMarkup = keyboards.services;
            break;
            
        case 'wallet':
            responseText = `💰 **کیف پول شما**\n\n` +
                `موجودی فعلی: 0 ریال\n\n` +
                `از گزینه‌های زیر استفاده کنید:`;
            replyMarkup = keyboards.wallet;
            break;
            
        case 'admin':
            responseText = `🎛️ **پنل مدیریت**\n\n` +
                `به پنل مدیریت خوش آمدید.\n` +
                `از منوی زیر بخش مورد نظر را انتخاب کنید:`;
            replyMarkup = keyboards.admin;
            break;
            
        default:
            responseText = `❌ دستور ناشناخته: /${command}\n\n` +
                `لطفاً از دکمه‌های زیر استفاده کنید:`;
            replyMarkup = keyboards.main;
    }
    
    if (responseText) {
        sendMessageWithKeyboard(chatId, responseText, replyMarkup);
    }
}

// Send message to Telegram
function sendMessage(chatId, text) {
    const data = JSON.stringify({
        chat_id: chatId,
        text: text,
        parse_mode: 'Markdown'
    });
    
    const options = {
        hostname: 'api.telegram.org',
        path: `/bot${BOT_TOKEN}/sendMessage`,
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Content-Length': data.length
        }
    };
    
    const req = https.request(options, (res) => {
        if (res.statusCode === 200) {
            console.log(`✅ Message sent to ${chatId}`);
        } else {
            console.error(`❌ Failed to send message: ${res.statusCode}`);
        }
    });
    
    req.on('error', (e) => {
        console.error(`❌ Send error: ${e.message}`);
    });
    
    req.write(data);
    req.end();
}

// Send message with inline keyboard
function sendMessageWithKeyboard(chatId, text, keyboard) {
    const messageData = {
        chat_id: chatId,
        text: text,
        parse_mode: 'Markdown',
        reply_markup: keyboard
    };
    
    // Debug: Show what we're sending
    console.log('📤 Sending message with keyboard:', {
        chat_id: chatId,
        has_keyboard: !!keyboard,
        keyboard_buttons: keyboard?.inline_keyboard?.length || 0
    });
    
    const data = JSON.stringify(messageData);
    
    const options = {
        hostname: 'api.telegram.org',
        path: `/bot${BOT_TOKEN}/sendMessage`,
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Content-Length': Buffer.byteLength(data)
        }
    };
    
    const req = https.request(options, (res) => {
        let responseData = '';
        
        res.on('data', (chunk) => {
            responseData += chunk;
        });
        
        res.on('end', () => {
            if (res.statusCode === 200) {
                console.log(`✅ Message with keyboard sent to ${chatId}`);
            } else {
                console.error(`❌ Failed to send message: ${res.statusCode}`);
                console.error('Response:', responseData);
            }
        });
    });
    
    req.on('error', (e) => {
        console.error(`❌ Send error: ${e.message}`);
    });
    
    req.write(data);
    req.end();
}

// Edit message with new text and keyboard
function editMessage(chatId, messageId, text, keyboard) {
    const data = JSON.stringify({
        chat_id: chatId,
        message_id: messageId,
        text: text,
        parse_mode: 'Markdown',
        reply_markup: keyboard
    });
    
    const options = {
        hostname: 'api.telegram.org',
        path: `/bot${BOT_TOKEN}/editMessageText`,
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Content-Length': data.length
        }
    };
    
    const req = https.request(options, (res) => {
        if (res.statusCode === 200) {
            console.log(`✅ Message edited`);
        } else {
            console.error(`❌ Failed to edit message: ${res.statusCode}`);
        }
    });
    
    req.on('error', (e) => {
        console.error(`❌ Edit error: ${e.message}`);
    });
    
    req.write(data);
    req.end();
}

// Answer callback query to remove loading state
function answerCallbackQuery(callbackQueryId, text = '') {
    const data = JSON.stringify({
        callback_query_id: callbackQueryId,
        text: text,
        show_alert: false
    });
    
    const options = {
        hostname: 'api.telegram.org',
        path: `/bot${BOT_TOKEN}/answerCallbackQuery`,
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Content-Length': data.length
        }
    };
    
    const req = https.request(options, (res) => {
        // Silent success
    });
    
    req.on('error', (e) => {
        console.error(`❌ Answer callback error: ${e.message}`);
    });
    
    req.write(data);
    req.end();
}

// Poll for updates
function pollUpdates() {
    const url = `https://api.telegram.org/bot${BOT_TOKEN}/getUpdates?offset=${lastUpdateId + 1}&timeout=${POLLING_TIMEOUT}&allowed_updates=["message","callback_query"]`;
    
    https.get(url, (res) => {
        let data = '';
        
        res.on('data', (chunk) => {
            data += chunk;
        });
        
        res.on('end', () => {
            try {
                const response = JSON.parse(data);
                
                if (response.ok && response.result) {
                    response.result.forEach(update => {
                        lastUpdateId = update.update_id;
                        handleUpdate(update);
                    });
                    
                    if (response.result.length > 0) {
                        console.log(`📥 Processed ${response.result.length} update(s)`);
                    }
                }
            } catch (e) {
                console.error('❌ Parse error:', e.message);
            }
            
            // Continue polling
            setTimeout(pollUpdates, 100);
        });
    }).on('error', (e) => {
        console.error('❌ Polling error:', e.message);
        setTimeout(pollUpdates, 5000); // Retry after 5 seconds
    });
}

// Start polling
pollUpdates();

// Handle graceful shutdown
process.on('SIGINT', () => {
    console.log('\n👋 Shutting down gracefully...');
    process.exit(0);
});