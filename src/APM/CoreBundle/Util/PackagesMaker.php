<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 01/06/2017
 * Time: 16:56
 */

namespace APM\CoreBundle\Util;


use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\UrlPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

class PackagesMaker
{
    private $images_url;
    private $docs_url;
    private $resolve_images_url;

    public function setValues($images_url = null, $docs_url = null, $resolve_images_url = null)
    {
        $this->images_url = $images_url;
        $this->docs_url = $docs_url;
        $this->resolve_images_url = $resolve_images_url;
    }

    public function getPackages()
    {
        $versionStrategy = new EmptyVersionStrategy();
        $defaultPackage = new Package($versionStrategy);
        $namedPackages = array(
            'img' => new PathPackage($this->images_url, $versionStrategy),
            'resolve_img' => new UrlPackage($this->resolve_images_url, $versionStrategy),
            'resolve_img_local' => new PathPackage($this->resolve_images_url, $versionStrategy),
            'doc' => new PathPackage($this->docs_url, $versionStrategy),
        );
        return new Packages($defaultPackage, $namedPackages);
    }
}