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

namespace OAuth2Framework\Component\AuthorizationEndpoint\ParameterChecker;

use OAuth2Framework\Component\AuthorizationEndpoint\Authorization;
use OAuth2Framework\Component\Core\Exception\OAuth2Exception;

interface ParameterChecker
{
    /**
     * @param Authorization $authorization
     *
     * @throws OAuth2Exception
     *
     * @return Authorization
     */
    public function check(Authorization $authorization): Authorization;
}
