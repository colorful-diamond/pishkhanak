# Wallet Zero Balance Charging Command

## Overview
This Laravel artisan command allows you to charge wallets that have exactly zero balance with a specified amount.

## Command Usage

```bash
php artisan wallet:charge-zero [options]
```

## Options

| Option | Description | Default |
|--------|-------------|---------|
| `--amount=AMOUNT` | Amount to charge in smallest currency unit (100,000 = 1,000 Toman) | 100000 |
| `--dry-run` | Show what would be charged without actually charging | - |
| `--force` | Skip confirmation prompt | - |

## Examples

### 1. Preview what will be charged (recommended first step)
```bash
php artisan wallet:charge-zero --dry-run
```

### 2. Charge with default amount (1,000 Toman)
```bash
php artisan wallet:charge-zero
```

### 3. Charge with custom amount (500 Toman)
```bash
php artisan wallet:charge-zero --amount=50000
```

### 4. Charge without confirmation prompt
```bash
php artisan wallet:charge-zero --force
```

### 5. Custom amount with dry-run
```bash
php artisan wallet:charge-zero --amount=25000 --dry-run
```

## Safety Features

- ✅ **Only charges wallets with exactly 0 balance**
- ✅ **Double-checks balance before charging**
- ✅ **Shows preview table of affected users**
- ✅ **Requires confirmation before charging**
- ✅ **Comprehensive logging for audit trail**
- ✅ **Progress bar for large operations**
- ✅ **Error handling with detailed messages**
- ✅ **Dry-run mode for testing**

## Currency Format
- Amounts are in smallest currency units (like cents)
- 100,000 = 1,000 Toman
- 50,000 = 500 Toman
- 10,000 = 100 Toman

## Logs
All operations are logged to Laravel's default log with:
- User ID and wallet ID
- Amount charged
- Success/failure status
- Timestamp
- Command metadata

## Current Status
Based on your database, there are currently **5 users** with zero balance wallets.