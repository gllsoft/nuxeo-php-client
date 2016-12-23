<?php
/**
 * (C) Copyright 2016 Nuxeo SA (http://nuxeo.com/) and contributors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * Contributors:
 *     Pierre-Gildas MILLON <pgmillon@nuxeo.com>
 */

namespace Nuxeo\Client\Api\Auth;


use Guzzle\Http\Message\RequestInterface;
use Nuxeo\Client\Internals\Spi\Auth\AuthenticationInterceptor;

class PortalSSOAuthentication implements AuthenticationInterceptor {

  const NX_USER = 'NX_USER';
  const NX_TOKEN = 'NX_TOKEN';
  const NX_RD = 'NX_RD';
  const NX_TS = 'NX_TS';

  protected $secret;
  protected $username;

  /**
   * PortalSSOAuthentication constructor.
   * @param $secret
   * @param $username
   */
  public function __construct($secret, $username) {
    $this->secret = $secret;
    $this->username = $username;
  }

  /**
   * @param RequestInterface $request
   */
  public function proceed($request) {
    $timestamp = time() * 1000;
    $random = random_int(0, $timestamp);

    $clearToken = implode(':', array($timestamp, $random, $this->secret, $this->username));
    $hashedToken = hash('md5', $clearToken, true);
    $base64HashedToken = base64_encode($hashedToken);

    $request->addHeaders(array(
      self::NX_TS => $timestamp,
      self::NX_RD => $random,
      self::NX_TOKEN => $base64HashedToken,
      self::NX_USER => $this->username
    ));
  }

}