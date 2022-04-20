<?php

require '../bootstrap.php';

use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\ChainAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\FilesystemTagAwareAdapter;
use Symfony\Component\Cache\Adapter\PhpArrayAdapter;
use Symfony\Contracts\Cache\ItemInterface;

$cache = new FilesystemAdapter();

// The callable will only be executed on a cache miss.
$value = $cache->get('my_cache_key', function (ItemInterface $item) {
    $item->expiresAfter(3600);

    // ... do some HTTP request or heavy computations
    $computedValue = 'filesystem';
    echo_with_eol('we cache stuff');

    return $computedValue;
});

echo_with_eol($value); // 'filesystem'

// ... and to remove the cache key
//$cache->delete('my_cache_key');

$cache = new FilesystemAdapter();

// create a new item by trying to get it from the cache
$productsCount = $cache->getItem('stats.products_count');

// assign a value to the item and save it
$productsCount->set(4711);
$cache->save($productsCount);

// retrieve the cache item
$productsCount = $cache->getItem('stats.products_count');
if (!$productsCount->isHit()) {
    echo_with_eol('miss cache');
}
// retrieve the value stored by the item
$total = $productsCount->get();
echo_with_eol($total);

// remove the cache item
$cache->deleteItem('stats.products_count');


$cache = new ApcuAdapter();

// The callable will only be executed on a cache miss.
$value = $cache->get('my_cache_key', function (ItemInterface $item) {
    $item->expiresAfter(3600);

    // ... do some HTTP request or heavy computations
    $computedValue = 'apcu';
    echo_with_eol('we cache stuff');

    return $computedValue;
});

echo_with_eol($value); // 'apcu'


$cache = new ArrayAdapter();

// The callable will only be executed on a cache miss.
$value = $cache->get('my_cache_key', function (ItemInterface $item) {
    $item->expiresAfter(3600);

    // ... do some HTTP request or heavy computations
    $computedValue = 'array';
    echo_with_eol('we cache stuff');

    return $computedValue;
});

echo_with_eol($value); // 'array'

$value = $cache->get('my_cache_key', function (ItemInterface $item) {
    $item->expiresAfter(3600);

    // ... do some HTTP request or heavy computations
    $computedValue = 'array';
    echo_with_eol('we cache stuff');

    return $computedValue;
});

echo_with_eol($value); // 'array'

$cache = new ChainAdapter([
    new ApcuAdapter(),
    new FilesystemAdapter(),
]);

// prune will proxy the call to FilesystemAdapter while silently skip ApcuAdapter
$cache->prune();


$cache = new FilesystemTagAwareAdapter();
$cache->invalidateTags(['hello']);
$value = $cache->get('my_cache_key', function (ItemInterface $item) {
    $item->expiresAfter(3600);
    $item->tag('hello');

    // ... do some HTTP request or heavy computations
    $computedValue = 'filesystem-tag';
    echo_with_eol('we cache stuff');

    return $computedValue;
});
echo_with_eol($value);


$needsWarmup = true;
// somehow, decide it's time to warm up the cache!
if ($needsWarmup) {
    // some static values
    $values = [
        'stats.products_count' => 4711,
        'stats.users_count' => 1356,
    ];

    $cache = new PhpArrayAdapter(
    // single file where values are cached
        __DIR__ . '/somefile.cache',
        // a backup adapter, if you set values after warm-up
        new FilesystemAdapter()
    );
    $cache->warmUp($values);
}

// ... then, use the cache!
$cacheItem = $cache->getItem('stats.users_count');
echo_with_eol($cacheItem->get());
