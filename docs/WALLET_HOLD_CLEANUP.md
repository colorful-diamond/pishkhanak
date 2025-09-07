# Wallet Hold Transactions Cleanup System

## Overview

This system automatically cleans up abandoned wallet transactions that are left in "hold" state for more than 1 hour. These transactions are created when users start a service but abandon the process before completion.

## How It Works

### Hold Transactions
When users use services through the preview system, the `ConfirmationBasedPaymentService` creates temporary "hold" transactions with:
- **Type**: `service_payment_hold`
- **Status**: `confirmed = false` (unconfirmed)
- **Description**: `نگهداری وجه برای سرویس: [Service Name]`

These transactions temporarily hold funds from the user's wallet while the service is being processed. If the service completes successfully, the transaction is confirmed. If the user abandons the process, these transactions remain unconfirmed.

### Automatic Cleanup
The system automatically cleans up abandoned transactions that:
- Are unconfirmed (`confirmed = false`)
- Have type `service_payment_hold`
- Are older than 1 hour
- Have description containing "نگهداری وجه برای سرویس"

## Manual Commands

### Dry Run (Check what would be cleaned)
```bash
php artisan wallet:cleanup-abandoned-holds --dry-run
```

### Clean up transactions older than 1 hour (default)
```bash
php artisan wallet:cleanup-abandoned-holds
```

### Clean up transactions older than 2 hours
```bash
php artisan wallet:cleanup-abandoned-holds --max-age-hours=2
```

### Force cleanup without confirmation
```bash
php artisan wallet:cleanup-abandoned-holds --no-interaction
```

## Scheduled Execution

The cleanup runs automatically every hour via Laravel's task scheduler:
- **Schedule**: Every hour
- **Log file**: `storage/logs/abandoned-holds-cleanup.log`
- **Overlap protection**: Enabled
- **Background execution**: Yes

## What Happens During Cleanup

1. **Find**: Identifies abandoned hold transactions
2. **Report**: Shows summary of transactions to be cleaned
3. **Confirm**: Asks for confirmation (in manual mode)
4. **Cancel**: Cancels each transaction using `ConfirmationBasedPaymentService::forceCancelTransaction()`
5. **Restore**: Automatically restores funds to user wallets
6. **Log**: Records all actions for audit purposes

## Benefits

- **Prevents locked funds**: Users don't lose money from abandoned transactions
- **Improves UX**: Funds are quickly available for other transactions
- **Reduces confusion**: Eliminates old pending transactions from user history
- **Audit trail**: All cleanup actions are logged

## Monitoring

### Log Files
- **Application logs**: Standard Laravel logs with detailed transaction info
- **Cleanup logs**: `storage/logs/abandoned-holds-cleanup.log`

### Success Indicators
- Transactions are successfully cancelled and removed
- User wallet balances are correctly restored
- No errors in the logs

### Alert Conditions
- High number of abandoned transactions (may indicate UX issues)
- Cleanup failures (check database connectivity)
- Large amounts being cleaned up (verify system is working correctly)

## Configuration

### Timing
The default 1-hour timeout is configured in:
- **Scheduler**: `app/Console/Kernel.php` (scheduler timeout)
- **Command**: Can be overridden with `--max-age-hours` option

### Safety Features
- **Dry run mode**: Test without making changes
- **Confirmation required**: Manual runs ask for confirmation
- **Overlap protection**: Only one cleanup can run at a time
- **Detailed logging**: Full audit trail of all actions

## Troubleshooting

### Command Fails
1. Check database connectivity
2. Verify `ConfirmationBasedPaymentService` is available
3. Check Laravel logs for detailed error messages

### Transactions Not Being Found
1. Verify transaction structure in database
2. Check if transactions have correct `type` and `meta` fields
3. Run with `--dry-run` to see what would be found

### Funds Not Restored
1. Check if `forceCancelTransaction()` method is working correctly
2. Verify wallet balance calculations
3. Check user wallet transaction history

## Example Output

```bash
$ php artisan wallet:cleanup-abandoned-holds --dry-run

🧹 Starting cleanup of abandoned hold transactions older than 1 hour(s)
🔍 DRY RUN MODE - No transactions will actually be cancelled
🎯 Found 5 abandoned hold transactions

+-----+---------+----------------------------------+---------------+-------------------+---------------------+
| ID  | User ID | Service                          | Amount        | Age               | Created             |
+-----+---------+----------------------------------+---------------+-------------------+---------------------+
| 123 | 45      | استعلام رتبه بندی و اعتبارسنجی بانکی | 2,500 تومان   | 2 hours ago       | 2024-01-15 10:30:00 |
| 124 | 67      | تبدیل کارت به شبا                   | 1,000 تومان   | 1 hour ago        | 2024-01-15 11:30:00 |
+-----+---------+----------------------------------+---------------+-------------------+---------------------+

💰 Total amount to be released: 3,500 تومان
🔍 DRY RUN: These transactions would be cancelled, but no action was taken
``` 