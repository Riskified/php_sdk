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
 * Class PaymentType
 * Values of PaymentDetails.payment_type. The field accepts other values too.
 * @package Riskified\OrderWebhook\Constants
 */
final class PaymentType {
    const CARD = 'card';
    const PAYPAL = 'paypal';
    const ACH = 'ach';
    const BANK_TRANSFER = 'bank_transfer';
    const BNPL = 'bnpl';
    const GIFT_CARD = 'gift_card';
    const REWARDS_POINTS = 'rewards_points';
    const STORE_CREDIT = 'store_credit';
    const ALIPAY = 'alipay';
    const AMAZON_PAY = 'amazon_pay';
    const APPLE_PAY = 'apple_pay';
    const GOOGLE_PAY = 'google_pay';
    const SAMSUNG_PAY = 'samsung_pay';
    const WECHAT_PAY = 'wechat_pay';
}
