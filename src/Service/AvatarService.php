<?php

namespace App\Service;

use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AvatarService
{
    private const AVATAR_CACHE_TAG = 'AvatarServiceCache';
    private const HOUR = 3600;

    private HttpClientInterface $client;
    private TagAwareCacheInterface $cache;

    public function __construct(HttpClientInterface $client, TagAwareCacheInterface $cache)
    {
        $this->client   = $client;
        $this->cache    = $cache;
    }

    public function fetchAvatar(?string $username): ?string
    {
        $cacheRef = md5(urlencode(self::AVATAR_CACHE_TAG . '_' . $username));
        return $this->cache->get($cacheRef, function (ItemInterface $item) {
            $item->tag([self::AVATAR_CACHE_TAG]);
            $item->expiresAfter(self::HOUR);
            $seed = time();
            $request = $this->client->request('GET', sprintf('https://avatars.dicebear.com/api/avataaars/%s.svg', $seed));
            return $request->getContent();
        });
    }
}
