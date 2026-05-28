<?php

if (!defined('DOKU_INC')) die();

/**
 * Populates JSINFO with edittable configuration
 */
class action_plugin_edittable_jsinfo extends DokuWiki_Action_Plugin
{
    /**
     * Register its handlers with the DokuWiki's event controller
     *
     * @param Doku_Event_Handler $controller
     */
    public function register(Doku_Event_Handler $controller)
    {
        $controller->register_hook('DOKUWIKI_STARTED', 'BEFORE', $this, 'fill_jsinfo');
    }

    /**
     * Add plugin config to JSINFO
     *
     * @param Doku_Event $event
     */
    public function fill_jsinfo(Doku_Event $event)
    {
        global $JSINFO;
        $JSINFO['plugins']['edittable']['default columnwidth'] = $this->getConf('default colwidth');
    }
}
