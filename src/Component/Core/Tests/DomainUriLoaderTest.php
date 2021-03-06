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

namespace OAuth2Framework\Component\Core\Tests;

use OAuth2Framework\Component\Core\Domain\DomainUriLoader;
use PHPUnit\Framework\TestCase;

/**
 * @group DomainUriLoader
 */
class DomainUriLoaderTest extends TestCase
{
    /**
     * @test
     * @expectedException \League\JsonReference\SchemaLoadingException
     * @expectedExceptionMessage The schema "foo" is not supported.
     */
    public function anAccessTokenCanBeCreatedUsingTheCommand()
    {
        $domainUriLoader = new DomainUriLoader();
        $domainUriLoader->load('foo');
    }
}
