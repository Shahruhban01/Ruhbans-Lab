<?php

declare(strict_types=1);

namespace App\Services\Payment\Enums;

final class PaymentStatus
{
    public const PENDING = 'pending';
    public const PROCESSING = 'processing';
    public const SUCCESS = 'success';
    public const FAILED = 'failed';
    public const CANCELLED = 'cancelled';
    public const REFUNDED = 'refunded';
    public const EXPIRED = 'expired';
}
