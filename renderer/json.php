<?php
/**
 * Table Renderer for Table Editor
 *
 * This renderer will use the inverse renderer to create Wiki text for everything inside the table. The table
 * it self is stored in two arrays which then can be outputted as JSON.
 *
 * @author     Andreas Gohr <gohr@cosmocode.de>
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

class renderer_plugin_edittable_json extends renderer_plugin_edittable_inverse {
    /** @var array holds the data cells */
    protected $tdata = [];
    /** @var array holds the cell meta data */
    protected $tmeta = [];

    /** @var array holds the meta data of the current cell */
    protected $tmetacell = [];

    /** @var int current row */
    protected $current_row = -1;

    /** @var int current column */
    protected $current_col = 0;

    /**
     * Returns the whole table data as a JSON string
     *
     * @return string
     */
    public function getDataJSON(): string {
        return json_encode($this->tdata);
    }

    /**
     * Returns cell meta data as a JSON string
     *
     * @return string
     */
    public function getMetaJSON(): string {
        return json_encode($this->tmeta);
    }

    // renderer functions below

    public function table_open($maxcols = null, $numrows = null, $pos = null) {
    }

    public function table_close($pos = null) {
    }

    public function tablerow_open() {
        // move counters
        $this->current_row++;
        $this->current_col = 0;
    }

    public function tablerow_close() {
        ksort($this->tdata[$this->current_row]);
        ksort($this->tmeta[$this->current_row]);
    }

    public function tableheader_open($colspan = 1, $align = null, $rowspan = 1) {
        $this->_tablefield_open('th', $colspan, $align, $rowspan);
    }

    public function tableheader_close() {
        $this->_tablefield_close();
    }

    public function tablecell_open($colspan = 1, $align = null, $rowspan = 1) {
        $this->_tablefield_open('td', $colspan, $align, $rowspan);
    }

    public function tablecell_close() {
        $this->_tablefield_close();
    }

    /**
     * Used for opening THs and TDs
     *
     * @param string $tag
     * @param int $colspan
     * @param string|null $align
     * @param int $rowspan
     */
    protected function _tablefield_open($tag, $colspan, $align, $rowspan) {
        // skip cells that already exist - those are previous (span) cells!
        while(isset($this->tmeta[$this->current_row][$this->current_col])) {
            $this->current_col++;
        }

        // remember these, we use them when closing
        $this->tmetacell = [];
        $this->tmetacell['tag'] = $tag;
        $this->tmetacell['colspan'] = $colspan;
        $this->tmetacell['rowspan'] = $rowspan;
        $this->tmetacell['align'] = $align;

        // empty $doc
        $this->doc = '';
    }

    /**
     * Used for closing THs and TDs
     */
    protected function _tablefield_close() {
        // these have been set to the correct cell already
        $row = $this->current_row;
        $col = $this->current_col;

        $this->tdata[$row][$col] = trim(str_replace("\n", ' ', $this->doc)); // no newlines in table cells!
        $this->tmeta[$row][$col] = $this->tmetacell; // as remembered in the open call

        // now fill up missing span cells
        {
            $rowspan = $this->tmetacell['rowspan'];
            $colspan = $this->tmetacell['colspan'];

            for($c = 1; $c < $colspan; $c++) {
                // hide colspanned cell in same row
                $this->tmeta[$row][$col + $c]['hide'] = true;
                $this->tmeta[$row][$col + $c]['rowspan'] = 1;
                $this->tmeta[$row][$col + $c]['colspan'] = 1;
                $this->tdata[$row][$col + $c] = '';

                // hide colspanned rows below if rowspan is in effect as well
                for($r = 1; $r < $rowspan; $r++) {
                    $this->tmeta[$row + $r][$col + $c]['hide'] = true;
                    $this->tmeta[$row + $r][$col + $c]['rowspan'] = 1;
                    $this->tmeta[$row + $r][$col + $c]['colspan'] = 1;
                    $this->tdata[$row + $r][$col + $c] = '';
                }
            }

            // hide rowspanned columns
            for($r = 1; $r < $rowspan; $r++) {
                $this->tmeta[$row + $r][$col]['hide'] = true;
                $this->tmeta[$row + $r][$col]['rowspan'] = 1;
                $this->tmeta[$row + $r][$col]['colspan'] = 1;
                $this->tdata[$row + $r][$col] = ':::';
            }
        }
    }
}
