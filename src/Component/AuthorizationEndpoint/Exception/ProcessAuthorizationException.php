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

namespace OAuth2Framework\Component\AuthorizationEndpoint\Exception;

use OAuth2Framework\Component\AuthorizationEndpoint\Authorization;

class ProcessAuthorizationException extends \Exception
{
    /**
     * @var Authorization
     */
    private $authorization;

    /**
     * CreateRedirectionException constructor.
     *
     * @param Authorization $authorization
     */
    public function __construct(Authorization $authorization)
    {
        parent::__construct();
        $this->authorization = $authorization;
    }

    /**
     * @return Authorization
     */
    public function getAuthorization(): Authorization
    {
        return $this->authorization;
    }
}
