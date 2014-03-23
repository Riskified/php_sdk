<?php
error_reporting(E_STRICT);

/**
 * Copyright 2013-2014 Riskified.com, Inc. or its affiliates. All Rights Reserved.
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

function __autoload($class) {
	if (strpos($class, 'Riskified\\') == 0) {
        $parts = explode('\\', $class);
        $file = __DIR__.'/../../'.join('/',$parts).'.php';
        if (is_file($file))
            require_once $file;
	}
}