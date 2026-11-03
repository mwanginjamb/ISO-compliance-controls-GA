<?php

namespace common\Library;

use yii\filters\auth\AuthMethod;
use yii\web\BadRequestHttpException;

/**
 * Authenticates API requests made by the web UI using the logged-in session.
 *
 * Session cookies are sent automatically by the browser, so unsafe requests
 * must also carry a valid CSRF token (X-CSRF-Token header or the CSRF body param).
 * Requests without a session fall through to the next method in CompositeAuth
 * (e.g. HttpBearerAuth for external clients).
 */
class SessionAuth extends AuthMethod
{
    public function authenticate($user, $request, $response)
    {
        if ($user->getIsGuest()) {
            return null;
        }

        if (!$request->validateCsrfToken()) {
            throw new BadRequestHttpException('Unable to verify your data submission.');
        }

        return $user->getIdentity();
    }
}
