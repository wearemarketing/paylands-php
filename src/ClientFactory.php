<?php

namespace WAM\Paylands;

use Http\Client\Common\Plugin\AuthenticationPlugin;
use Http\Client\Common\Plugin\BaseUriPlugin;
use Http\Client\Common\Plugin\ContentTypePlugin;
use Http\Client\Common\Plugin\ErrorPlugin;
use Http\Client\Common\PluginClient;
use Http\Client\HttpClient;
use Http\Message\Authentication\BasicAuth;
use Http\Message\UriFactory;

/**
 * Class ClientFactory.
 *
 * @author Santi Garcia <sgarcia@wearemarketing.com>, <sangarbe@gmail.com>
 */
class ClientFactory
{
    /**
     * @var string
     */
    private $api_key;

    /**
     * @var string
     */
    private $api_url;

    /**
     * @var bool
     */
    private $sandbox;

    /**
     * @var RequestFactory
     */
    private $apiRequestFactory;

    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var UriFactory;
     */
    protected $uriFactory;

    /**
     * @var DiscoveryProxy
     */
    private $apiDiscoveryProxy;

    /**
     * ClientFactory constructor.
     *
     * @param RequestFactory $apiRequestFactory
     * @param DiscoveryProxy $apiDiscoveryProxy
     * @param string            $api_key
     * @param string            $api_url
     * @param bool              $sandbox
     */
    public function __construct(
        RequestFactory $apiRequestFactory,
        DiscoveryProxy $apiDiscoveryProxy,
        $api_key,
        $api_url,
        $sandbox)
    {
        $this->apiRequestFactory = $apiRequestFactory;
        $this->apiDiscoveryProxy = $apiDiscoveryProxy;
        $this->api_key = $api_key;
        $this->api_url = $api_url;
        $this->sandbox = $sandbox;
    }

    /**
     * @param HttpClient $httpClient
     *
     * @return ClientFactory
     */
    public function setHttpClient(HttpClient $httpClient = null)
    {
        if (!$httpClient) {
            $httpClient = $this->apiDiscoveryProxy->discoverHttpClient();
        }

        $this->httpClient = $httpClient;

        return $this;
    }

    /**
     * @param UriFactory $uriFactory
     *
     * @return ClientFactory
     */
    public function setUriFactory(UriFactory  $uriFactory = null)
    {
        if (!$uriFactory) {
            $uriFactory = $this->apiDiscoveryProxy->discoverUriFactory();
        }

        $this->uriFactory = $uriFactory;

        return $this;
    }

    /**
     * Creates the API client with needed configuration.
     *
     * @return ClientInterface
     */
    public function create()
    {
        $pluginClient = new PluginClient($this->httpClient, [
            new BaseUriPlugin($this->uriFactory->createUri($this->api_url), ['replace' => true]),
            new AuthenticationPlugin(new BasicAuth($this->api_key, '')),
            new ErrorPlugin(),
            new ContentTypePlugin(),
        ]);

        return new Client($pluginClient, $this->apiRequestFactory, (bool) $this->sandbox);
    }
}
