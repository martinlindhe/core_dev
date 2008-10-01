<?php
/**
 * $Id$
 *
 * Renders a table of data in XHTML (.xhtml) format
 *
 * \author Martin Lindhe, 2008 <martin@startwars.org>
 */

//FIXME use functions_xhtml.php internally??

class Render_Table_XHTML extends Render_Table
{
	private $table_class = '';
	private $table_style = '';
	function tableClass($class) $this->table_class = $class;
	function tableStyle($style) $this->table_style = $style;

	private $tr_class = '';
	private $tr_style = '';
	function trClass($class) $this->tr_class = $class;
	function trStyle($style) $this->tr_style = $style;

	private $td_class = '';
	private $td_style = '';
	function tdClass($class) $this->td_class = $class;
	function tdStyle($style) $this->td_style = $style;

	function render()
	{
		$out = '<table'.
			($this->table_class ? ' class="'.$this->table_class.'"' : '').
			($this->table_style ? ' style="'.$this->table_style.'"' : '').
			'>';

		$i = 0;
		foreach ($this->data as $data) {
			if ($i == 0) {
				$out .= '<tr'.
					($this->tr_class ? ' class="'.$this->tr_class.'"' : '').
					($this->tr_style ? ' style="'.$this->tr_style.'"' : '').
					'>';
			}
			$out .= '<td'.
				($this->td_class ? ' class="'.$this->td_class.'"' : '').
				($this->td_style ? ' style="'.$this->td_style.'"' : '').
				'>'.$data.'</td>';

			$i++;
			if ($i == $this->columns) {
				$out .= '</tr>';
				$i = 0;
			}
		}

		$out .= '</table>';
		return $out;
	}
}

?>
