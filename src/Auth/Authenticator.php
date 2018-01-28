<?php
namespace Czim\CmsAuthApi\Auth;

use Czim\CmsAuth\Auth\Authenticator as BaseAuthenticator;

class Authenticator extends BaseAuthenticator
{

    /**
     * The CMS Authenticator version.
     *
     * @var string
     */
    const VERSION = '0.0.3';


    use AuthApiRoutingTrait;

}
