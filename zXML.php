<?php
// version: 1
namespace za\zXML;

// ザガタ。六 /////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////

class zXML {
	/* Zagata.XML */
	private $sort = 'asc';
	private $limt = false;

	public function load() {
		// $this->za->msg('ntf','db', 'love.xml loaded');
		$this->x = simplexml_load_file($this->f, 'za\zXML\SimpleXMLExtend'); // , null, LIBXML_NOCDATA);

		$this->za->ee($this->n.'_ready');
	}
	
	private function save() {
		$dom = new \DOMDocument('1.0','UTF-8');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->loadXML($this->x->asXML());
		$dom->save($this->f);
	}
	
	/////////////////////////////// 
	// funcs
	public function max($k,$idp=false) {
		$qu = './/i[./'.$k.'!="" '.(($idp)?'and ./idp="'.$idp.'"':'').' and not(./'.$k.' <= preceding-sibling::i'.(($idp)?'[./idp="'.$idp.'"]':'').'/'.$k.') and not(./'.$k.' <= following-sibling::i'.((is_numeric($idp))?'[./idp="'.$idp.'"]':((is_string($idp))?'[./ca="'.$idp.'"]':'')).'/'.$k.')]/'.$k;
		$re = $this->x->xpath($qu);
		if($re) {
			$re = $this->i2a($re); $re = $re[$k]; $re = ((int) $re);
			return $re;
		} else {
			return 0;
		}
	}
	
	public function srt($s=false) {
		$this->sort = ($s)?$s:false;
	return $this;
	}
	public function lim($s=false) {
		$this->limt = ($s)?$s:false;
	return $this;
	}
	
	public function q($qu) {
		$re = false;
		try {
			$tmp = array();
			foreach($this->x->xpath($qu) as $i) {
				$tmp[] = $this->i2a($i);
			}
			// pp
			if($this->limt) {
				$srt = (($this->sort&&$this->sort=='desc')?'dsc':'asc');
				usort($tmp, array($this,'srt_'.$srt));
				
				// elements of page
				// ->pp - have to change it as one solution
				$pg = (integer) $this->limt; $p = (integer) $this->za->mm(array('vrs','page')); $ix = count($tmp);
				$p = ($p==0)?1:$p;
				$this->za->mm('vrs',array('pages'=>ceil($ix / $pg)));

				for($i=($pg*($p-1));$i<($pg*$p);$i++) {
					if($i < $ix) {
						$re[] = $tmp[$i];
						// child elements
						$qu = ((isset($tmp[$i]['ids']))?$tmp[$i]['ids']:'').$tmp[$i]['id'].',';
						$qu = $this->qq( $qu );
						if($qu) {
							foreach($qu as $y) {
								$re[] = $y;
							}
						} else {}
					} else {
						break;
					}
				}
				
			} else {
				$re = $tmp;
			}
		} catch (Exception $e) {
			// exit($qu."\n".$e->getMessage());
			$this->za->dbg($qu."\n".$e->getMessage());
		}
	return ((is_array($re)&&count($re)>0)||!is_array($re))?$re:false;
	}
	
	public function updwn($id,$d='upp') {
		$srt = (($this->sort&&$this->sort=='desc')?'dsc':'asc');
		$tmp = $this->qq($id)[0];
		$re = array();

		$tmp['z'] = (isset($tmp['z']))?$tmp['z']:0; // have to send return false
		$qu = './/i[ (not(./idx) or ./idx!="*") and ./idp="'.$tmp['idp'].'" and ./z '.((($d=='upp'&&$srt=='asc')||($d=='dwn'&&$srt=='dsc'))?'<':'>').' '.$tmp['z'].' ]';
		foreach($this->x->xpath($qu) as $i) {
			$re[] = $this->i2a($i); // no break 'cos have to sort
		}
		if($re) {
			usort($re, array($this,'srt_'.$srt));

			$re = (($d=='upp')?$re[(count($re)-1)]:$re[0]);
			$z = $re['z']; $re['z'] = $tmp['z']; $tmp['z'] = $z;

			$this->qq($tmp['id'],$tmp);
			$this->qq($re['id'],$re);
			return true;
		} else { }
	return false; 
	}
	private function srt_asc($a,$b) {
		if($a['z']==$b['z']) { return 0; } else { return ($a['z']<$b['z'])? -1: 1; }
	}
	private function srt_dsc($a,$b) {
		if($a['z']==$b['z']) { return 0; } else { return ($a['z']<$b['z'])? 1: -1; }
	}

