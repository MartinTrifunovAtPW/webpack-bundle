<?php

namespace Maba\Bundle\WebpackBundle\Twig;

use Maba\Bundle\WebpackBundle\Service\AssetManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class WebpackExtension extends AbstractExtension
{
    const FUNCTION_NAME = 'webpack_asset';
    const NAMED_ASSET_FUNCTION_NAME = 'webpack_named_asset';

    protected $assetManager;

    public function __construct(AssetManager $assetManager)
    {
        $this->assetManager = $assetManager;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction(self::FUNCTION_NAME, [$this, 'getAssetUrl']),
            new TwigFunction(self::NAMED_ASSET_FUNCTION_NAME, [$this, 'getNamedAssetUrl']),
        ];
    }

    public function getTokenParsers()
    {
        return [
            new WebpackTokenParser(self::FUNCTION_NAME, self::NAMED_ASSET_FUNCTION_NAME),
        ];
    }

    /**
     * @param string $resource Path to resource. Can be begin with alias and be prefixed with loaders
     * @param string|null $type Type of asset. If null, type is guessed by extension
     * @param string|null $group Not used here - only used when parsing twig templates to group assets
     *
     * @return string|null
     */
    public function getAssetUrl($resource, $type = null, $group = null)
    {
        return $this->assetManager->getAssetUrl($resource, $type);
    }

    /**
     * @param string $name
     * @param string $type
     *
     * @return string|null
     */
    public function getNamedAssetUrl($name, $type = null)
    {
        return $this->assetManager->getNamedAssetUrl($name, $type);
    }

    public function getName()
    {
        return 'maba_webpack';
    }
}