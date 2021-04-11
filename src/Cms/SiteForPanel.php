<?php

namespace Kirby\Cms;

/**
 * Provides information about the site for the Panel
 * @since 3.6.0
 *
 * @package   Kirby Cms
 * @author    Nico Hoffmann <nico@getkirby.com>
 * @link      https://getkirby.com
 * @copyright Bastian Allgeier GmbH
 * @license   https://getkirby.com/license
 */
class SiteForPanel extends ModelForPanel
{
    /**
     * Returns the full path without leading slash
     *
     * @return string
     */
    public function path(): string
    {
        return 'site';
    }
}
