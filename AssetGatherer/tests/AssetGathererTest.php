<?php

namespace AssetGatherer\Tests;

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

    public function testLoadConfiguration(): void
    {
        $this->assertIsArray($this->assetGatherer->getAssets());
    }

    public function testGatherAssetsForHomepageRequest(): void
    {
        $request = [
            'path' => '/homepage',
            'headers' => [],
            'query' => []
        ];

        $this->assetGatherer->gatherAssetsForRequest($request);
        $assets = $this->assetGatherer->getAssets('homepage');

        $this->assertArrayHasKey('images', $assets);
    }

    public function testNoAssetsForNonMatchingRequest(): void
    {
        $request = [
            'path' => '/unknown',
            'headers' => [],
            'query' => []
        ];

        $this->assetGatherer->gatherAssetsForRequest($request);
        $assets = $this->assetGatherer->getAssets();

        $this->assertEmpty($assets);
    }
}
