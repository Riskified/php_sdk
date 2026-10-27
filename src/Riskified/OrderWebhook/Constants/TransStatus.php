<?php

/**
 * Copyright 2013-2026 Riskified.com, Inc. or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 * http://www.apache.org/licenses/LICENSE-2.0.html
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */

namespace Riskified\OrderWebhook\Constants;

/**
 * Class TransStatus
 * Values of AuthenticationResult.trans_status, as defined by 3-D Secure 2.
 * @package Riskified\OrderWebhook\Constants
 */
final class TransStatus {
    const AUTHENTICATED = 'Y';
    const NOT_AUTHENTICATED = 'N';
    const UNAVAILABLE = 'U';
    const ATTEMPTED = 'A';
    const CHALLENGE_REQUIRED = 'C';
    const DECOUPLED = 'D';
    const REJECTED = 'R';
    const INFORMATIONAL = 'I';
    const CHALLENGE_SPC = 'S';
}
