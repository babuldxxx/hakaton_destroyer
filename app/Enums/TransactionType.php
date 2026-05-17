<?php

namespace App\Enums;

enum TransactionType: string
{
    case PlatformEarning = 'platform_earning';
    case CustomOrder = 'custom_order';
    case LabelShare = 'label_share';
    case Payout = 'payout';
}