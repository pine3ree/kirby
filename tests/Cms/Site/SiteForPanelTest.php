<?php

namespace Kirby\Cms;

class SiteForPanelTest extends TestCase
{
    public function testPath()
    {
        $site = new Site();

        $this->assertSame('site', $site->panel()->path());
    }

    public function testUrl()
    {
        $site = new Site();

        $this->assertSame('/panel/site', $site->panel()->url());
        $this->assertSame('/site', $site->panel()->url(true));
    }
}
