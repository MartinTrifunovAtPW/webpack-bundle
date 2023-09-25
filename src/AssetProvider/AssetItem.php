<?php

namespace Maba\Bundle\WebpackBundle\AssetProvider;

/**
 * @api
 */
class AssetItem
{
    /**
     * @var string
     */
    private $resource;

    /**
     * @var string|null
     */
    private $group;

    /**
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @param string $resource
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
    }

    /**
     * @return string|null
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param string|null $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }
}