# Invoice SMS System - Setup & Usage Guide

## Overview

This system automatically sends SMS notifications to customers when an invoice is created, including:

- Invoice number
- Invoice total amount
- Customer's total due amount till today

## Prerequisites

- SpellSMS API account with valid credentials (already configured in `.env`)
- Database connection for logging SMS

## Installation & Setup

### 1. Run Database Migration

When your database is ready, run:

```bash
php artisan migrate
```

This creates the `sms_logs` table to track all SMS sent.

### 2. Configuration

All credentials are stored in `.env`:

```env
SMS_USERNAME=om_hari
SMS_API_KEY=DE932FD6F0E9C395DCEDECC1158BCAF4
SMS_CAMPAIGN=Default
SMS_ROUTE_ID=SI_Alert
```

### 3. Files Created/Modified

#### New Files:

- `app/Services/SmsService.php` - SpellSMS API integration
- `app/Models/SmsLog.php` - SMS log model
- `app/Helpers/InvoiceSmsHelper.php` - Message templates
- `app/Console/Commands/SendInvoiceSms.php` - Manual SMS sending
- `app/Console/Commands/CheckInvoiceSmsStatus.php` - SMS status checking
- `database/migrations/2026_06_04_111632_create_sms_logs_table.php` - SMS logs table

#### Modified Files:

- `app/Http/Controllers/ItemsalesController.php` - Auto-send SMS on invoice creation
- `app/Models/invoice.php` - Added SMS relationships
- `config/services.php` - Added SMS configuration
- `config/logging.php` - Added SMS logging channel
- `.env` - Added SMS credentials

## How It Works

### Auto-Send Flow

1. User creates an invoice via ItemsalesController
2. Invoice is saved to database
3. Customer's phone number is retrieved and formatted
4. SMS message is created with invoice & due amount details
5. SMS is sent via SpellSMS API
6. Log entry is created in `sms_logs` table
7. Invoice creation completes (SMS doesn't block the process)

### Message Template

```
Namaste [Customer Name], your invoice no [ID] has been created.
Invoice Amount: Rs [Amount].
Your total due till today: Rs [Total Due].
Thank you!
```

## Manual SMS Operations

### Send SMS for a Specific Invoice

```bash
php artisan sms:send-invoice 123
```

Replace `123` with the actual invoice ID.

### Check SMS Status

```bash
# Show last 10 SMS sent
php artisan sms:status

# Show SMS history for specific invoice
php artisan sms:status 123
```

## Viewing SMS Logs

### In Database

```sql
SELECT * FROM sms_logs ORDER BY created_at DESC;
```

### View Logs

- SMS logs: `storage/logs/sms.log`
- General logs: `storage/logs/laravel.log`

## Troubleshooting

### SMS Not Sending?

1. Check database connection: `php artisan migrate` should work
2. Verify phone number format (must have digits only)
3. Check SMS balance in SpellSMS account
4. Review logs: `storage/logs/sms.log`

### Check SMS Balance

Visit: https://spellcpaas.com/dashboard

- View API balance in dashboard
- Ensure `SMS_BALANCE` section shows available credit (4999)

### Debug Mode

Check SMS status for invoice:

```bash
php artisan sms:status 123
```

## SMS Limits

- Maximum message length: 720 characters
- Messages auto-truncate if longer
- Phone numbers must be 10 digits or more
- Nepal numbers auto-formatted with +977 prefix

## Monitoring

- All SMS attempts logged in `sms_logs` table
- Status: pending, sent, failed
- Response stored for debugging
- Retry count tracked for failed attempts

## Future Enhancements

- Scheduled SMS reminders for overdue invoices
- WhatsApp + SMS together
- SMS confirmation receipts
- Customer preference for notification method
- Bulk SMS to multiple customers

## Support

For issues or questions about the SMS system, check:

1. SMS logs: `storage/logs/sms.log`
2. Database: `sms_logs` table
3. SpellSMS API docs: https://spellcpaas.com/developer-api/text-sms
