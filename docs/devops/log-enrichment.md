# Log Enrichment with saas_client_id — PropManage SaaS

> Phase 16 — DevOps & Deployment  
> All log entries should include `saas_client_id` and `tenant_slug` for traceability.

---

## Current Implementation

Laravel's logging is configured in `config/logging.php`. Log enrichment is achieved via a custom log processor that injects tenant context into every log entry.

---

## 1. Add Tenant Context to All Log Entries

The `AppServiceProvider` (or a dedicated `LoggingServiceProvider`) injects the current tenant ID into every log line via a Monolog processor:

```php
// In AppServiceProvider::boot() or a dedicated provider:

use Illuminate\Support\Facades\Log;
use Monolog\LogRecord;

Log::channel('stack')->pushProcessor(function (LogRecord $record): LogRecord {
    $extra = $record->extra;

    if (tenancy()->initialized) {
        $extra['saas_client_id'] = tenant()?->id;
        $extra['tenant_slug']    = tenant()?->slug;
    } else {
        $extra['saas_client_id'] = null;
        $extra['tenant_slug']    = 'central';
    }

    return $record->with(extra: $extra);
});
```

---

## 2. Structured Logging (JSON format for production)

For production log aggregation (e.g., CloudWatch, Datadog, Loki), use JSON format in `config/logging.php`:

```php
'channels' => [
    'stack' => [
        'driver'   => 'stack',
        'channels' => ['json'],
    ],

    'json' => [
        'driver'    => 'single',
        'path'      => storage_path('logs/laravel.log'),
        'formatter' => \Monolog\Formatter\JsonFormatter::class,
        'level'     => env('LOG_LEVEL', 'debug'),
    ],
],
```

With the processor above, every log entry will include:

```json
{
  "message": "Property created",
  "context": {...},
  "level": 200,
  "level_name": "INFO",
  "channel": "stack",
  "extra": {
    "saas_client_id": "f47ac10b-58cc-4372-a567-0e02b2c3d479",
    "tenant_slug": "acme-properties"
  }
}
```

---

## 3. Including saas_client_id in Explicit Log Calls

Always pass tenant context in log calls within tenant-scoped code:

```php
// Good — context included
Log::info('Invoice generated', [
    'invoice_id'     => $invoice->id,
    'saas_client_id' => $invoice->saas_client_id,
    'amount'         => $invoice->amount,
]);

// Avoid — no tenant context
Log::info('Invoice generated');
```

---

## 4. Horizon Job Logs

Horizon workers log failed jobs. Ensure the job includes `saas_client_id` in its metadata:

```php
// In your Job class:
public function tags(): array
{
    return [
        'tenant:' . $this->saasClientId,
        'job:' . static::class,
    ];
}
```

This makes jobs filterable by tenant in the Horizon dashboard.

---

## 5. Querying Logs by Tenant (local dev)

```bash
# Filter logs by tenant
grep '"saas_client_id":"<UUID>"' storage/logs/laravel.log | tail -50

# Count log entries per tenant
cat storage/logs/laravel.log | \
  grep -o '"saas_client_id":"[^"]*"' | \
  sort | uniq -c | sort -rn
```

---

## 6. Slow Query Log (PostgreSQL)

Enable slow query logging on the Citus coordinator:

```sql
-- Log queries taking longer than 500ms
ALTER SYSTEM SET log_min_duration_statement = 500;
SELECT pg_reload_conf();
```

View slow queries:

```bash
docker compose exec citus-coordinator \
  tail -f /var/log/postgresql/postgresql.log | \
  grep "duration:"
```

---

## Notes

- Never log full credential payloads (M-Pesa callbacks, API keys).
- Use `Log::debug()` for verbose tenant context in development, `Log::info()` for business events.
- In production, ship logs to a centralized system (e.g., CloudWatch, Grafana Loki) with `saas_client_id` as an indexed field.
