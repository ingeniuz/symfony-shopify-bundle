<?php

namespace CodeCloud\Bundle\ShopifyBundle\Security;

use CodeCloud\Bundle\ShopifyBundle\EventListener\SessionAuthenticationListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use CodeCloud\Bundle\ShopifyBundle\Security\HmacSignature;


/**
 * Authenticates users via session parameter.
 */
class SessionAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var HmacSignature
     */
    private $hmacSignature;

    /**
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, HmacSignature $hmacSignature)
    {
        $this->urlGenerator = $urlGenerator;
        $this->hmacSignature = $hmacSignature;
    }

    /**
     * @see https://symfony.com/blog/new-in-symfony-3-4-guard-authentication-improvements
     */
    public function supports(Request $request)
    {
        return true;
    }

    public function getCredentials(Request $request)
    {
        if (!$session = $request->getSession()) {
            return false;
        }

        if (!$session->has(SessionAuthenticationListener::SESSION_PARAMETER)) {
            
            $storeName = $request->get('shop');
            $hmac      = $request->get('hmac');
            if ($storeName && $hmac) {
                if (!$this->hmacSignature->isValid($hmac, $request->query->all())) {
                    return false;
                } else {
                    $session->set(SessionAuthenticationListener::SESSION_PARAMETER, $storeName);
                }
            } else 
                return false;
        }

        return [
            'shop' => $session->get(SessionAuthenticationListener::SESSION_PARAMETER),
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $userProvider->loadUserByUsername(($credentials && isset($credentials['shop']))? $credentials['shop']:null);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
    }

    public function supportsRememberMe()
    {
        return false;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        if (!$request->get('shop')) {
            return new Response('Your session has expired. Please access the app via Shopify Admin again.');
        }

        return new RedirectResponse(
            $this->urlGenerator->generate('codecloud_shopify_auth', [
                'shop' => $request->get('shop'),
            ])
        );
    }
}
