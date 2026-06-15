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

/*
 * Fallback polyfills for functions used by this SDK that are not available on
 * the lowest supported PHP version (5.5). When the SDK is installed via Composer
 * these are already provided by symfony/polyfill-php56; this file additionally
 * covers consumers that bootstrap the SDK through its bundled autoloader.php.
 *
 * Every definition is guarded by function_exists() so it is a no-op whenever the
 * native function (or another polyfill) is already present.
 */

if (!function_exists('hash_equals')) {
    /**
     * Timing attack safe string comparison (PHP 5.6+ native).
     *
     * @param string $known_string the string of known length to compare against
     * @param string $user_string the user-supplied string
     * @return bool true when the two strings are equal, false otherwise
     */
    function hash_equals($known_string, $user_string) {
        $known_len = strlen($known_string);
        if ($known_len !== strlen($user_string)) {
            return false;
        }

        $result = 0;
        for ($i = 0; $i < $known_len; $i++) {
            $result |= ord($known_string[$i]) ^ ord($user_string[$i]);
        }

        return 0 === $result;
    }
}
