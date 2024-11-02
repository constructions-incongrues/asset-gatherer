<?php

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use AssetGatherer\AssetGatherer;

class AssetGathererTest extends TestCase
{
    private $assetGatherer;

    protected function setUp(): void
    {
        $this->assetGatherer = new AssetGatherer();
        $this->assetGatherer->loadConfiguration(__DIR__ . '/../config/bundles.yaml');
    }

    public function testGatherAssetsForHomepageRequest(): void
    {
        $factory = new Psr17Factory();
        $request = new ServerRequest('GET', '/homepage');

        $this->assetGatherer->gatherAssetsForRequest($request);
        $assets = $this->assetGatherer->getAssets('homepage');

        $this->assertArrayHasKey('images', $assets);
    }

    public function testNoAssetsForNonMatchingRequest(): void
    {
        $factory = new Psr17Factory();
        $request = new ServerRequest('GET', '/unknown');

        $this->assetGatherer->gatherAssetsForRequest($request);
        $assets = $this->assetGatherer->getAssets();

        $this->assertEmpty($assets);
    }
}
