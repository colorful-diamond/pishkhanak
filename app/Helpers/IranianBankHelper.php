<?php

namespace App\Helpers;

class IranianBankHelper
{
    /**
     * Iranian bank IBAN codes mapping
     * Format: IBAN code (3 digits after IR) => [bank_name, bank_persian_name, bank_logo]
     */
    private static $bankIbanMapping = [
        // Major Banks
        '017' => ['melli', 'بانک ملی ایران', 'melli.svg'],
        '012' => ['mellat', 'بانک ملت', 'mellat.svg'],
        '018' => ['tejarat', 'بانک تجارت', 'tejarat.svg'],
        '019' => ['saderat', 'بانک صادرات ایران', 'saderat.svg'],
        '013' => ['refah', 'بانک رفاه کارگران', 'refah.svg'],
        '014' => ['maskan', 'بانک مسکن', 'maskan.svg'],
        '011' => ['sepah', 'بانک سپه', 'sepah.svg'],
        '016' => ['keshavarzi', 'بانک کشاورزی', 'keshavarzi.svg'],
        '015' => ['sanaat', 'بانک صنعت و معدن', 'sanaat.svg'],
        '021' => ['postbank', 'پست بانک ایران', 'postbank.svg'],
        
        // Private Banks
        '054' => ['parsian', 'بانک پارسیان', 'parsian.svg'],
        '057' => ['pasargad', 'بانک پاسارگاد', 'pasargad.svg'],
        '056' => ['saman', 'بانک سامان', 'saman.svg'],
        '055' => ['eghtesad-novin', 'بانک اقتصاد نوین', 'eghtesad-novin.svg'],
        '058' => ['sarmayeh', 'بانک سرمایه', 'sarmayeh.svg'],
        '059' => ['sina', 'بانک سینا', 'sina.svg'],
        '060' => ['mehr-eghtesad', 'بانک مهر اقتصاد', 'mehr-eghtesad.svg'],
        '061' => ['shahr', 'بانک شهر', 'shahr.svg'],
        '062' => ['ayandeh', 'بانک آینده', 'ayandeh.svg'],
        '063' => ['ansar', 'بانک انصار', 'ansar.svg'],
        '064' => ['gardeshgari', 'بانک گردشگری', 'gardeshgari.svg'],
        '065' => ['karafarin', 'بانک کارآفرین', 'karafarin.svg'],
        '066' => ['day', 'بانک دی', 'day.svg'],
        '069' => ['iran-zamin', 'بانک ایران زمین', 'iran-zamin.svg'],
        '070' => ['resalat', 'بانک رسالت', 'resalat.svg'],
        '073' => ['kosar', 'موسسه اعتباری کوثر', 'kosar.svg'],
        '075' => ['mehr-iran', 'بانک قرض الحسنه مهر ایران', 'mehr-iran.svg'],
        '078' => ['middle-east', 'بانک خاورمیانه', 'middle-east.svg'],
        '079' => ['ghavamin', 'بانک قوامین', 'ghavamin.svg'],
        '080' => ['tosee-taavon', 'بانک توسعه تعاون', 'tosee-taavon.svg'],
        '081' => ['tosee-saderat', 'بانک توسعه صادرات', 'tosee-saderat.svg'],
        '082' => ['tosee', 'موسسه اعتباری توسعه', 'tosee.svg'],
        '090' => ['hekmat', 'بانک حکمت ایرانیان', 'hekmat.svg'],
        
        // Central Bank
        '010' => ['central-bank', 'بانک مرکزی جمهوری اسلامی ایران', 'central-bank.svg'],
    ];

    /**
     * Get bank information from IBAN
     * 
     * @param string $iban
     * @return array|null
     */
    public static function getBankFromIban($iban)
    {
        // Remove spaces and convert to uppercase
        $iban = strtoupper(str_replace(' ', '', $iban));
        
        // Check if it's a valid Iranian IBAN format
        if (!preg_match('/^IR\d{2}(\d{3})\d{19}$/', $iban, $matches)) {
            return null;
        }
        
        $bankCode = $matches[1];
        
        if (isset(self::$bankIbanMapping[$bankCode])) {
            $bankInfo = self::$bankIbanMapping[$bankCode];
            return [
                'bank_name' => $bankInfo[0],
                'bank_persian_name' => $bankInfo[1],
                'bank_logo' => $bankInfo[2],
                'iban_code' => $bankCode
            ];
        }
        
        return null;
    }

