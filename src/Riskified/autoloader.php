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

function riskifiedAutoload($class) {
    $parts = explode('\\', $class);
    if (array_shift($parts) == 'Riskified') {
        array_unshift($parts, __DIR__);
        $file = join('/', $parts) . '.php';
        if (is_file($file)) {
            require_once $file;
        }
    }
    return true;
}

// Register Riskified autoloader into the SPL autoloading stack (in order to support multiple autoloaders).
//
// $do_throw must be true: PHP 8 ignores the argument and always throws, and passing false there
// emits a notice on every request. Under display_errors that notice prints the SDK's absolute
// install path into the response body. true is also the PHP 7 behaviour you want - a failed
// registration should be loud - so this stays valid on the 7.0 floor phpcs enforces.
spl_autoload_register('riskifiedAutoload', true, true);
