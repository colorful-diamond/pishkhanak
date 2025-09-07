<?php

namespace App\Services;

use App\Models\Token;

/**
 * FinnotechTokenMapper
 * 
 * Maps Finnotech API endpoints to their appropriate token categories
 * based on the service endpoint patterns.
 */
class FinnotechTokenMapper
{
    /**
     * Mapping of service endpoint patterns to token categories
     */
    private const ENDPOINT_TOKEN_MAP = [
        // Inquiry services
        'oak:deposits:get' => Token::NAME_FINNOTECH_INQUIRY,
        'oak:iban-inquiry:get' => Token::NAME_FINNOTECH_INQUIRY,
        'card:information:get' => Token::NAME_FINNOTECH_INQUIRY,
        'facility:card-to-deposit:get' => Token::NAME_FINNOTECH_INQUIRY,
        'oak:card-to-deposit:get' => Token::NAME_FINNOTECH_INQUIRY,
        'oak:deposit-to-iban:get' => Token::NAME_FINNOTECH_INQUIRY,
        'facility:card-to-iban:get' => Token::NAME_FINNOTECH_INQUIRY,
        'facility:cc-deposit-iban:get' => Token::NAME_FINNOTECH_INQUIRY,
        'facility:deposit-information:get' => Token::NAME_FINNOTECH_INQUIRY,
        'facility:account-info:get' => Token::NAME_FINNOTECH_INQUIRY,
        'oak:shahab-inquiry:get' => Token::NAME_FINNOTECH_INQUIRY,
        'oak:cif-inquiry:get' => Token::NAME_FINNOTECH_INQUIRY,
        'oak:blacklist-inquiry:get' => Token::NAME_FINNOTECH_INQUIRY,
        'ecity:cc-postal-code-inquiry:get' => Token::NAME_FINNOTECH_INQUIRY,
        'oak:cc-customer-cards:get' => Token::NAME_FINNOTECH_INQUIRY,
        'facility:cc-bank-info:get' => Token::NAME_FINNOTECH_INQUIRY,
        'ecity:postal-inquiry:get' => Token::NAME_FINNOTECH_INQUIRY,
        'ecity:postal-code-inquiry-coordinates:get' => Token::NAME_FINNOTECH_INQUIRY,

        // Credit services
        'credit:cheque-color-inquiry:get' => Token::NAME_FINNOTECH_CREDIT,
        'credit:sayad-serial-inquiry:get' => Token::NAME_FINNOTECH_CREDIT,
        'credit:chequebook-inquiry:get' => Token::NAME_FINNOTECH_CREDIT,
        'credit:sayad-id-by-serial:get' => Token::NAME_FINNOTECH_CREDIT,
        'credit:guarantee-details:get' => Token::NAME_FINNOTECH_CREDIT,
        'credit:guarantee-collaterals:get' => Token::NAME_FINNOTECH_CREDIT,
        'credit:macna-inquiry:get' => Token::NAME_FINNOTECH_CREDIT,
        'credit:sayad-transfers-chain-inquiry:post' => Token::NAME_FINNOTECH_CREDIT,
        'credit:cheque-status-inquiry:post' => Token::NAME_FINNOTECH_CREDIT,
        // Add loan inquiry pattern that was missing
        'credit:sms-facility-inquiry:get' => Token::NAME_FINNOTECH_CREDIT,
        'credit:sms-back-cheques:get' => Token::NAME_FINNOTECH_CREDIT,
        'credit:sms-guaranty-inquiry:get' => Token::NAME_FINNOTECH_CREDIT,

        // KYC services
        'kyc:sms-shahkar-send:get' => Token::NAME_FINNOTECH_KYC,
        'facility:sms-shahkar-send:get' => Token::NAME_FINNOTECH_KYC,
        'facility:sms-shahkar-verification:get' => Token::NAME_FINNOTECH_KYC,
        'kyc:identification-inquiry:get' => Token::NAME_FINNOTECH_KYC,
        'boomrang:sms-verify:execute' => Token::NAME_FINNOTECH_KYC,
        'boomrang:sms-send:execute' => Token::NAME_FINNOTECH_KYC,
        'kyc:iban-owner-verification:get' => Token::NAME_FINNOTECH_KYC,
        'kyc:iban-owner-birthdate-verification:get' => Token::NAME_FINNOTECH_KYC,
        'kyc:signature-verification:post' => Token::NAME_FINNOTECH_KYC,
        'kyc:death-status-inquiry:get' => Token::NAME_FINNOTECH_KYC,
        'kyc:compare-two-images:post' => Token::NAME_FINNOTECH_KYC,
        'kyc:compare-live-image-with-national-card-image:post' => Token::NAME_FINNOTECH_KYC,
        'kyc:compare-live-video-with-national-card-image:post' => Token::NAME_FINNOTECH_KYC,
        'facility:deposit-owner-verification:get' => Token::NAME_FINNOTECH_KYC,
        'kyc:military-inquiry:get' => Token::NAME_FINNOTECH_KYC,
        'sign:document-sign-request:post' => Token::NAME_FINNOTECH_KYC,
        'sign:signed-document:get' => Token::NAME_FINNOTECH_KYC,
        'kyc:passport-inquiry:post' => Token::NAME_FINNOTECH_KYC,
        'kyc:compare-video-with-national-card-image:post' => Token::NAME_FINNOTECH_KYC,
        'kyb:match-legal-id-with-members:get' => Token::NAME_FINNOTECH_KYC,
        'kyc:foreigner-id-inquiry:get' => Token::NAME_FINNOTECH_KYC,
        'kyc:national-card-image:get' => Token::NAME_FINNOTECH_KYC,
        'kyb:board-members:get' => Token::NAME_FINNOTECH_KYC,
        'kyb:authorized-signatories:get' => Token::NAME_FINNOTECH_KYC,
        'kyb:company-info:get' => Token::NAME_FINNOTECH_KYC,
        'kyb:corporate-journal:get' => Token::NAME_FINNOTECH_KYC,
        'kyb:enamad-inquiry:get' => Token::NAME_FINNOTECH_KYC,
        'kyc:sms-shahkar-verification:get' => Token::NAME_FINNOTECH_KYC,
        'sign:document-sign-verify:post' => Token::NAME_FINNOTECH_KYC,
        'sign:document-digital-certificate:get' => Token::NAME_FINNOTECH_KYC,

        // Promissory services
        'promissory:publish-request:post' => Token::NAME_FINNOTECH_PROMISSORY,
        'promissory:finalize:post' => Token::NAME_FINNOTECH_PROMISSORY,
        'promissory:guarantee-request:post' => Token::NAME_FINNOTECH_PROMISSORY,
        'promissory:inquiry:get' => Token::NAME_FINNOTECH_PROMISSORY,
        'promissory:publish-request-inquiry:get' => Token::NAME_FINNOTECH_PROMISSORY,
        'sign:promissory-sign-request:post' => Token::NAME_FINNOTECH_PROMISSORY,
        'sign:status-inquiry:get' => Token::NAME_FINNOTECH_PROMISSORY,
        'sign:digital-certificate:post' => Token::NAME_FINNOTECH_PROMISSORY,
        'promissory:finalize-inquiry:get' => Token::NAME_FINNOTECH_PROMISSORY,
        'promissory:verify-and-validate-certificate:post' => Token::NAME_FINNOTECH_PROMISSORY,

        // Vehicle services
        'billing:traffic-offense-image:get' => Token::NAME_FINNOTECH_VEHICLE,
        'billing:cc-negative-score:get' => Token::NAME_FINNOTECH_VEHICLE,
        'billing:driving-offense-inquiry:get' => Token::NAME_FINNOTECH_VEHICLE,
        'billing:riding-offense-inquiry:get' => Token::NAME_FINNOTECH_VEHICLE,
        'vehicle:traffic-toll-inquiry:get' => Token::NAME_FINNOTECH_VEHICLE,
        'vehicle:matching-vehicle-ownership:get' => Token::NAME_FINNOTECH_VEHICLE,
        'vehicle:active-plate-numbers:get' => Token::NAME_FINNOTECH_VEHICLE,
        'vehicle:vehicle-info-inquiry:get' => Token::NAME_FINNOTECH_VEHICLE,
        'ecity:freeway-toll-inquiry:get' => Token::NAME_FINNOTECH_VEHICLE,
        'kyc:cc-license-check-inquiry:post' => Token::NAME_FINNOTECH_VEHICLE,
        'vehicle:plate-number-history:get' => Token::NAME_FINNOTECH_VEHICLE,

        // Insurance services
        'kyc:thirdParty-insurance-inquiry:get' => Token::NAME_FINNOTECH_INSURANCE,
        'kyc:drivers-risk-inquiry:get' => Token::NAME_FINNOTECH_INSURANCE,
        'insurance:issue-travel-insurance:post' => Token::NAME_FINNOTECH_INSURANCE,
        'insurance:country-code-insurance:get' => Token::NAME_FINNOTECH_INSURANCE,
        'insurance:cities:get' => Token::NAME_FINNOTECH_INSURANCE,
        'insurance:insurance-registration:post' => Token::NAME_FINNOTECH_INSURANCE,

        // SMS services
        'facility:finnotext:post' => Token::NAME_FINNOTECH_SMS,
        'facility:finnotext-inquiry:get' => Token::NAME_FINNOTECH_SMS,
        'facility:finnotext-otp:post' => Token::NAME_FINNOTECH_SMS,
        'facility:receive-sms:get' => Token::NAME_FINNOTECH_SMS,
    ];