	public function qq($k,$v=false) {
		if($k == 'del' && $v) { // del
			try {
				if(is_array($v)) {
					$qu = array();
					if(isset($v['id'])) { $qu[] = './id="'.$v['id'].'"'; } else {}
					if(isset($v['ids'])) { $qu[] = 'starts-with(./ids,"'.$v['ids'].$v['id'].','.'")'; } else {}
					$qu = implode(" or ",$qu);
				} elseif(strpos($v,',')!==false) {
					$qu = 'starts-with(./ids,"'.$v.'")';
				} else {
					$qu = './id = "'.$v.'" ';
				}
				$tmp = $this->x->xpath('.//i['.$qu.']');

				foreach($tmp as $k=>$i) {
					unset($tmp[$k][0]);
				}

				// exit($this->x->asXML());
				$this->save();
				return true;
			} catch (Exception $e) {
				$this->za->msg('err',$this->n,$e->getMessage());
			}
			return false;
		} elseif($k && $v) { // red
			$tmp = $this->x->xpath('.//i[./id="'.$k.'"]');
			try {
				// ???
				foreach($v as $kk=>$vv) {
					if(in_array($kk,array('dpd','dlbd','dfd','xpd','xlbd','xfd'))) {
						unset($v[$kk]);
					} else {}
				}
				
				$this->qq('del',$k);
				$this->qq(false,$v);

				// exit($this->x->asXML());
				$this->save();
				return true; 
			} catch (Exception $e) {
				$this->za->msg('err',$this->n,'red '.$e->getMessage());
			}
			return false;
		} elseif($k!==false) { // get
			$re = array();
			$qu = false;

			if(is_string($k)&&strpos($k,',')!==false) {
				$qu = '(not(./idx) or ./idx="") and starts-with(./ids,"'.$k.'")';
			} elseif($k == '*') {
				$qu = './idx="*"';
			} elseif(is_numeric($k)) {
				$qu = './id="'.$k.'"'; // and (not(./idx) or ./idx!="*")
			} elseif(is_string($k)) {
				$qu = './ca="'.$k.'" or ./li="'.$k.'"';
			} else { // elseif(is_array($k)) {
				return false;
			} 
			if(!isset($this->za->m['adm'])) {
				$qu = './/i[ ('.$qu.') and (not(./pd) or ./pd=\'\' or ./pd <= '.time().') and (not(./fd) or ./fd=\'\' or ./fd > '.time().') ]';
			} else { $qu = './/i[ '.$qu.' ]'; }
			// $this->za->msg('ntf',$this->n,$qu);

			try {
				foreach($this->x->xpath($qu) as $i) {
					$i = $this->i2a($i);
					foreach(array('pd','lbd','fd') as $v) {
						if(isset($i[$v])&&is_numeric($i[$v])) {
							$i['d'.$v] = date('d.m.Y H:i:s T',$i[$v]); 
							$i['x'.$v] = date(DATE_W3C,$i[$v]); 
						} else {}
					}
					$re[] = $i;
				}
			} catch (Exception $e) {
				$this->za->dbg($qu."\n".$e->getMessage());
			}

			return ((is_array($re)&&count($re)>0)||!is_array($re))?$re:false;
		} elseif($v) { // add
			try {
				// ???
				foreach($v as $kk=>$vv) {
					if(in_array($kk,array('dpd','dlbd','dfd','xpd','xlbd','xfd'))) {
						unset($v[$kk]);
					} else {}
				}
				
				// ??? -- make it trough this->za->a2x function
				$this->a2i(array($v),$this->x);
				
				// exit($this->x->asXML());
				$this->save();
				return true; 
			} catch (Exception $e) {
				$this->za->dbg('adding: '.$e->getMessage());
				$this->za->msg('err',$this->n,$e->getMessage());
			}
			return false;
		} else {
			$this->za->msg('err','zXML','no condition accepted by qq: '.$k);
		}
	return $this;
	}
	
