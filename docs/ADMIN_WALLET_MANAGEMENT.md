# Admin Wallet Management System

## Overview

The **Admin Wallet Management System** provides comprehensive tools for administrators to manage user wallet balances through the Filament admin panel. This system enables secure wallet operations with full audit trails and explanations for all transactions.

## Features

### 🎯 Core Functionality

1. **Wallet Balance Display** - View user wallet balances directly in the user list
2. **Charge Wallet** - Add funds to user wallets with explanations
3. **Deduct from Wallet** - Remove funds from user wallets (with force option)
4. **Transaction History** - Direct access to user wallet transaction history
5. **Audit Trail** - Complete logging of all admin actions
6. **Statistics Dashboard** - Real-time wallet management analytics

---

## User Interface Features

### 1. User List Enhancements

#### **New Wallet Balance Column**
```php
TextColumn::make('balance')
    ->label('موجودی کیف پول')
    ->formatStateUsing(fn (User $record): string => number_format($record->balance) . ' تومان')
    ->badge()
    ->color('success')
    ->copyable()
```

**Benefits:**
- ✅ **Quick Overview** - See all user balances at a glance
- ✅ **Copy Function** - Click to copy balance amounts
- ✅ **Visual Design** - Color-coded badges for easy identification

#### **Action Buttons**
Each user row now includes action buttons for:
- 📈 **Charge Wallet** (Green button with plus icon)
- 📉 **Deduct from Wallet** (Red button with minus icon)
- 📊 **View Wallet History** (Blue button with clock icon)

---

## Wallet Operations

### 1. Charge Wallet 💰

**Purpose:** Add funds to a user's wallet for various reasons

**Form Fields:**
- **Amount** (required): 1,000 - 50,000,000 Toman
- **Description** (required): Explanation for the charge (max 500 chars)
- **Reason Type** (required): Predefined categories

**Reason Types:**
- `admin_charge` - شارژ توسط ادمین
- `support_refund` - بازگشت وجه پشتیبانی  
- `promotional_credit` - اعتبار تبلیغاتی
- `error_correction` - تصحیح خطا
- `bonus` - جایزه و پاداش
- `other` - سایر موارد

**Transaction Metadata:**
```json
{
    "description": "بازگشت وجه برای سرویس ناموفق",
    "reason_type": "support_refund",
    "admin_user_id": 1,
    "admin_user_name": "Admin Name",
    "type": "admin_charge",
    "performed_at": "2024-12-20T10:30:00Z"
}
```

### 2. Deduct from Wallet 💸

**Purpose:** Remove funds from a user's wallet with proper authorization

**Form Fields:**
- **Amount** (required): Minimum 1 Toman
- **Current Balance Display** - Shows user's current balance
- **Description** (required): Explanation for deduction (max 500 chars)
- **Reason Type** (required): Predefined categories
- **Force Deduction** (toggle): Allow negative balance

**Reason Types:**
- `admin_deduction` - کسر توسط ادمین
- `penalty` - جریمه
- `error_correction` - تصحیح خطا
- `chargeback` - برگشت تراکنش
- `fraud_prevention` - پیشگیری از تقلب
- `other` - سایر موارد

**Safety Features:**
- ⚠️ **Balance Check** - Prevents overdraft unless forced
- ✅ **Confirmation Required** - Double confirmation for deductions
- 🔒 **Force Option** - Admin can override balance restrictions

### 3. Transaction History Access 📋

**Direct Link Features:**
- Opens wallet transaction resource in new tab
- Pre-filtered to show only selected user's transactions
- Full transaction details with metadata

---

## Statistics Dashboard 📊

### Widget: WalletManagementStatsWidget

**Real-time Metrics:**
1. **Total Wallet Balance** - Sum of all user wallet balances
2. **Users with Wallets** - Count of active wallet users
3. **Average Balance** - Mean wallet balance per user
4. **Recent Transactions** - 24-hour transaction activity
5. **Admin Operations** - Weekly admin charge/deduction count
6. **Pending Transactions** - Unconfirmed transaction count

**Visual Features:**
- 📈 **7-day Trend Chart** - Wallet balance changes over time
- 🎨 **Color-coded Stats** - Visual status indicators
- ⏰ **Auto-refresh** - Updates every 30 seconds
- 📱 **Responsive Design** - Works on all screen sizes

---

## Security & Audit Trail

### 1. Comprehensive Logging

**Every admin action logs:**
```php
\Log::info('Admin wallet charge', [
    'target_user_id' => $record->id,
    'target_user_name' => $record->name,
    'amount' => $data['amount'],
    'description' => $data['description'],
    'reason_type' => $data['reason_type'],
    'admin_user_id' => Auth::id(),
    'admin_user_name' => Auth::user()->name,
]);
```

### 2. Transaction Metadata

**Complete audit trail in wallet transactions:**
- Admin user information
- Timestamp of operation
- Reason classification
- Force deduction flags
- Original descriptions

### 3. Access Control

**Permission-based access:**
- Only authenticated admin users can access
- Widget visibility controls
- Action-level permissions (future enhancement)

---

## Technical Implementation

### 1. Bavix Wallet Integration

**Deposit Operation:**
```php
$record->deposit($data['amount'], [
    'description' => $data['description'],
    'reason_type' => $data['reason_type'],
    'admin_user_id' => Auth::id(),
    'admin_user_name' => Auth::user()->name,
    'type' => 'admin_charge',
    'performed_at' => now()->toISOString(),
]);
```

**Withdrawal Operations:**
```php
// Normal withdrawal (respects balance)
$record->withdraw($amount, $metadata);

// Force withdrawal (allows negative balance)
$record->forceWithdraw($amount, $metadata);
```

### 2. Error Handling

**Robust error management:**
- Try-catch blocks for all operations
- User-friendly error notifications
- Detailed error logging
- Balance validation before operations

### 3. User Feedback

**Real-time notifications:**
- ✅ Success notifications with amounts
- ⚠️ Warning for insufficient balance
- ❌ Error notifications with details
- 📊 Real-time balance updates

---

## Usage Examples

### Scenario 1: Customer Support Refund

**Situation:** User reports service failure, needs refund

**Steps:**
1. Navigate to Users → Find user
2. Click "شارژ کیف پول" (Charge Wallet)
3. Enter refund amount
4. Select "بازگشت وجه پشتیبانی" (Support Refund)
5. Add description: "Refund for failed IBAN service #12345"
6. Submit

**Result:** 
- User wallet charged immediately
- Support ticket updated
- Audit trail created
- User receives notification

### Scenario 2: Fraud Prevention

**Situation:** Suspicious activity detected, need to freeze funds

**Steps:**
1. Navigate to Users → Find user
2. Click "کسر از کیف پول" (Deduct from Wallet)
3. Enter suspicious amount
4. Select "پیشگیری از تقلب" (Fraud Prevention)
5. Add description: "Suspicious transaction pattern detected"
6. Enable force deduction if needed
7. Confirm operation

**Result:**
- Funds secured/removed
- User account flagged
- Investigation trail created
- Automated alerts triggered

### Scenario 3: Promotional Credit

**Situation:** Marketing campaign offering wallet credits

**Steps:**
1. Navigate to Users → Select eligible users
2. Use bulk operations or individual charges
3. Select "اعتبار تبلیغاتی" (Promotional Credit)
4. Add campaign description
5. Process charges

**Result:**
- Campaign credits distributed
- Marketing analytics updated
- User engagement increased
- ROI tracking enabled

---

## Best Practices

### 1. Operational Guidelines

**Before Making Changes:**
- ✅ Verify user identity
- ✅ Confirm transaction details
- ✅ Use appropriate reason codes
- ✅ Add clear descriptions

**Documentation Standards:**
- 📝 Use specific, actionable descriptions
- 🏷️ Select appropriate reason types
- 📅 Include reference numbers when applicable
- 🔗 Link to support tickets or issues

### 2. Safety Protocols

**For Large Amounts:**
- Get secondary approval
- Document authorization source
- Use detailed descriptions
- Monitor for errors

**For Force Deductions:**
- Only use when absolutely necessary
- Document legal/business justification
- Monitor account status post-operation
- Set up alerts for negative balances

### 3. Monitoring Guidelines

**Daily Reviews:**
- Check pending transactions
- Review admin operations
- Monitor unusual patterns
- Verify balance integrity

**Weekly Analytics:**
- Review widget statistics
- Analyze operation trends
- Check error rates
- Update procedures as needed

---

## Troubleshooting

### Common Issues

**1. Insufficient Balance Error**
```
Error: موجودی کیف پول کافی نیست
Solution: Enable force deduction or reduce amount
```

**2. Transaction Fails**
```
Error: خطا در پردازش تراکنش
Solution: Check logs, verify user account status
```

**3. Widget Not Loading**
```
Error: Statistics not displaying
Solution: Check database connectivity, verify permissions
```

### Error Resolution

**Step-by-step debugging:**
1. Check application logs
2. Verify database connectivity
3. Confirm user permissions
4. Test with smaller amounts
5. Contact technical support

---

## Future Enhancements

### Planned Features

1. **Bulk Operations** - Mass wallet operations for multiple users
2. **Scheduled Operations** - Time-delayed wallet changes
3. **Approval Workflows** - Multi-step approval for large amounts
4. **Advanced Reporting** - Detailed analytics and reports
5. **API Integration** - External system wallet management
6. **Mobile Interface** - Responsive mobile admin interface

### Integration Possibilities

1. **Support Ticket System** - Auto-link wallet operations to tickets
2. **Accounting Software** - Export transactions for bookkeeping
3. **Marketing Automation** - Trigger campaigns based on wallet events
4. **Fraud Detection** - AI-powered suspicious activity alerts
5. **Customer Notifications** - SMS/Email alerts for wallet changes

---

## Summary

The Admin Wallet Management System provides:

### ✅ **Complete Control**
- Full wallet operation capabilities
- Real-time balance management
- Comprehensive audit trails

### ✅ **User-Friendly Interface**
- Intuitive Filament integration
- Clear action buttons
- Helpful form validations

### ✅ **Security & Compliance**
- Complete audit logging
- Permission-based access
- Force operation controls

### ✅ **Analytics & Monitoring**
- Real-time statistics
- Trend analysis
- Performance metrics

This system ensures administrators have all necessary tools to manage user wallets effectively while maintaining security, compliance, and user experience standards.

---

**Need Help?**
- Check logs: `/storage/logs/laravel.log`
- Review transactions: Admin → Wallet Transactions
- Contact support: [Your support channel] 