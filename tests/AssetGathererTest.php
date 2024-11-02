<?php

use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use AssetGatherer\AssetGatherer;

class AssetGathererTest extends TestCase
{
    private $assetGatherer;
    private $baseDirectory;

    protected function setUp(): void
    {
        $this->baseDirectory = __DIR__ . '/bundles'; // Définir un répertoire de base pour les tests
        mkdir($this->baseDirectory . '/homepage/images', 0777, true);
        file_put_contents($this->baseDirectory . '/homepage/images/test.jpg', 'image content');

        // Instancier AssetGatherer avec un répertoire de base
        $this->assetGatherer = new AssetGatherer($this->baseDirectory);
        $this->assetGatherer->loadConfiguration(__DIR__ . '/../config/bundles.yaml');
    }

    protected function tearDown(): void
    {
        // Nettoyer les fichiers et dossiers de test créés
        unlink($this->baseDirectory . '/homepage/images/test.jpg');
        rmdir($this->baseDirectory . '/homepage/images');
        rmdir($this->baseDirectory . '/homepage');
    }

    public function testGatherAssetsWithBaseDirectory(): void
    {
        $request = new ServerRequest('GET', '/homepage');
        $this->assetGatherer->gatherAssetsForRequest($request);

        $assets = $this->assetGatherer->getAssets('homepage');
        
        // Vérifier que le fichier est collecté en utilisant le répertoire de base
        $this->assertArrayHasKey('images', $assets);
        $this->assertCount(1, $assets['images']);
        $this->assertStringContainsString('test.jpg', $assets['images'][0]);
    }

    public function testNoAssetsForNonMatchingRequest(): void
    {
        $request = new ServerRequest('GET', '/nonexistent');
        $this->assetGatherer->gatherAssetsForRequest($request);

        $assets = $this->assetGatherer->getAssets();
        $this->assertEmpty($assets);
    }
}