    /**
     * Mapping of URL patterns to token categories
     */
    private const URL_PATTERN_TOKEN_MAP = [
        // Inquiry patterns
        '/oak.*iban.*inquiry/i' => Token::NAME_FINNOTECH_INQUIRY,
        '/oak.*deposit/i' => Token::NAME_FINNOTECH_INQUIRY,
        '/facility.*card.*deposit/i' => Token::NAME_FINNOTECH_INQUIRY,
        '/facility.*card.*iban/i' => Token::NAME_FINNOTECH_INQUIRY,
        '/facility.*deposit.*iban/i' => Token::NAME_FINNOTECH_INQUIRY,
        '/facility.*account.*info/i' => Token::NAME_FINNOTECH_INQUIRY,
        '/oak.*shahab/i' => Token::NAME_FINNOTECH_INQUIRY,
        '/oak.*cif/i' => Token::NAME_FINNOTECH_INQUIRY,
        '/oak.*blacklist/i' => Token::NAME_FINNOTECH_INQUIRY,
        '/ecity.*postal/i' => Token::NAME_FINNOTECH_INQUIRY,
        '/mpg.*cards/i' => Token::NAME_FINNOTECH_INQUIRY,
        '/facility.*bank.*info/i' => Token::NAME_FINNOTECH_INQUIRY,

        // Credit patterns
        '/credit.*cheque/i' => Token::NAME_FINNOTECH_CREDIT,
        '/credit.*sayad/i' => Token::NAME_FINNOTECH_CREDIT,
        '/credit.*guarantee/i' => Token::NAME_FINNOTECH_CREDIT,
        '/credit.*macna/i' => Token::NAME_FINNOTECH_CREDIT,
        '/credit.*facility/i' => Token::NAME_FINNOTECH_CREDIT,
        '/credit.*score/i' => Token::NAME_FINNOTECH_CREDIT,

        // KYC patterns
        '/kyc.*shahkar/i' => Token::NAME_FINNOTECH_KYC,
        '/facility.*shahkar/i' => Token::NAME_FINNOTECH_KYC,
        '/kyc.*identification/i' => Token::NAME_FINNOTECH_KYC,
        '/boomrang.*sms/i' => Token::NAME_FINNOTECH_KYC,
        '/kyc.*iban.*owner/i' => Token::NAME_FINNOTECH_KYC,
        '/kyc.*signature/i' => Token::NAME_FINNOTECH_KYC,
        '/kyc.*death/i' => Token::NAME_FINNOTECH_KYC,
        '/kyc.*compare/i' => Token::NAME_FINNOTECH_KYC,
        '/kyc.*military/i' => Token::NAME_FINNOTECH_KYC,
        '/sign.*document/i' => Token::NAME_FINNOTECH_KYC,
        '/kyc.*passport/i' => Token::NAME_FINNOTECH_KYC,
        '/kyb.*/i' => Token::NAME_FINNOTECH_KYC,
        '/kyc.*foreigner/i' => Token::NAME_FINNOTECH_KYC,
        '/kyc.*national.*card/i' => Token::NAME_FINNOTECH_KYC,
        '/facility.*deposit.*owner/i' => Token::NAME_FINNOTECH_KYC,

        // Promissory patterns
        '/promissory.*/i' => Token::NAME_FINNOTECH_PROMISSORY,
        '/sign.*promissory/i' => Token::NAME_FINNOTECH_PROMISSORY,

        // Vehicle patterns
        '/billing.*traffic/i' => Token::NAME_FINNOTECH_VEHICLE,
        '/billing.*driving/i' => Token::NAME_FINNOTECH_VEHICLE,
        '/billing.*riding/i' => Token::NAME_FINNOTECH_VEHICLE,
        '/vehicle.*/i' => Token::NAME_FINNOTECH_VEHICLE,
        '/ecity.*freeway/i' => Token::NAME_FINNOTECH_VEHICLE,
        '/kyc.*license.*check/i' => Token::NAME_FINNOTECH_VEHICLE,

        // Insurance patterns
        '/kyc.*insurance/i' => Token::NAME_FINNOTECH_INSURANCE,
        '/kyc.*drivers.*risk/i' => Token::NAME_FINNOTECH_INSURANCE,
        '/insurance.*/i' => Token::NAME_FINNOTECH_INSURANCE,

        // SMS patterns
        '/facility.*finnotext/i' => Token::NAME_FINNOTECH_SMS,
        '/facility.*sms/i' => Token::NAME_FINNOTECH_SMS,
    ];

