<?php
namespace CodeCloud\Bundle\ShopifyBundle\Twig\Extension;

use CodeCloud\Bundle\ShopifyBundle\Security\HmacSignature;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;


class ShopifyStore extends AbstractExtension
{
    /**
     * @var HmacSignature
     */
    private $hmac;

    /**
     * @param HmacSignature $hmac
     */
    public function __construct(HmacSignature $hmac)
    {
        $this->hmac = $hmac;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('embedded_link', [$this, 'embeddedLink']),
        ];
    }

    public function embeddedLink($storeName, $uri, $uriParams = [])
    {
        $authParams = $this->hmac->generateParams($storeName);

        return '/embedded/' . $uri . '?' . http_build_query(
            array_merge($authParams, $uriParams)
        );
    }
}
