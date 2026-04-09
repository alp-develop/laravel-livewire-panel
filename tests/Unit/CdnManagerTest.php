<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Unit;

use AlpDevelop\LivewirePanel\Cdn\CdnManager;
use PHPUnit\Framework\TestCase;

final class CdnManagerTest extends TestCase
{
    private CdnManager $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = new CdnManager();

        $ref = new \ReflectionClass(CdnManager::class);
        $prop = $ref->getProperty('registered');
        $prop->setAccessible(true);
        $prop->setValue(null, []);
    }

    public function test_register_stores_definition(): void
    {
        CdnManager::register('select2', ['css' => ['select2.css'], 'js' => ['select2.js']]);

        $this->assertArrayHasKey('select2', CdnManager::registered());
        $this->assertSame(['css' => ['select2.css'], 'js' => ['select2.js']], CdnManager::registered()['select2']);
    }

    public function test_registered_returns_empty_initially(): void
    {
        $this->assertSame([], CdnManager::registered());
    }

    public function test_resolve_for_panel_returns_css_and_js(): void
    {
        $config = [
            'cdn' => [
                'charts' => [
                    'css' => ['chart.css'],
                    'js' => ['chart.js'],
                    'routes' => [],
                ],
            ],
        ];

        $result = $this->manager->resolveForPanel($config, '/dashboard');

        $this->assertSame(['chart.css'], $result['css']);
        $this->assertSame(['chart.js'], $result['js']);
    }

    public function test_resolve_for_panel_filters_by_route(): void
    {
        $config = [
            'cdn' => [
                'editor' => [
                    'css' => ['editor.css'],
                    'js' => ['editor.js'],
                    'routes' => ['admin/posts*'],
                ],
            ],
        ];

        $result = $this->manager->resolveForPanel($config, '/admin/dashboard');
        $this->assertSame([], $result['css']);

        $result = $this->manager->resolveForPanel($config, '/admin/posts/create');
        $this->assertSame(['editor.css'], $result['css']);
    }

    public function test_active_aliases_returns_matching(): void
    {
        $config = [
            'cdn' => [
                'select2' => [
                    'routes' => [],
                    'css' => [],
                    'js' => [],
                ],
                'editor' => [
                    'routes' => ['posts*'],
                    'css' => [],
                    'js' => [],
                ],
            ],
        ];

        $active = $this->manager->activeAliases($config, '/dashboard');

        $this->assertContains('select2', $active);
        $this->assertNotContains('editor', $active);
    }

    public function test_active_aliases_with_numeric_keys(): void
    {
        $config = [
            'cdn' => [
                [
                    'routes' => [],
                    'css' => [],
                    'js' => [],
                ],
            ],
        ];

        $active = $this->manager->activeAliases($config, '/');

        $this->assertContains('library', $active);
    }

    public function test_render_css_links_outputs_link_tags(): void
    {
        $config = [
            'cdn' => [
                'lib' => [
                    'css' => ['https://cdn.example.com/lib.css'],
                    'js' => [],
                    'routes' => [],
                ],
            ],
        ];

        $output = $this->manager->renderCssLinks($config, '/');

        $this->assertStringContainsString('<link rel="stylesheet" href="https://cdn.example.com/lib.css">', $output);
    }

    public function test_render_js_scripts_outputs_script_tags(): void
    {
        $config = [
            'cdn' => [
                'lib' => [
                    'css' => [],
                    'js' => ['https://cdn.example.com/lib.js'],
                    'routes' => [],
                ],
            ],
        ];

        $output = $this->manager->renderJsScripts($config, '/');

        $this->assertStringContainsString('<script src="https://cdn.example.com/lib.js"></script>', $output);
    }

    public function test_render_css_links_escapes_html(): void
    {
        $config = [
            'cdn' => [
                'lib' => [
                    'css' => ['https://cdn.example.com/lib.css?a=1&b=2'],
                    'js' => [],
                    'routes' => [],
                ],
            ],
        ];

        $output = $this->manager->renderCssLinks($config, '/');

        $this->assertStringContainsString('&amp;', $output);
    }
}
