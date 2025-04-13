<?php

namespace App\Enums;

enum TransactionDetailTypeEnum: string
{
    case DEPOSIT = 'deposit';
    case WITHDRAW = 'withdraw';
}
