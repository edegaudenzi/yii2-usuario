<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\AuthClient;

use Da\User\Contracts\AuthClientInterface;
use Da\User\Traits\AuthClientUserIdTrait;
use yii\authclient\OAuth2;

/**
 * Xero allows authentication via Xero OAuth2 flow.
 * Before using Xero OAuth2 you must register your Xero Application
 * @see https://developer.xero.com/
 *
 * Note: the registered App must have the following:
 * -Authentication: 'Redirect URIs' set 'user/security/auth?authclient=xero' as an absolute URL
 *  e.g. https://domain.com/index.php/user/security/auth?authclient=xero
 *
 * Example application configuration:
 *
 * ```
 * 'components' => [
 *     ...
 *     'authClientCollection' => [
 *         'class' => 'yii\authclient\Collection',
 *         'clients' => [
 *             'xero' => [
 *                 'class' => 'yii\authclient\clients\Xero',
 *                 'clientId' => '15ED1343598243FE666D5276CD42F825',
 *                 'clientSecret' => '8P_WI03t9VRlcWSpjFJc89PhpyAkkD5mgl4tkhfCHt_qwK0d',
 *             ],
 *         ],
 *     ]
 *     ...
 * ]
 * ```
 */
class Xero extends OAuth2 implements AuthClientInterface
{
    use AuthClientUserIdTrait;

    public $apiBaseUrl = 'https://identity.xero.com/connect';
    public $authUrl    = 'https://login.xero.com/identity/connect/authorize';
    public $scope      = 'email openid profile';
    public $tokenUrl   = 'https://identity.xero.com/connect/token';

    /**
     * {@inheritdoc}
     */
    public function applyAccessTokenToRequest($request, $accessToken)
    {
        $request->headers->set('Authorization', 'Bearer '.$accessToken->getToken());
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultName()
    {
        return 'xero';
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultTitle()
    {
        return 'Xero';
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail()
    {
        return $this->getUserAttributes()['email'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserId()
    {
        return $this->getUserAttributes()['xero_userid'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->getUserAttributes()['preferred_username'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    protected function initUserAttributes()
    {
        return $this->api('userinfo', 'GET');
    }
}
