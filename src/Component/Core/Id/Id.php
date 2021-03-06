<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2018 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace OAuth2Framework\Component\Core\Id;

abstract class  Id implements \JsonSerializable
{
    /**
     * @var string
     */
    private $value;

    /**
     * TokenId constructor.
     *
     * @param $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return $this->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->__toString();
    }
}
