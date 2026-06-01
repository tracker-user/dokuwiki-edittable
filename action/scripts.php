<?php
/**
 * Table editor – conditional script loading
 *
 * Injects Handsontable and the table-editor scripts only on edit/preview pages,
 * keeping the global JS bundle free of the ~1.1 MB Handsontable payload.
 *
 * @author Andreas Gohr <gohr@cosmocode.de>
 */

if (!defined('DOKU_INC')) die();

/**
 * Adds heavy editor JS to the page head only when editing or previewing
 */
class action_plugin_edittable_scripts extends DokuWiki_Action_Plugin
{
    /**
     * Register its handlers with the DokuWiki's event controller
     *
     * @param Doku_Event_Handler $controller
     */
    public function register(Doku_Event_Handler $controller)
    {
        $controller->register_hook('TPL_METAHEADER_OUTPUT', 'BEFORE', $this, 'addEditorScripts');
    }

    /**
     * Inject editor JS files into the page head on edit/preview actions
     *
     * @param Doku_Event $event
     */
    public function addEditorScripts(Doku_Event $event)
    {
        global $ACT;
        $act = act_clean($ACT);
        if (!in_array($act, ['edit', 'preview'], true)) return;

        $base = DOKU_BASE . 'lib/plugins/edittable/';
        $files = [
            'lib/handsontable.full.js',
            'script/contextmenu.js',
            'script/editor.js',
            'script/newtable.js',
        ];

        foreach ($files as $file) {
            $event->data['script'][] = [
                'type'  => 'text/javascript',
                '_data' => '',
                'src'   => $base . $file,
                'defer' => 'defer',
            ];
        }
    }
}
