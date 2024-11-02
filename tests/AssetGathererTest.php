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

    public function testGatherAssetsForAdminDashboardWithMultipleQueryParameters(): void
    {
        $request = [
            'path' => '/admin',
            'headers' => [],
            'query' => [
                'admin' => 'true',
                'view' => 'dashboard'
            ]
        ];
    
        $this->assetGatherer->gatherAssetsForRequest($request);
        $assets = $this->assetGatherer->getAssets('admin_dashboard');
    
        // Vérifie que le bundle `admin_dashboard` contient les fichiers JavaScript et CSS
        $this->assertArrayHasKey('javascript', $assets, 'Admin dashboard bundle should contain JavaScript files');
        $this->assertArrayHasKey('css', $assets, 'Admin dashboard bundle should contain CSS files');
    }
    
    public function testAdminDashboardNotLoadedWithoutQueryParameters(): void
    {
        $request = [
            'path' => '/admin',
            'headers' => [],
            'query' => [
                'admin' => 'true'
            ]
        ];
    
        $this->assetGatherer->gatherAssetsForRequest($request);
        $assets = $this->assetGatherer->getAssets('admin_dashboard');
    
        // Vérifie que le bundle `admin_dashboard` n'est pas chargé si un paramètre est manquant
        $this->assertEmpty($assets, 'Admin dashboard bundle should not be loaded if query parameters are missing or incorrect');
    }    
}
