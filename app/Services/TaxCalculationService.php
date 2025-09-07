<?php

namespace App\Services;

use App\Models\TaxRule;
use Illuminate\Support\Collection;

class TaxCalculationService
{
    /**
     * Calculate tax for a given amount and currency
     */
    public function calculateTax(int $amount, string $currencyCode): array
    {
        $applicableRules = $this->getApplicableRules($amount, $currencyCode);
        
        if ($applicableRules->isEmpty()) {
            return [
                'total_tax' => 0,
                'tax_breakdown' => [],
                'rules_applied' => [],
            ];
        }

        $totalTax = 0;
        $taxBreakdown = [];
        $rulesApplied = [];

        foreach ($applicableRules as $rule) {
            $taxAmount = $rule->calculateTax($amount);
            $totalTax += $taxAmount;
            
            $taxBreakdown[] = [
                'rule_id' => $rule->id,
                'rule_name' => $rule->name,
                'rule_type' => $rule->type,
                'rate' => $rule->rate,
                'tax_amount' => $taxAmount,
                'formatted_rate' => $rule->getFormattedRate(),
            ];
            
            $rulesApplied[] = $rule->id;
        }

        return [
            'total_tax' => $totalTax,
            'tax_breakdown' => $taxBreakdown,
            'rules_applied' => $rulesApplied,
        ];
    }

    /**
     * Calculate tax with detailed breakdown
     */
    public function calculateDetailedTax(int $amount, string $currencyCode): array
    {
        $result = $this->calculateTax($amount, $currencyCode);
        
        return array_merge($result, [
            'original_amount' => $amount,
            'currency_code' => $currencyCode,
            'final_amount' => $amount + $result['total_tax'],
            'tax_percentage' => $amount > 0 ? round(($result['total_tax'] / $amount) * 100, 2) : 0,
        ]);
    }

    /**
     * Get applicable tax rules for amount and currency
     */
    protected function getApplicableRules(int $amount, string $currencyCode): Collection
    {
        return TaxRule::getApplicableRules($amount, $currencyCode);
    }

    /**
     * Get all active tax rules
     */
    public function getActiveTaxRules(): Collection
    {
        return TaxRule::getActiveRules();
    }

    /**
     * Get default tax rule
     */
    public function getDefaultTaxRule(): ?TaxRule
    {
        return TaxRule::getDefaultRule();
    }

    /**
     * Validate tax rule configuration
     */
    public function validateTaxRule(array $ruleData): array
    {
        $errors = [];

        // Required fields
        $required = ['name', 'type', 'rate'];
        foreach ($required as $field) {
            if (empty($ruleData[$field])) {
                $errors[] = "Field {$field} is required";
            }
        }

        // Type validation
        if (!empty($ruleData['type']) && !in_array($ruleData['type'], [TaxRule::TYPE_PERCENTAGE, TaxRule::TYPE_FIXED])) {
            $errors[] = 'Tax type must be either percentage or fixed';
        }

        // Rate validation
        if (isset($ruleData['rate'])) {
            if (!is_numeric($ruleData['rate']) || $ruleData['rate'] < 0) {
                $errors[] = 'Tax rate must be a positive number';
            }

            if ($ruleData['type'] === TaxRule::TYPE_PERCENTAGE && $ruleData['rate'] > 100) {
                $errors[] = 'Percentage tax rate cannot exceed 100%';
            }
        }

        // Amount validation
        if (isset($ruleData['min_amount']) && isset($ruleData['max_amount'])) {
            if ($ruleData['min_amount'] >= $ruleData['max_amount']) {
                $errors[] = 'Minimum amount must be less than maximum amount';
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Create a new tax rule
     */
    public function createTaxRule(array $ruleData): TaxRule
    {
        $validation = $this->validateTaxRule($ruleData);
        
        if (!$validation['valid']) {
            throw new \InvalidArgumentException('Invalid tax rule data: ' . implode(', ', $validation['errors']));
        }

        // If this is set as default, remove default from others
        if (!empty($ruleData['is_default'])) {
            TaxRule::where('is_default', true)->update(['is_default' => false]);
        }

        return TaxRule::create($ruleData);
    }

    /**
     * Update an existing tax rule
     */
    public function updateTaxRule(TaxRule $rule, array $ruleData): TaxRule
    {
        $validation = $this->validateTaxRule($ruleData);
        
        if (!$validation['valid']) {
            throw new \InvalidArgumentException('Invalid tax rule data: ' . implode(', ', $validation['errors']));
        }

        // If this is set as default, remove default from others
        if (!empty($ruleData['is_default']) && !$rule->is_default) {
            TaxRule::where('is_default', true)->update(['is_default' => false]);
        }

        $rule->update($ruleData);
        return $rule;
    }

    /**
     * Calculate tax for multiple amounts (bulk calculation)
     */
    public function calculateBulkTax(array $amounts, string $currencyCode): array
    {
        $results = [];
        
        foreach ($amounts as $key => $amount) {
            $results[$key] = $this->calculateTax($amount, $currencyCode);
        }
        
        return $results;
    }

    /**
     * Get tax exemption status for amount
     */
    public function isExempt(int $amount, string $currencyCode): bool
    {
        $applicableRules = $this->getApplicableRules($amount, $currencyCode);
        return $applicableRules->isEmpty();
    }

    /**
     * Format tax amount with currency
     */
    public function formatTaxAmount(int $taxAmount, string $currencyCode): string
    {
        // This would ideally use the CurrencyService
        // For now, simple formatting based on currency
        if ($currencyCode === 'IRT') {
            return number_format($taxAmount) . ' ریال';
        }
        
        return number_format($taxAmount / 100, 2) . ' ' . $currencyCode;
    }

    /**
     * Get tax statistics
     */
    public function getTaxStatistics(): array
    {
        $rules = TaxRule::active()->get();
        
        return [
            'total_active_rules' => $rules->count(),
            'percentage_rules' => $rules->where('type', TaxRule::TYPE_PERCENTAGE)->count(),
            'fixed_rules' => $rules->where('type', TaxRule::TYPE_FIXED)->count(),
            'default_rule' => $rules->where('is_default', true)->first()?->name,
            'average_percentage_rate' => $rules->where('type', TaxRule::TYPE_PERCENTAGE)->avg('rate'),
            'rules_by_currency' => $this->getRulesByCurrency($rules),
        ];
    }

    /**
     * Group rules by applicable currencies
     */
    protected function getRulesByCurrency(Collection $rules): array
    {
        $byCurrency = [];
        
        foreach ($rules as $rule) {
            if (empty($rule->applicable_currencies)) {
                $byCurrency['all'][] = $rule->name;
            } else {
                foreach ($rule->applicable_currencies as $currency) {
                    $byCurrency[$currency][] = $rule->name;
                }
            }
        }
        
        return $byCurrency;
    }
} 