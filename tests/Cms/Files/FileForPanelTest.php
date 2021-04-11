<?php

namespace Kirby\Cms;

class FileTestForceLocked extends File
{
    public function isLocked(): bool
    {
        return true;
    }
}

class FileForPanelTest extends TestCase
{
    public function testDragText()
    {
        $page = new Page([
            'slug'  => 'test',
            'files' => [
                [
                    'filename' => 'test.pdf'
                ]
            ]
        ]);

        $panel = $page->file('test.pdf')->panel();
        $this->assertEquals('(file: test.pdf)', $panel->dragText());
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
                        'slug' => 'test',
                        'files' => [
                            [
                                'filename' => 'test.pdf'
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        $panel = $app->page('test')->file('test.pdf')->panel();
        $this->assertEquals('[test.pdf](test.pdf)', $panel->dragText());
    }

    public function testDragTextForImages()
    {
        $page = new Page([
            'slug'  => 'test',
            'files' => [
                [
                    'filename' => 'test.jpg'
                ]
            ]
        ]);

        $panel = $page->file('test.jpg')->panel();
        $this->assertEquals('(image: test.jpg)', $panel->dragText());
    }

    public function testDragTextForImagesMarkdown()
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
                        'files' => [
                            [
                                'filename' => 'test.jpg'
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        $panel = $app->page('test')->file('test.jpg')->panel();
        $this->assertEquals('![](test.jpg)', $panel->dragText());
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
                        'fileDragText' => function (\Kirby\Cms\File $file, string $url) {
                            if ($file->extension() === 'heic') {
                                return sprintf('![](%s)', $url);
                            }

                            return null;
                        },
                    ]
                ]
            ],

            'site' => [
                'children' => [
                    [
                        'slug' => 'test',
                        'files' => [
                            [
                                'filename' => 'test.heic'
                            ],
                            [
                                'filename' => 'test.jpg'
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        // Custom function does not match and returns null, default case
        $panel = $app->page('test')->file('test.jpg')->panel();
        $this->assertEquals('![](test.jpg)', $panel->dragText());

        // Custom function should return image tag for heic
        $panel = $app->page('test')->file('test.heic')->panel();
        $this->assertEquals('![](test.heic)', $panel->dragText());
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
                        'fileDragText' => function (\Kirby\Cms\File $file, string $url) {
                            if ($file->extension() === 'heic') {
                                return sprintf('(image: %s)', $url);
                            }

                            return null;
                        },
                    ]
                ]
            ],

            'site' => [
                'children' => [
                    [
                        'slug' => 'test',
                        'files' => [
                            [
                                'filename' => 'test.heic'
                            ],
                            [
                                'filename' => 'test.jpg'
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        // Custom function does not match and returns null, default case
        $panel = $app->page('test')->file('test.jpg')->panel();
        $this->assertEquals('(image: test.jpg)', $panel->dragText());

        // Custom function should return image tag for heic
        $panel = $app->page('test')->file('test.heic')->panel();
        $this->assertEquals('(image: test.heic)', $panel->dragText());
    }

    public function testIconDefault()
    {
        $file = new File([
            'filename' => 'something.jpg'
        ]);

        $icon = $file->panel()->icon();

        $this->assertEquals([
            'type'  => 'file-image',
            'back'  => 'pattern',
            'color' => '#de935f',
            'ratio' => null
        ], $icon);
    }

    public function testIconWithRatio()
    {
        $file = new File([
            'filename' => 'something.jpg'
        ]);

        $icon = $file->panel()->icon(['ratio' => '3/2']);

        $this->assertEquals([
            'type'  => 'file-image',
            'back'  => 'pattern',
            'color' => '#de935f',
            'ratio' => '3/2'
        ], $icon);
    }

    public function testOptions()
    {
        $file = new File([
            'filename' => 'test.jpg',
        ]);

        $file->kirby()->impersonate('kirby');

        $expected = [
            'changeName' => true,
            'create'     => true,
            'delete'     => true,
            'read'       => true,
            'replace'    => true,
            'update'     => true,
        ];

        $this->assertEquals($expected, $file->panel()->options());
    }

    public function testOptionsWithLockedFile()
    {
        $file = new FileTestForceLocked([
            'filename' => 'test.jpg',
        ]);

        $file->kirby()->impersonate('kirby');

        // without override
        $expected = [
            'changeName' => false,
            'create'     => false,
            'delete'     => false,
            'read'       => false,
            'replace'    => false,
            'update'     => false,
        ];

        $this->assertEquals($expected, $file->panel()->options());

        // with override
        $expected = [
            'changeName' => false,
            'create'     => false,
            'delete'     => true,
            'read'       => false,
            'replace'    => false,
            'update'     => false,
        ];

        $this->assertEquals($expected, $file->panel()->options(['delete']));
    }

    public function testOptionsDefaultReplaceOption()
    {
        $file = new File([
            'filename' => 'test.js',
        ]);
        $file->kirby()->impersonate('kirby');

        $expected = [
            'changeName' => true,
            'create'     => true,
            'delete'     => true,
            'read'       => true,
            'replace'    => false,
            'update'     => true,
        ];

        $this->assertSame($expected, $file->panel()->options());
    }

    public function testOptionsAllowedReplaceOption()
    {
        new App([
            'blueprints' => [
                'files/test' => [
                    'name'   => 'test',
                    'accept' => true
                ]
            ]
        ]);

        $file = new File([
            'filename' => 'test.js',
            'template' => 'test',
        ]);

        $file->kirby()->impersonate('kirby');

        $expected = [
            'changeName' => true,
            'create'     => true,
            'delete'     => true,
            'read'       => true,
            'replace'    => true,
            'update'     => true,
        ];

        $this->assertSame($expected, $file->panel()->options());
    }

    public function testOptionsDisabledReplaceOption()
    {
        new App([
            'blueprints' => [
                'files/test' => [
                    'name'   => 'test',
                    'accept' => [
                        'type' => 'image'
                    ]
                ]
            ]
        ]);

        $file = new File([
            'filename' => 'test.js',
            'template' => 'test',
        ]);

        $file->kirby()->impersonate('kirby');

        $expected = [
            'changeName' => true,
            'create'     => true,
            'delete'     => true,
            'read'       => true,
            'replace'    => false,
            'update'     => true,
        ];

        $this->assertSame($expected, $file->panel()->options());
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
                                'slug' => 'child',
                                'files' => [
                                    ['filename' => 'page-file.jpg'],
                                ]
                            ]
                        ]
                    ]
                ],
                'files' => [
                    ['filename' => 'site-file.jpg']
                ]
            ],
            'users' => [
                [
                    'email' => 'test@getkirby.com',
                    'id'    => 'test',
                    'files' => [
                        ['filename' => 'user-file.jpg']
                    ]
                ]
            ]
        ]);

        // site file
        $file = $app->file('site-file.jpg');

        $this->assertEquals(
            'https://getkirby.com/panel/site/files/site-file.jpg',
            $file->panel()->url()
        );
        $this->assertEquals(
            '/site/files/site-file.jpg',
            $file->panel()->url(true)
        );

        // page file
        $file = $app->file('mother/child/page-file.jpg');

        $this->assertEquals(
            'https://getkirby.com/panel/pages/mother+child/files/page-file.jpg',
            $file->panel()->url()
        );
        $this->assertEquals(
            '/pages/mother+child/files/page-file.jpg',
            $file->panel()->url(true)
        );

        // user file
        $user = $app->user('test@getkirby.com');
        $file = $user->file('user-file.jpg');

        $this->assertEquals(
            'https://getkirby.com/panel/users/test/files/user-file.jpg',
            $file->panel()->url()
        );
        $this->assertEquals(
            '/users/test/files/user-file.jpg',
            $file->panel()->url(true)
        );
    }
}
