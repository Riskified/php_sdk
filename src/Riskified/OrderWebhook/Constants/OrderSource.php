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
 * Class OrderSource
 * Values of Order.source, which accepts only these.
 * @package Riskified\OrderWebhook\Constants
 */
final class OrderSource {
    const DESKTOP_WEB = 'desktop_web';
    const MOBILE_APP = 'mobile_app';
    const MOBILE_WEB = 'mobile_web';
    const WEB = 'web';
    const CHAT = 'chat';
    const THIRD_PARTY = 'third_party';
    const PHONE = 'phone';
    const IN_STORE = 'in_store';
    const SHOPIFY_DRAFT_ORDER = 'shopify_draft_order';
    const UNKNOWN = 'unknown';
    const MOBILE_APP_ANDROID = 'mobile_app_android';
    const MOBILE_APP_IOS = 'mobile_app_ios';
    const SUBSCRIPTION = 'subscription';
    const AI_AGENT = 'ai_agent';
}
