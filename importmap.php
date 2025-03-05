<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    '@symfony/stimulus-bundle' => [
        'path' => '@symfony/stimulus-bundle/loader.js',
    ],
    'Lucide' => [
        'path' => './assets/lib/Lucide.js',
    ],
    'bootstrap' => [
        'version' => '5.3.3',
    ],
    '@popperjs/core' => [
        'version' => '2.11.8',
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    'path-to-regexp' => [
        'version' => '8.2.0',
    ],
    '@kurkle/color' => [
        'version' => '0.3.4',
    ],
    'bootstrap/dist/css/bootstrap.min.css' => [
        'version' => '5.3.3',
        'type' => 'css',
    ],
    'bootstrap/dist/js/bootstrap.min.js' => [
        'version' => '5.3.3',
    ],
    'bootstrap/dist/js/bootstrap.bundle.min.js' => [
        'version' => '5.3.3',
    ],
    '@fortawesome/fontawesome-free' => [
        'version' => '6.7.2',
    ],
    '@fortawesome/free-solid-svg-icons' => [
        'version' => '6.7.2',
    ],
    '@fortawesome/free-regular-svg-icons' => [
        'version' => '6.7.2',
    ],
    '@fortawesome/fontawesome-free/css/fontawesome.min.css' => [
        'version' => '6.7.2',
        'type' => 'css',
    ],
    'jquery' => [
        'version' => '3.7.1',
    ],
    '@symfony/stimulus-bridge' => [
        'version' => '4.0.0',
    ],
    'axios' => [
        'version' => '1.7.9',
    ],
    'umbrellajs' => [
        'version' => '3.3.3',
    ],
    '@stimulus-components/notification' => [
        'version' => '3.0.0',
    ],
    'stimulus-use' => [
        'version' => '0.52.3',
    ],
    'boxicons/dist/boxicons.js' => [
        'version' => '2.1.4',
    ],
    'lucide' => [
        'version' => '0.475.0',
    ],
    'notiflix' => [
        'version' => '3.2.8',
    ],
];
