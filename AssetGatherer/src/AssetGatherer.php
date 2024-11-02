<?php

namespace AssetGatherer;

use Symfony\Component\Yaml\Yaml;
use Exception;

class AssetGatherer
{
    private $bundles = [];
    private $gatheredAssets = [];

    public function loadConfiguration(string $yamlFilePath): void
    {
        if (!file_exists($yamlFilePath)) {
            throw new Exception("Configuration file not found: $yamlFilePath");
        }

        $this->bundles = Yaml::parseFile($yamlFilePath);
    }

    public function gatherAssetsForRequest(array $request): void
    {
        foreach ($this->bundles as $bundleName => $config) {
            if ($this->bundleMatchesRequest($config['rules'] ?? [], $request)) {
                $this->gatheredAssets[$bundleName] = [];

                foreach ($config as $type => $typeConfig) {
                    if ($type !== 'rules') {
                        foreach ($typeConfig['directories'] as $directory) {
                            if (is_dir($directory)) {
                                $this->scanDirectory($directory, $typeConfig['extensions'], $bundleName, $type);
                            }
                        }
                    }
                }
            }
        }
    }

    private function bundleMatchesRequest(array $rules, array $request): bool
    {
        foreach ($rules as $ruleType => $ruleValue) {
            switch ($ruleType) {
                case 'pathContains':
                    if (strpos($request['path'] ?? '', $ruleValue) === false) {
                        return false;
                    }
                    break;
                case 'header':
                    foreach ($ruleValue as $headerKey => $headerValue) {
                        if (($request['headers'][$headerKey] ?? '') !== $headerValue) {
                            return false;
                        }
                    }
                    break;
                case 'query':
                    foreach ($ruleValue as $queryKey => $queryValue) {
                        if (($request['query'][$queryKey] ?? '') !== $queryValue) {
                            return false;
                        }
                    }
                    break;
                default:
                    return false;
            }
        }
        return true;
    }

    private function scanDirectory(string $directory, array $extensions, string $bundleName, string $type): void
    {
        $directoryIterator = new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS);
        $iterator = new \RecursiveIteratorIterator($directoryIterator);

        foreach ($iterator as $file) {
            if (in_array($file->getExtension(), $extensions, true)) {
                if (!isset($this->gatheredAssets[$bundleName][$type])) {
                    $this->gatheredAssets[$bundleName][$type] = [];
                }
                $this->gatheredAssets[$bundleName][$type][] = $file->getPathname();
            }
        }
    }

    public function getAssets(string $bundleName = null): array
    {
        if ($bundleName) {
            return $this->gatheredAssets[$bundleName] ?? [];
        }
        return $this->gatheredAssets;
    }
}
