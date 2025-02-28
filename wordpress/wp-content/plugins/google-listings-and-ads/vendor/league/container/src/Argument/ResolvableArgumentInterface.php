<?php

declare(strict_types=1);

namespace Automattic\WooCommerce\GoogleListingsAndAds\Vendor\League\Container\Argument;

interface ResolvableArgumentInterface extends ArgumentInterface
{
    public function getValue(): string;
}
