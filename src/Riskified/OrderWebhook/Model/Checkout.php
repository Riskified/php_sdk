<?php

namespace Riskified\OrderWebhook\Model;

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

/**
 * Class Checkout
 * pre-order checkout data model, includes nested models
 * @package Riskified\OrderWebhook\Model
 */
class Checkout extends Order {
    /**
     * Like Order, but keeps single addresses: post-auth /checkout_denied documents them as objects, not lists
     * @param array $data Serialized fields of this checkout
     * @return array Fields to send
     */
    protected function wire_transform($data) {
        $data = $this->as_lists($data, array('charge_free_payment_details'));
        return $this->authorization_error_in_payment_details($data);
    }

    /**
     * Copies the root authorization_error into each payment detail that has none, where /checkout_denied
     * documents it. The root key is kept.
     * @param array $data Serialized fields of this checkout
     * @return array Fields to send
     */
    private function authorization_error_in_payment_details($data) {
        $payments = isset($data['payment_details']) ? $data['payment_details'] : null;
        if (empty($data['authorization_error']) || empty($payments) || !is_array($payments)) {
            return $data;
        }

        $single = !isset($payments[0]);
        $payments = $single ? array($payments) : $payments;
        foreach ($payments as $i => $payment) {
            if (is_array($payment) && !isset($payment['authorization_error'])) {
                $payments[$i]['authorization_error'] = $data['authorization_error'];
            }
        }
        $data['payment_details'] = $single ? $payments[0] : $payments;
        return $data;
    }
}