    /**
     * Get the appropriate token name for a given endpoint.
     *
     * @param string $endpoint
     * @return string
     */
    public static function getTokenNameForEndpoint(string $endpoint): string
    {
        // First, try to match exact service patterns
        foreach (self::ENDPOINT_TOKEN_MAP as $pattern => $tokenName) {
            if (str_contains($endpoint, $pattern) || str_contains($endpoint, str_replace(':', '/', $pattern))) {
                return $tokenName;
            }
        }

        // Then try URL pattern matching
        foreach (self::URL_PATTERN_TOKEN_MAP as $pattern => $tokenName) {
            if (preg_match($pattern, $endpoint)) {
                return $tokenName;
            }
        }

        // Default fallback to the original fino token
        return Token::NAME_FINNOTECH;
    }

    /**
     * Get all available Finnotech token categories.
     *
     * @return array
     */
    public static function getAllTokenCategories(): array
    {
        return [
            Token::NAME_FINNOTECH_INQUIRY,
            Token::NAME_FINNOTECH_CREDIT,
            Token::NAME_FINNOTECH_KYC,
            Token::NAME_FINNOTECH_TOKEN,
            Token::NAME_FINNOTECH_PROMISSORY,
            Token::NAME_FINNOTECH_VEHICLE,
            Token::NAME_FINNOTECH_INSURANCE,
            Token::NAME_FINNOTECH_SMS,
        ];
    }

