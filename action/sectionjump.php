<?php
/**
 * Table editor
 *
 * @author     Adrian Lang <lang@cosmocode.de>
 */

if (!defined('DOKU_INC')) die();

/**
 * redirect to the section containing the table
 */
class action_plugin_edittable_sectionjump extends DokuWiki_Action_Plugin
{
    /**
     * Register its handlers with the DokuWiki's event controller
     *
     * @param Doku_Event_Handler $controller
     */
    public function register(Doku_Event_Handler $controller)
    {
        $controller->register_hook('ACTION_SHOW_REDIRECT', 'BEFORE', $this, 'jump_to_section');
    }

    /**
     * Jump after save to the section containing this table
     *
     * @param Doku_Event $event
     */
    public function jump_to_section($event)
    {
        global $INPUT;
        if (!$INPUT->has('edittable_data')) return;

        global $PRE;
        if (preg_match_all('/^\s*={2,}([^=\n]+)/m', $PRE, $match, PREG_SET_ORDER)) {
            $check                   = false; //Byref
            $match                   = array_pop($match);
            $event->data['fragment'] = sectionID($match[1], $check);
        }
    }
}
