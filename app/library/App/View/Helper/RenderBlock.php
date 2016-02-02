<?php
/**
 * View Helper that allows a current view to pull a template from the common templates directory.
 */

namespace App\View\Helper;
class RenderBlock extends HelperAbstract
{
    public function renderBlock($block_name, $vars = array(), $title = FALSE)
    {
        return \Entity\Block::render($block_name, $vars, $title);
    }
}