<?php

namespace Config;

use App\Bundles\GlobalBundle;
use App\Bundles\TinyMCEBundle;
use Tatter\Assets\Config\Assets as AssetsConfig;
use Tatter\Files\Bundles\DropzoneBundle;
use Tatter\Frontend\Bundles\BootstrapBundle;
use Tatter\Frontend\Bundles\DataTablesBundle;
use Tatter\Frontend\Bundles\FontAwesomeBundle;

class Assets extends AssetsConfig
{
    //--------------------------------------------------------------------
    // Route Assets
    //--------------------------------------------------------------------

    /**
     * Assets to apply to each route. Routes may use * as a wildcard to
     * allow any valid character, similar to URL Helper's url_is().
     * Keys are routes; values are an array of any of the following:
     *   - Bundle class names
     *   - File paths (relative to $directory)
     *   - URLs
     *
     * Example:
     *     $routes = [
     *         '*' => [
     *             'https://pagecdn.io/lib/cleave/1.6.0/cleave.min.js',
     *             \App\Bundles\Bootstrap::class,
     *          ],
     *         'admin/*' => [
     *             \Tatter\Frontend\Bundles\AdminLTE::class,
     *             'admin/login.js',
     *         ],
     *     ];
     *
     * @var array<string,string[]>
     */
    public array $routes = [
        '*' => [
            BootstrapBundle::class,
            FontAwesomeBundle::class,
            GlobalBundle::class,
        ],
        'jobs/files*' => [
            DropzoneBundle::class,
            'dropzone.js', // Custom Dropzone config
        ],
        'files*' => [
            DropzoneBundle::class,
            'dropzone.js', // Custom Dropzone config
        ],
        // Note: AdminLTE bundle is loaded directly on the layout
        'manage*' => [
            DataTablesBundle::class,
        ],
        'manage/content*' => [
            TinyMCEBundle::class,
        ],
        'manage/materials*' => [
            TinyMCEBundle::class,
        ],
        'manage/methods*' => [
            TinyMCEBundle::class,
        ],
    ];
}
