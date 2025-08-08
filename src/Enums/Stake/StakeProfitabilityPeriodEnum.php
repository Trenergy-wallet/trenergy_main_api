<?php

namespace Apd\Trenergy\Enums\Stake;

enum StakeProfitabilityPeriodEnum: int
{
    case week = 7;
    case month = 30;
    case year = 365;
}
