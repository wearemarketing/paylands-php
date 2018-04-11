<?php

namespace WAM\Paylands;

use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Discovery\UriFactoryDiscovery;
use Http\Client\HttpClient;
use Http\Message\MessageFactory;
use Http\Message\UriFactory;

/**
 * Class DiscoveryProxy.
 *
 * @author Santi Garcia <sgarcia@wearemarketing.com>, <sangarbe@gmail.com>
 */
class DiscoveryProxy
{
    /**
     * @return HttpClient
     */
    public function discoverHttpClient()
    {
        return HttpClientDiscovery::find();
    }

    /**
     * @return MessageFactory
     */
    public function discoverRequestFactory()
    {
        return MessageFactoryDiscovery::find();
    }

    /**
     * @return UriFactory
     */
    public function discoverUriFactory()
    {
        return UriFactoryDiscovery::find();
    }
}
