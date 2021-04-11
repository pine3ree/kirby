<?php

namespace Kirby\Cms;

class PageTestForceLocked extends Page
{
    public function isLocked(): bool
    {
        return true;
    }
}


class PageForPanelTest extends TestCase
{
    public function testDragText()
    {
        $page = new Page([
            'slug' => 'test'
        ]);

        $panel = $page->panel();
        $this->assertEquals('(link: test text: test)', $panel->dragText());
    }

    public function testDragTextMarkdown()
    {
        $app = new App([
            'roots' => [
                'index' => '/dev/null'
            ],
            'options' => [
                'panel' => [
                    'kirbytext' => false
                ]
            ],
            'site' => [
                'children' => [
                    [
                        'slug' => 'test'
                    ]
                ]
            ]
        ]);

        $panel = $app->page('test')->panel();
        $this->assertEquals('[test](/test)', $panel->dragText());
    }

    public function testDragTextWithTitle()
    {
        $page = new Page([
            'slug' => 'test',
            'content' => [
                'title' => 'Test Title'
            ]
        ]);

        $panel = $page->panel();
        $this->assertEquals('(link: test text: Test Title)', $panel->dragText());
    }

    public function testDragTextWithTitleMarkdown()
    {
        $app = new App([
            'roots' => [
                'index' => '/dev/null'
            ],
            'options' => [
                'panel' => [
                    'kirbytext' => false
                ]
            ],
            'site' => [
                'children' => [
                    [
                        'slug' => 'test',
                        'content' => [
                            'title' => 'Test Title'
                        ]
                    ]
                ]
            ]
        ]);

        $panel = $app->page('test')->panel();
        $this->assertEquals('[Test Title](/test)', $panel->dragText());
    }


    public function testDragTextCustomMarkdown()
    {
        $app = new App([
            'roots' => [
                'index' => '/dev/null'
            ],

            'options' => [
                'panel' => [
                    'kirbytext' => false,
                    'markdown' => [
                        'pageDragText' => function (\Kirby\Cms\Page $page) {
                            return sprintf('Links sind toll: %s', $page->url());
                        },
                    ]
                ]
            ],

            'site' => [
                'children' => [
                    [
                        'slug' => 'test',
                        'content' => [
                            'title' => 'Test Title'
                        ]
                    ]
                ]
            ]
        ]);

        $panel = $app->page('test')->panel();
        $this->assertEquals('Links sind toll: /test', $panel->dragText());
    }

    public function testDragTextCustomKirbytext()
    {
        $app = new App([
            'roots' => [
                'index' => '/dev/null'
            ],

            'options' => [
                'panel' => [
                    'kirbytext' => [
                        'pageDragText' => function (\Kirby\Cms\Page $page) {
                            return sprintf('Links sind toll: %s', $page->url());
                        },
                    ]
                ]
            ],

            'site' => [
                'children' => [
                    [
                        'slug' => 'test',
                        'content' => [
                            'title' => 'Test Title'
                        ]
                    ]
                ]
            ]
        ]);

        $panel = $app->page('test')->panel();
        $this->assertEquals('Links sind toll: /test', $panel->dragText());
    }

    public function testIconDefault()
    {
        $page = new Page([
            'slug' => 'test'
        ]);

        $icon = $page->panel()->icon();

        $this->assertEquals([
            'type'  => 'page',
            'back'  => 'pattern',
            'ratio' => null,
            'color' => '#c5c9c6'
        ], $icon);
    }

    public function testIconFromBlueprint()
    {
        $page = new Page([
            'slug' => 'test',
            'blueprint' => [
                'name' => 'test',
                'icon' => 'test'
            ]
        ]);

        $icon = $page->panel()->icon();

        $this->assertEquals([
            'type'  => 'test',
            'back'  => 'pattern',
            'ratio' => null,
            'color' => '#c5c9c6'
        ], $icon);
    }

    public function testIconWithRatio()
    {
        $page = new Page([
            'slug' => 'test'
        ]);

        $icon = $page->panel()->icon(['ratio' => '3/2']);

        $this->assertEquals([
            'type'  => 'page',
            'back'  => 'pattern',
            'ratio' => '3/2',
            'color' => '#c5c9c6'
        ], $icon);
    }

    public function testIconWithEmoji()
    {
        $page = new Page([
            'slug' => 'test',
            'blueprint' => [
                'name' => 'test',
                'icon' => $emoji = '❤️'
            ]
        ]);

        $icon = $page->panel()->icon();

        $this->assertEquals($emoji, $icon['type']);
        $this->assertEquals('pattern', $icon['back']);
        $this->assertEquals(null, $icon['ratio']);
    }

    public function testOptions()
    {
        $page = new Page([
            'slug' => 'test',
        ]);

        $page->kirby()->impersonate('kirby');

        $expected = [
            'changeSlug'     => true,
            'changeStatus'   => true,
            'changeTemplate' => false, // no other template available in this scenario
            'changeTitle'    => true,
            'create'         => true,
            'delete'         => true,
            'duplicate'      => true,
            'read'           => true,
            'preview'        => true,
            'sort'           => false, // drafts cannot be sorted
            'update'         => true,
        ];

        $this->assertEquals($expected, $page->panel()->options());
    }

    public function testOptionsWithLockedPage()
    {
        $page = new PageTestForceLocked([
            'slug' => 'test',
        ]);

        $page->kirby()->impersonate('kirby');

        // without override
        $expected = [
            'changeSlug'     => false,
            'changeStatus'   => false,
            'changeTemplate' => false,
            'changeTitle'    => false,
            'create'         => false,
            'delete'         => false,
            'duplicate'      => false,
            'read'           => false,
            'preview'        => false,
            'sort'           => false,
            'update'         => false,
        ];

        $this->assertEquals($expected, $page->panel()->options());

        // with override
        $expected = [
            'changeSlug'     => false,
            'changeStatus'   => false,
            'changeTemplate' => false,
            'changeTitle'    => false,
            'create'         => false,
            'delete'         => false,
            'duplicate'      => false,
            'read'           => false,
            'preview'        => true,
            'sort'           => false,
            'update'         => false,
        ];

        $this->assertEquals($expected, $page->panel()->options(['preview']));
    }

    public function testUrl()
    {
        $app = new App([
            'roots' => [
                'index' => '/dev/null'
            ],
            'urls' => [
                'index' => 'https://getkirby.com'
            ],
            'site' => [
                'children' => [
                    [
                        'slug' => 'mother',
                        'children' => [
                            [
                                'slug' => 'child'
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        $page = $app->page('mother/child');

        $this->assertEquals('https://getkirby.com/panel/pages/mother+child', $page->panel()->url());
        $this->assertEquals('/pages/mother+child', $page->panel()->url(true));
    }
}
