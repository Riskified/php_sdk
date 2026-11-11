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
 * Class Seller
 * data model of seller in a marketplace
 * @package Riskified\OrderWebhook\Model
 */
class Seller extends AbstractModel {
    protected $_fields = array(
        'customer' => 'object \Customer',
        'correspondence' => 'number optional',
        'price_negotiated' => 'boolean optional',
        'starting_price' => 'float optional'
    );

    /**
     * Sends a numeric starting price as a string, as the API documents
     * @param array $data Serialized fields of this seller
     * @return array Fields to send
     */
    protected function wire_transform($data) {
        return $this->as_strings($data, array('starting_price'));
    }
}