    /**
     * Get token category display names.
     *
     * @return array
     */
    public static function getTokenCategoryLabels(): array
    {
        return [
            Token::NAME_FINNOTECH_INQUIRY => 'Inquiry Services',
            Token::NAME_FINNOTECH_CREDIT => 'Credit Services',
            Token::NAME_FINNOTECH_KYC => 'KYC Services',
            Token::NAME_FINNOTECH_TOKEN => 'Token Services',
            Token::NAME_FINNOTECH_PROMISSORY => 'Promissory Services',
            Token::NAME_FINNOTECH_VEHICLE => 'Vehicle Services',
            Token::NAME_FINNOTECH_INSURANCE => 'Insurance Services',
            Token::NAME_FINNOTECH_SMS => 'SMS Services',
        ];
    }

    /**
     * Check if a token name is a Finnotech category token.
     *
     * @param string $tokenName
     * @return bool
     */
    public static function isFinnotechCategoryToken(string $tokenName): bool
    {
        return in_array($tokenName, self::getAllTokenCategories());
    }

    /**
     * Test and verify which token an endpoint would use.
     * Useful for debugging and verification.
     *
     * @param string $endpoint
     * @return array
     */
    public static function analyzeEndpoint(string $endpoint): array
    {
        $tokenName = self::getTokenNameForEndpoint($endpoint);
        $matchedBy = 'fallback';
        $matchedPattern = null;

        // Check which pattern matched
        foreach (self::ENDPOINT_TOKEN_MAP as $pattern => $token) {
            if (str_contains($endpoint, $pattern) || str_contains($endpoint, str_replace(':', '/', $pattern))) {
                $matchedBy = 'exact_service_pattern';
                $matchedPattern = $pattern;
                break;
            }
        }

        if ($matchedBy === 'fallback') {
            foreach (self::URL_PATTERN_TOKEN_MAP as $pattern => $token) {
                if (preg_match($pattern, $endpoint)) {
                    $matchedBy = 'url_pattern';
                    $matchedPattern = $pattern;
                    break;
                }
            }
        }

        return [
            'endpoint' => $endpoint,
            'token_category' => $tokenName,
            'matched_by' => $matchedBy,
            'matched_pattern' => $matchedPattern,
            'is_category_token' => self::isFinnotechCategoryToken($tokenName),
            'token_label' => self::getTokenCategoryLabels()[$tokenName] ?? $tokenName,
        ];
    }

    /**
     * Test multiple endpoints at once for verification.
     *
     * @param array $endpoints
     * @return array
     */
    public static function analyzeMultipleEndpoints(array $endpoints): array
    {
        $results = [];
        foreach ($endpoints as $endpoint) {
            $results[] = self::analyzeEndpoint($endpoint);
        }
        return $results;
    }

    /**
     * Get all service patterns that map to a specific token category.
     *
     * @param string $tokenName
     * @return array
     */
    public static function getServicePatternsForToken(string $tokenName): array
    {
        $patterns = [];
        
        // Get exact service patterns
        foreach (self::ENDPOINT_TOKEN_MAP as $pattern => $token) {
            if ($token === $tokenName) {
                $patterns['service_patterns'][] = $pattern;
            }
        }
        
        // Get URL patterns
        foreach (self::URL_PATTERN_TOKEN_MAP as $pattern => $token) {
            if ($token === $tokenName) {
                $patterns['url_patterns'][] = $pattern;
            }
        }
        
        return $patterns;
    }
}