	public function pp($k,$pg=10,$p=1) {
		$re = array(); $tmp = array();

		foreach($this->x->xpath('.//i[ ./ids="'.$k.'" and (not(./pd) or ./pd <= '.time().') and (not(./fd) or ./fd > '.time().') ]') as $i) {
			$tmp[] = $this->i2a($i);
		}

		$srt = (($this->sort&&$this->sort=='desc')?'dsc':'asc');
		usort($tmp, array($this,'srt_'.$srt));

		// elements of page
		$pg = (integer) $pg; $p = (integer) $p; $ix = count($tmp);

		$this->za->mm('vrs',array('pages'=>ceil($ix / $pg)));

		for($i=($pg*($p-1));$i<($pg*$p);$i++) {
			if($i < $ix) {
				$re[] = $tmp[$i];
				// child elements
				$qu = ((isset($tmp[$i]['ids']))?$tmp[$i]['ids']:'').$tmp[$i]['id'].',';
				$qu = $this->qq( $qu );
				if($qu) {
					foreach($qu as $y) {
						$re[] = $y;
					}
				} else {}
			} else {
				break;
			}
		}

	return ((is_array($re)&&count($re)>0)||!is_array($re))?$re:false;
	}
	
	public function a2i($a,&$x) {
		foreach($a as $k=>$v) {
			$k = (is_numeric($k))?'i':$k;
			if(is_array($v)) {
				$sub = $x->addChild($k);
				$this->a2i($v,$sub);
			} elseif(strpos($v,'<')!==false||strpos($v,'>')!==false) {
				$x->addCData($k,$v);
			} else {
				$x->addChild($k,$v);
			}
		}
	return $x;
	}
	
	public function i2a($i) {
		$re = array();
		$tag = 0;
		foreach($i as $k => $v) {
			$re[ (($v->getName() == 'i')?$tag:$v->getName()) ] = (count($v->children()))?$this->i2a($v):strval($v);
			if($v->getName() == 'i') {
				$tag += 1;
			} else { }
		}
	return $re;
	}
	
	public function a2q($a,$l=0) {
		$q = '';
		if($a[0]=='not') {
			$q = "not( ".$this->a2q($a[1],1).")";
		} elseif($a[1]=='in') {
			$q = "contains(',".$a[2].",',concat(',',./".$a[0].",','))";
		} elseif($a[1]=='like') {
			// 
		} else {
			$a[1] = str_replace(array('&&','&','||','|'),array('and','and','or', 'or'),trim($a[1]));
			$q = "(".((is_array($a[0]))?$this->a2q($a[0],1):"./".$a[0])." ".$a[1]." ".((is_array($a[2]))?$this->a2q($a[2],1):"'".$a[2]."'").")";
		}
		if($l==0) { $q = '/r/i['.$q.']'; } else {}
	return $q;
	}
	
	/////////////////////////////// 
	// ini
	function __construct($za,$a=false,$n=false) {
		$this->za = $za;
		$this->n = (($n)?$n:'zXML');
		// $this->za->msg('dbg','zXML','i am '.$this->n.'(zXML)');

		$this->cd = realpath( __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR );
		$this->dd = DIRECTORY_SEPARATOR;

		$a = explode('@',str_replace(array('://',':','@'),'@',$a));
		$a = end($a);
		$this->f = (strpos($a,'/')===false)?$this->cd.$this->dd.$a:$a;

		if(is_file($this->f)) {
			// $this->za->msg('ntf','db', 'start loading '.$a);
			$this->za->ee($this->n,array($this,'load'));
		} else {
			$this->za->msg('err', $this->n, 'no file '.$a);
			$this->za->ee($this->n.'_ready');
		}
	}
}

////////////////////////////////////////////////////////////////

class SimpleXMLExtend extends \SimpleXMLElement {
	public function addCData($k,$v) {
		$node = $this->addChild($k); //Added a nodename to create inside the function
		$node = dom_import_simplexml($node);
		$no = $node->ownerDocument;
		$node->appendChild($no->createCDATASection($v));
	}
} 

////////////////////////////////////////////////////////////////

if(class_exists('\zlo')) {
	\zlo::da('zXML');
} elseif(realpath(__FILE__) == realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME'])) {
	header("content-type: text/plain;charset=utf-8");
	exit('zXML');
} else {}

?>