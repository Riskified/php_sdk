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
 * Class ProductType
 * Values of LineItem.product_type. The field accepts other values too.
 * @package Riskified\OrderWebhook\Constants
 */
final class ProductType {
    const PHYSICAL = 'physical';
    const DIGITAL = 'digital';
    const EVENT = 'event';
    const TRAVEL = 'travel';
    const RIDE = 'ride';
    const ACCOMMODATION = 'accommodation';
    const REMITTANCE = 'remittance';
    const CRYPTO = 'crypto';
    const GIFTCARD = 'giftcard';
}
