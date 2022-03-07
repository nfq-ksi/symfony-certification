<?php

require '../Bootstrap.php';

use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\UrlPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;
use Symfony\Component\Asset\VersionStrategy\StaticVersionStrategy;
use SymfonyCertification\Asset\VersionStrategy\DateVersionStrategy;

function echo_with_eol(string $string) {
    echo $string . '</br>';
}

$package = new Package(new EmptyVersionStrategy());

// Absolute path
echo_with_eol($package->getUrl('/image.png'));
// result: /image.png

// Relative path
echo_with_eol($package->getUrl('image.png'));
// result: image.png

$package = new Package(new StaticVersionStrategy('v1'));

// Absolute path
echo_with_eol($package->getUrl('/image.png'));
// result: /image.png?v1

// Relative path
echo_with_eol($package->getUrl('image.png'));
// result: image.png?v1


// puts the 'version' word before the version value
$package = new Package(new StaticVersionStrategy('v1', '%s?version=%s'));

echo_with_eol($package->getUrl('/image.png'));
// result: /image.png?version=v1

// puts the asset version before its path
$package = new Package(new StaticVersionStrategy('v1', '%2$s/%1$s'));

echo_with_eol($package->getUrl('/image.png'));
// result: /v1/image.png

echo_with_eol($package->getUrl('image.png'));
// result: v1/image.png


$package = new Package(new DateVersionStrategy());

echo_with_eol($package->getUrl('image.png'));
// result: v1/image.png?v1=date('Ymd')

$pathPackage = new PathPackage('/static/images', new StaticVersionStrategy('v1'));

echo_with_eol($pathPackage->getUrl('logo.png'));
// result: /static/images/logo.png?v1

// Base path is ignored when using absolute paths
echo_with_eol($pathPackage->getUrl('/logo.png'));
// result: /logo.png?v1


$urlPackage = new UrlPackage(
    'http://static.example.com/images/',
    new StaticVersionStrategy('v1')
);

echo_with_eol($urlPackage->getUrl('/logo.png'));
// result: http://static.example.com/images/logo.png?v1

$urlPackage = new UrlPackage(
    '//static.example.com/images/',
    new StaticVersionStrategy('v1')
);

echo_with_eol($urlPackage->getUrl('/logo.png'));
// result: //static.example.com/images/logo.png?v1

$urls = [
    '//static1.example.com/images/',
    '//static2.example.com/images/',
];
$urlPackage = new UrlPackage($urls, new StaticVersionStrategy('v1'));

echo_with_eol($urlPackage->getUrl('/logo.png'));
// result: http://static2.example.com/images/logo.png?v1
echo_with_eol($urlPackage->getUrl('/icon.png'));
// result: http://static1.example.com/images/icon.png?v1

$versionStrategy = new StaticVersionStrategy('v1');

$defaultPackage = new Package($versionStrategy);

$namedPackages = [
    'img' => new UrlPackage('http://img.example.com/', $versionStrategy),
    'doc' => new PathPackage('/somewhere/deep/for/documents', $versionStrategy),
];

$packages = new Packages($defaultPackage, $namedPackages);


echo_with_eol($packages->getUrl('/main.css'));
// result: /main.css?v1

echo_with_eol($packages->getUrl('/logo.png', 'img'));
// result: http://img.example.com/logo.png?v1

echo_with_eol($packages->getUrl('resume.pdf', 'doc'));
// result: /somewhere/deep/for/documents/resume.pdf?v1


$localPackage = new UrlPackage(
    'file:///path/to/images/',
    new EmptyVersionStrategy()
);

$ftpPackage = new UrlPackage(
    'ftp://example.com/images/',
    new EmptyVersionStrategy()
);

echo_with_eol($localPackage->getUrl('/logo.png'));
// result: file:///path/to/images/logo.png

echo_with_eol($ftpPackage->getUrl('/logo.png'));
// result: ftp://example.com/images/logo.png


// assumes the JSON file above is called "rev-manifest.json"
$package = new Package(new JsonManifestVersionStrategy(__DIR__.'/rev-manifest.json'));

echo_with_eol($package->getUrl('css/app.css'));
// result: build/css/app.b916426ea1d10021f3f17ce8031f93c2.css

echo_with_eol($package->getUrl('css/appb.css'));
// result: build/css/appb.css


// The value of $strictMode can be specific per environment "true" for debugging and "false" for stability.
$strictMode = true;
// assumes the JSON file above is called "rev-manifest.json"
$package = new Package(new JsonManifestVersionStrategy(__DIR__.'/rev-manifest.json', strictMode: $strictMode));

echo $package->getUrl('not-found.css');
// error:
