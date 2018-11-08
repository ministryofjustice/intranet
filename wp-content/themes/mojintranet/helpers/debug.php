<?php

/**
 * usage:
 * Debug::brief($var) - shows the structure of $variable, doesn't recurse objects
 * Debug::full($var) - shows the structure of $variable, recurses objects
 * Debug::raw($var) - does print_r($var)
 * Debug::pre($var) - does print_r($var) wrapped in <pre></pre>
 */
class Debug {
	private static $styleAttached = false;
	private static $recurseObjects = false;
	private static $maxlevel = false;

  public static function raw($var){
    print_r($var);
  }

  public static function pre($var){
    echo'<pre>';
    print_r($var);
    echo'</pre>';
  }

  /** Doesn't show structure of objects
   */
  public static function brief($var, $maxlevel = 5){
    self::debug_var($var, false, $maxlevel);
  }

  /** Shows everything including objects' structure
   */
  public static function full($var, $maxlevel = 5){
    self::debug_var($var, true, $maxlevel);
  }

	private static function debug_var($var, $recurseObjects = false, $maxlevel = 5){
		self::$recurseObjects = $recurseObjects;
		self::$maxlevel = $maxlevel;
		//get original variable name
		//note: this will not work correctly when this function is called more than once in one line of code
		$backtrace = debug_backtrace();
		$x = $backtrace[1]; //check 1 step behind
		$fname = $x['file'];
		$fline = $x['line'];
		$ffunc = $x['function'];

		$cdata = file($fname);
		$l = $cdata[$fline-1];
		$matches = array();
		preg_match_all("/".$ffunc."\s*\((.*)\s*\)\s*;/", $l, $matches);
		$varname = preg_split("/\s*,\s*/", $matches[1][0]);
		$varname = $varname[0]; //truncate at comma

		$serializedSize = strlen(serialize($var));

		//show data
		if(!self::$styleAttached){
			self::attach_style();
			self::$styleAttached = true;
		}
		echo'<div class="__dv">';
			echo'<div class="__dv_header"><strong>PHP Debug</strong></div>';
			echo'<div class="__dv_header"><span class="__r">'.$varname.'</span> in <span class="__g">'.$fname.'</span> <span class="__b">['.$fline.']</span> <span class="__r">Serialized size:</span> <span class="__b">'.number_format($serializedSize, 0, false, ',').'</span></div>';
			echo'<div>';
			self::get_recursive(array($var));
			echo'</div>';
		echo'</div>';
		echo'<div style="clear: both"></div>';
	}

	private static function indent($level, $text){
		$tab = '<div class="__tab">&nbsp;</div>';
		return '<p>'.str_repeat($tab, $level).$text.'</p>';
	}

	private static function get_recursive($var, $level = 0){
		if($level>self::$maxlevel){
			echo self::indent($level, '...');
			return;
		}
		if(is_object($var)) $var = (array)$var;

		if(is_array($var)){
			self::get_array($var, $level);
		}
		else{
			self::get_var($var, $level);
		}
	}

	private static function get_array($var, $level){
		foreach($var as $x1=>$x2){
			if(!is_numeric($x1)) $x1 = "'".$x1."'";
			if(is_array($x2)){
				echo self::indent($level, (($level>0) ? '<strong>['.$x1.']</strong> : ' : '').'<i>array</i> {');
				self::get_recursive($x2, $level+1);
				echo self::indent($level, '}');
			}
			elseif(is_object($x2) && self::$recurseObjects){
				echo self::indent($level, (($level>0) ? '<strong>['.$x1.']</strong> : ' : '').'<i>object</i> {');
				self::get_recursive($x2, $level+1);
				echo self::indent($level, '}');
			}
			else{
				if(is_bool($x2)){ $t = 'boolean'; $x2 = '<span class="__g">'.(($x2) ? 'true' : 'false').'</span>';}
				elseif(is_integer($x2)){ $t = 'int'; $x2 = '<span class="__r">'.$x2.'</span>';}
				elseif(is_float($x2)){ $t = 'float'; $x2 = '<span class="__r">'.$x2.'</span>';}
				elseif(is_string($x2)){ $t = 'string'; $x2 = '<span class="__r">[</span><span class="__b">'.htmlspecialchars($x2).'</span><span class="__r">]</span>';}
				elseif(is_null($x2)){ $t = 'null'; $x2 = '<span class="__g">NULL</span>';}
        elseif(is_object($x2)){ $t = 'object'; $x2 = '<span class="__g">Object</span>';}
				else{ $t = gettype($x2); $x2 = '???';}
				echo self::indent($level, (($level>0) ? '<strong>['.$x1.'] :</strong> ' : '').'<i>'.$t.'</i> '.$x2);
			}
		}
	}

	private static function get_var($var, $level){
		if(is_bool($var)){ $t = 'boolean'; $var = '<span class="__g">'.(($var) ? 'true' : 'false').'</span>';}
		elseif(is_integer($var)){ $t = 'int'; $var = '<span class="__r">'.$var.'</span>';}
		elseif(is_float($var)){ $t = 'float'; $var = '<span class="__r">'.$var.'</span>';}
		elseif(is_string($var)){ $t = 'string'; $var = '<span class="__r">[</span><span class="__b">'.htmlspecialchars($var).'</span><span class="__r">]</span>';}
		elseif(is_null($var)){ $t = 'null'; $var = '<span class="__g">NULL</span>';}
		elseif(is_object($var)){ $t = 'object'; $var = '{...}';}
		echo self::indent($level, '<i>'.$t.'</i> '.$var);
	}

	private static function attach_style(){
		ob_start();
		?>
		<style>
		div.__dv, div.__dv * {
			font-size: 12px;
			font-family: verdana,helvetica,arial,sans-serif;
			margin: 0;
			padding: 0;
			border: 0;
			/*font-family: monospace;*/
		}
		div.__dv {
			margin: 5px 0;
			background-color: #f8f8f8;
			border: 1px solid #000;
			float: left;
		}
		div.__dv div.__dv_header {
			background-color: #eee;
			border-bottom: 1px solid #000;
		}
		div.__dv .__r {
			color: #a00;
		}
		div.__dv .__g {
			color: #080;
		}
		div.__dv .__b {
			color: #00a;
		}
		div.__dv div.__tab {
			float: left;
			margin-left: 2px;
			padding-left: 10px;
			border-left: 1px dotted #000;
			width: 1px;
		}
		</style>
		<?php
		echo ob_get_clean();
	}
}