    /**
     * Get bank logo path
     * 
     * @param string $bankName
     * @return string
     */
    public static function getBankLogoPath($bankName)
    {
        // Mapping from Persian bank names to English file names
        $persianToEnglishMapping = [
            'ملی' => 'melli',
            'بانک ملی ایران' => 'melli',
            'سپه' => 'sepah',
            'بانک سپه' => 'sepah',
            'ملت' => 'mellat',
            'بانک ملت' => 'mellat',
            'تجارت' => 'tejarat',
            'بانک تجارت' => 'tejarat',
            'صادرات ایران' => 'saderat',
            'بانک صادرات ایران' => 'saderat',
            'رفاه کارگران' => 'refahkargaran',
            'بانک رفاه کارگران' => 'refahkargaran',
            'مسکن' => 'maskan',
            'بانک مسکن' => 'maskan',
            'کشاورزی' => 'keshavarzi',
            'بانک کشاورزی' => 'keshavarzi',
            'صنعت و معدن' => 'sanatmadan',
            'بانک صنعت و معدن' => 'sanatmadan',
            'پست بانک ایران' => 'post',
            'پست بانک' => 'post',
            'پارسیان' => 'parsian',
            'بانک پارسیان' => 'parsian',
            'پاسارگاد' => 'pasargad',
            'بانک پاسارگاد' => 'pasargad',
            'سامان' => 'saman',
            'بانک سامان' => 'saman',
            'اقتصاد نوین' => 'eghtesad',
            'بانک اقتصاد نوین' => 'eghtesad',
            'سرمایه' => 'sarmaye',
            'بانک سرمایه' => 'sarmaye',
            'سینا' => 'sina',
            'بانک سینا' => 'sina',
            'مهر اقتصاد' => 'mehreghtesad',
            'بانک مهر اقتصاد' => 'mehreghtesad',
            'شهر' => 'shahr',
            'بانک شهر' => 'shahr',
            'آینده' => 'ayandeh',
            'بانک آینده' => 'ayandeh',
            'انصار' => 'ansar',
            'بانک انصار' => 'ansar',
            'گردشگری' => 'gardeshgari',
            'بانک گردشگری' => 'gardeshgari',
            'کارآفرین' => 'karafarin',
            'بانک کارآفرین' => 'karafarin',
            'دی' => 'day',
            'بانک دی' => 'day',
            'ایران زمین' => 'iranzamin',
            'بانک ایران زمین' => 'iranzamin',
            'رسالت' => 'resalat',
            'بانک رسالت' => 'resalat',
            'کوثر' => 'kosar',
            'موسسه اعتباری کوثر' => 'kosar',
            'مهر ایران' => 'mehriran',
            'بانک قرض الحسنه مهر ایران' => 'mehriran',
            'خاورمیانه' => 'khavarmianeh',
            'بانک خاورمیانه' => 'khavarmianeh',
            'قوامین' => 'ghavvamin',
            'بانک قوامین' => 'ghavvamin',
            'توسعه تعاون' => 'tosetaavon',
            'بانک توسعه تعاون' => 'tosetaavon',
            'توسعه صادرات' => 'tosesaderat',
            'بانک توسعه صادرات' => 'tosesaderat',
            'توسعه' => 'tose',
            'موسسه اعتباری توسعه' => 'tose',
            'حکمت ایرانیان' => 'hekmat',
            'بانک حکمت ایرانیان' => 'hekmat',
            'مرکزی' => 'centeral',
            'بانک مرکزی جمهوری اسلامی ایران' => 'centeral',
        ];
        
        // First try to use the bank name as is (for English names)
        $englishBankName = $bankName;
        
        // If it's a Persian name, convert to English
        if (isset($persianToEnglishMapping[$bankName])) {
            $englishBankName = $persianToEnglishMapping[$bankName];
        }
        
        $logoPath = "/assets/images/banks/{$englishBankName}.svg";
        
        // Check if file exists, otherwise return default
        $fullPath = public_path($logoPath);
        if (file_exists($fullPath)) {
            return $logoPath;
        }
        
        return "/assets/images/banks/default.svg";
    }

    /**
     * Get all supported banks
     * 
     * @return array
     */
    public static function getAllBanks()
    {
        $banks = [];
        foreach (self::$bankIbanMapping as $code => $info) {
            $banks[$code] = [
                'bank_name' => $info[0],
                'bank_persian_name' => $info[1],
                'bank_logo' => $info[2],
                'iban_code' => $code
            ];
        }
        return $banks;
    }

    /**
     * Validate Iranian IBAN format
     * 
     * @param string $iban
     * @return bool
     */
    public static function validateIranianIban($iban)
    {
        $iban = strtoupper(str_replace(' ', '', $iban));
        return preg_match('/^IR\d{24}$/', $iban) === 1;
    }

    /**
     * Format IBAN for display (with spaces)
     * 
     * @param string $iban
     * @return string
     */
    public static function formatIban($iban)
    {
        $iban = strtoupper(str_replace(' ', '', $iban));
        return chunk_split($iban, 4, ' ');
    }

    /**
     * Get bank name by IBAN code
     * 
     * @param string $ibanCode
     * @return string|null
     */
    public static function getBankNameByCode($ibanCode)
    {
        if (isset(self::$bankIbanMapping[$ibanCode])) {
            return self::$bankIbanMapping[$ibanCode][1]; // Persian name
        }
        return null;
    }
} 