<?php
include("conf.php");

if(get_magic_quotes_gpc()||get_magic_quotes_runtime())
{
	if(isset($_GET))
		$_GET=array_map(array('CFileManPermission','_StripSlashes'),$_GET);
	if(isset($_POST))
		$_POST=array_map(array('CFileManPermission','_StripSlashes'),$_POST);
	if(isset($_REQUEST))
		$_REQUEST=array_map(array('CFileManPermission','_StripSlashes'),$_REQUEST);
	if(isset($_COOKIE))
		$_COOKIE=array_map(array('CFileManPermission','_StripSlashes'),$_COOKIE);
}


class CFileManPermission {
	private $rootDir;

	static function _StripSlashes(&$data)
	{
		return is_array($data)?array_map(array('CFileManPermission','_StripSlashes'),$data):stripslashes($data);
	}

	function __construct($rootDir) {
		$this->rootDir=$rootDir;
	}

	function is_in_root($name) {
		$p=strpos($name,$this->rootDir);
		return ($p===0 && $name>$this->rootDir);
	}

	function canDelete($name) {
		return $this->is_in_root($name);
	}

	function canMove($src,$dst) {
		return $this->is_in_root($src)&&$this->is_in_root($dst);
	}

	function canRename($name) {
		return $this->is_in_root($name);
	}

	function canCreateFile($name) {
		return $this->is_in_root($name);
	}

	function isFileAcceptable($file) {
		return preg_match('/\.(html?|jpe?g|gif|png|zip|xls|doc|pdf|rar|ctx)$/i',$file);
	}

}

class CFileMamager {
	private $rootDir;
	private $currentDir;
	private $filearr;
	private $dirarr;
	public $quota_limit=100000000;
	public $quota_inuse=0;

	private $file_permissions;
	/* @var $file_permissions CFileManPermission*/


	function __construct($rootDir,$dir) {
		$this->rootDir=$rootDir;
		$this->currentDir=$this->check_dir($dir);
		$this->file_permissions=new CFileManPermission($rootDir);
	//	$this->getDir();
	}


	function getCurrentDir() {
		if($this->currentDir[0]=='/')
			return $this->rootDir.$this->currentDir;
		return $this->rootDir.'/'.$this->currentDir;
	}

	function getOffsetDir() {
		if($this->currentDir[0]=='/')
			return $GLOBALS['FMAN_IMAGES_URL_PATH'].$this->currentDir;
		return $GLOBALS['FMAN_IMAGES_URL_PATH'].'/'.$this->currentDir;
	}

	function check_dir($dir) {
		if (!$dir) $dir="/";
		else {
			if ($dir[0]!="/") $dir="/".$dir;
			if ($dir[strlen($dir)-1]!="/") $dir.="/";
		}

		$dir = str_replace("//", "/", $dir );
		$dir = preg_replace( "#(^|/)\./#", "\\1", $dir );
		$dir = preg_replace( "#[^/]*(^|/)\.\./#", "", $dir );
		if (!$dir) $dir="/";

		if (strlen($dir)==0 || $dir=="." || $dir==".." ||
				$dir=="./" || $dir=="../" ||
				$dir=="/." || $dir=="/.." || preg_match("#/\.\./#",$dir) ) $dir="/";

		return $dir;
	}

	function check_file($file) {
		if (strlen($file)==0 || $file=="." || $file==".." || $file[0]=="/" || $file[0]=="\\" || strpos($file,"\\") ||
	   	 strpos($file,"/") ) return FALSE;
		return 1;
	}

	function getDir() {
		$this->dirarr=array();
		$this->filearr=array();
		return $this->_getdir(0,0,new RecursiveDirectoryIterator($this->rootDir.$this->currentDir));
	}

	private function _getdir($dirarr_ind,$filearr_ind,$it) {
		/* @var $it RecursiveDirectoryIterator*/
		for( ; $it->valid(); $it->next()) {
			if($it->isDir() && !$it->isDot()) {
				if($it->hasChildren()) {
					$this->dirarr[$dirarr_ind][0]=$it->getFilename();
					$this->dirarr[$dirarr_ind][1]=0;
					$this->dirarr[$dirarr_ind++][2]=$it->getMTime();
				}
			} elseif($it->isFile()) {
				$this->filearr[$filearr_ind][0]=$it->getFilename();
				$this->filearr[$filearr_ind][1]=$it->getSize();
				$this->filearr[$filearr_ind++][2]=$it->getMTime();
			}
		}
		return array('dir'=>$this->dirarr,'file'=>$this->filearr);
	}

	function deltree($dir,&$total_bytes_deleted,&$status_line) {
	//	$total_bytes_deleted=0;
	//	$status_line='';
		return $this->_deltree(new RecursiveDirectoryIterator($this->rootDir.$this->currentDir.$dir),$total_bytes_deleted,$status_line);
	}

	private function _deltree($it,&$total_bytes_deleted,&$status_line) {
		for( ; $it->valid(); $it->next()) {
			if($it->isDir() && !$it->isDot()) {
				if($it->hasChildren()) {
					$bleh = $it->getChildren();
					$this->_deltree($bleh,$total_bytes_deleted,$status_line);
				}
				if($this->file_permissions->canDelete($it->getPathname())) {
					@$res=rmdir($it->getPathname());
				}
				else {
					$res=false;
				}
				if(!$res) {
					$status_line.="Error deliting directory <b>".$files."</b><br>";
					return FALSE;
				}

			} elseif($it->isFile()) {
				$fs=$it->getSize();
				if($this->file_permissions->canDelete($it->getPathname())) {
					@$res=unlink($it->getPathname());
				}
				else {
					$res=false;
				}
				if(!$res) {
					$status_line.="Error deliting object <b>".$files."</b>, please contact WebAdmin.<br>";
					return FALSE;
				}
				else {
					$total_bytes_deleted+=$fs;
				}
			}
		}
		return true;
	}

	function removeDir($name) {
		if($this->file_permissions->canDelete($this->rootDir.$this->currentDir.'/'.$name)) {
			@$res=rmdir($this->rootDir.$this->currentDir.'/'.$name);
			return $res;
		}
		return false;
	}

	function MakeDir($name) {
		if($this->file_permissions->canCreateFile($this->rootDir.$this->currentDir.'/'.$name)) {
			@$res=mkdir($this->rootDir.$this->currentDir.'/'.$name,0755);
			return $res;
		}
		return false;
	}

	function createFile($name) {
		if($this->file_permissions->canCreateFile($this->rootDir.$this->currentDir.'/'.$name)) {
			@$res=touch($this->rootDir.$this->currentDir.'/'.$name);
			return $res;
		}
		return false;
	}

	function isFile($name) {
		return is_file($this->rootDir.$this->currentDir.'/'.$name);
	}

	function isDir($name) {
		return is_dir($this->rootDir.$this->currentDir.'/'.$name);
	}

	function rename($old_name,$new_name) {
		if($this->file_permissions->canRename($this->rootDir.$this->currentDir.'/'.$new_name)) {
			@$res=rename($this->rootDir.$this->currentDir.'/'.$old_name,$this->rootDir.$this->currentDir.'/'.$new_name);
			return $res;
		}
		return false;
	}

	function isAllowableFileExt($name) {
		return true;
	}

	function move_uploaded_file($file_struct) {
		if($this->file_permissions->canMove($this->rootDir.$this->currentDir.'/'.$file_struct['name'],$this->rootDir.$this->currentDir.'/'.$file_struct['name'])) {
			@$res=move_uploaded_file($file_struct['tmp_name'],$this->rootDir.$this->currentDir.'/'.$file_struct['name']);
			return $res;
		}
		return false;
	}

	function unlinkFile($name) {
		if($this->file_permissions->canDelete($this->rootDir.$this->currentDir.'/'.$name)) {
			@$res=unlink($this->rootDir.$this->currentDir.'/'.$name);
			return $res;
		}
		return false;
	}

	function getFileSize($name) {
		return filesize($this->rootDir.$this->currentDir.'/'.$name);
	}

	function moveFile($src,$dst) {
		if($this->file_permissions->canMove($this->rootDir.$src,$this->rootDir.$this->currentDir.$dst)) {
			@$res=rename($this->rootDir.$src,$this->rootDir.$this->currentDir.$dst);
			return $res;
		}
		return false;
	}
}

class CFManInterface {
	private $file_manager=null;
	private $files;
	private $data;
	private $cur_dir;
	private $srtfld;

	public $status_line;
	public $sortfield;
	public $sortorder;
	public $clipboard='';
	public $resource='';

	/* @var $file_manager CFileMamager*/

	function __construct($virtual_dir,$currentDir,$data) {
		$this->file_manager=new CFileMamager($virtual_dir,$currentDir);
		$this->cur_dir=$currentDir;
		$this->files=$data['files'];
		$this->data=$data;
		$this->clipboard=$this->data['clipboard'];
	}

	function commandButton($newfoldername,$command) {

		if(!$this->file_manager->check_file($newfoldername))
		{
			$this->status_line.="Invalid name <b>$newfoldername</b>";
		}
		else {
			switch ($command) {
				case 1: {
					if(!$this->file_manager->MakeDir($newfoldername))
						$this->status_line.="Error creating folder <b>$newfoldername</b>";
						break;
					}
					case 3: {
						if(!$this->file_manager->createFile($newfoldername.".html"))
						//if (!@touch($basedir.$dir."/".$newfoldername.".html"))
							$this->status_line.="Error creating file <b>$newfoldername</b>";
						break;
					}
				 case 2: {
				 	if($this->file_manager->isFile($newfoldername)) {
						$this->status_line.="Veche ima fail s takova ime<br>";
					} elseif( $this->file_manager->isDir($newfoldername) ) {
						$this->status_line.="Veche ima Folder s takova ime<br>";
					} elseif(!$this->file_manager->check_file($this->files[0])) {
						$this->status_line.="Invalid name <b>".$this->files[0]."</b><br>";
					} elseif (!$this->file_manager->rename($this->files[0],$newfoldername) ) {
						$this->status_line.="Error Renam object <b>".$this->files[0]."</b>.<br>";
					}
				}
			}
		}
		return $this->status_line;
	}

	function Upload() {
		for ($i=1;$i<=max(1,(int)$this->data['urlcount']);$i++) {
			$uf=$_FILES["userfile".$i];

			if(!$this->file_manager->isAllowableFileExt($uf['name']) || !$this->file_manager->check_file($uf['name']))
			{
				$this->status_line.="Invalid name <b>{$uf['name']}</b><br>";
				continue;
			}
			if($this->file_manager->isFile($uf['name']))
				continue;
			if(intval($uf['size'])+$this->file_manager->quota_inuse<$this->file_manager->quota_limit) {
				if(is_uploaded_file($uf['tmp_name'])) {
					if(!$this->file_manager->move_uploaded_file($uf)) {
						$this->status_line.="ERROR Uploading files.";
					} else {
//						update_quota($rid, $uf_s);
						$this->file_manager->quota_inuse+=intval($uf['size']);
					}
				}
			} else {
				$this->status_line.="Not enought disk space!";
				break;
			}
		}
	}

	function Delete() {
		$total_bytes_deleted=0;
		$files=$this->data['files'];
		for ($i=0;$i<count($files);$i++) {
			$uf=$files[$i];
			if ($this->file_manager->check_file($uf) &&$this->file_manager->isFile($uf) )
			{
				$uf_s=$this->file_manager->getFileSize($uf);
				if($this->file_manager->unlinkFile($uf))
				{
					$total_bytes_deleted+=$uf_s;
				} else {
					$this->status_line.="Error deliting file <b>".$files[$i]."</b><br>";
				}
			} elseif ($this->file_manager->isDir($uf) ) {
				if ($this->file_manager->deltree($uf,$total_bytes_deleted,$this->status_line))
				{
					if ( !$this->file_manager->removeDir($uf) )
					{
						$this->status_line.="Error deliting folder <b>".$files[$i]."</b>, Not empty.<br>";
					}
				}
			} else {
				$this->status_line.="Error deliting object <b>".$files[$i]."</b>, please contact WebAdmin.<br>";
			}
		}
		if ($total_bytes_deleted>0)
		{
//			update_quota($rid, -$total_bytes_deleted);
			$this->file_manager->quota_inuse-=$total_bytes_deleted;
		}
		return $total_bytes_deleted;
	}

	function Paste() {
		$this->clipboard=$clipboard = explode ("|", $this->data['clipboard']);
		$srcdir=$this->file_manager->check_dir($clipboard[0]);
		if($srcdir[strlen($srcdir)-1]=='/')
			$srcdir=substr($srcdir,0,strlen($srcdir)-1);
		for ($i=1;$i<count($clipboard);$i++) {
		//	$srcfile = $basedir.$srcdir."/".$clipboard[$i];
		//	$dstfile = $basedir.$dir."/".$clipboard[$i];
			if(!$this->file_manager->check_file($clipboard[$i])) {
				$this->status_line.="Invalid name ".$clipboard[$i]."<br>";
			} elseif ($this->file_manager->isFile($clipboard[$i])) {
				$this->status_line.=$clipboard[$i].", Veche ima fail s takova ime v tazi direktoria<br>";
			} elseif ( $this->file_manager->isDir($clipboard[$i]) ) {
				$this->status_line.=$clipboard[$i].", Veche ima Folder s takova ime v tazi direktoria<br>";
			} elseif ( !$this->file_manager->moveFile($srcdir.'/'.$clipboard[$i],$clipboard[$i]) ) {
				$this->status_line.="failed to Paste ".$clipboard[$i]."<br>";
			}
		}
		$this->clipboard=$this->data['clipboard']='';

	}

	function cmpdesc ($a, $b) {
		if ($a[$GLOBALS['sort_field_name']] == $b[$GLOBALS['sort_field_name']]) return 0;
		return ($a[$GLOBALS['sort_field_name']] > $b[$GLOBALS['sort_field_name']]) ? -1 : 1;
	}

	function cmpasc ($a, $b) {
		if ($a[$GLOBALS['sort_field_name']] == $b[$GLOBALS['sort_field_name']]) return 0;
		return ($a[$GLOBALS['sort_field_name']] < $b[$GLOBALS['sort_field_name']]) ? -1 : 1;
	}

	function printDir( $sortorder, $sortfield) {
		$dir=$this->cur_dir;
		$webdir=$this->file_manager->getOffsetDir();
	//	$webdir = $base_virtual_disk_URL."/".$this->data['resource'];
	//	if ($webdir[strlen($webdir)-1]=="/")  $webdir=substr($webdir,0,-1);
	//	$webdir.=$dir;
		$this->sortorder=$sortorder;
		$this->sortfield=$sortfield;
		$root=realpath(dirname(__FILE__).'/../../../../');
		//echo $root;
		$array=$this->file_manager->getDir();
		$filearr=$array['file'];
		$dirarr=$array['dir'];
		$this->srtfld=$sortfield;
		if ($sortorder=="asc")
		{
			$GLOBALS['sort_field_name']=$this->srtfld;
			if ($filearr) usort($filearr,array('CFManInterface',"cmpasc"));
			if ($this->srtfld==1) $this->srtfld=0;
			$GLOBALS['sort_field_name']=$this->srtfld;
			if ($dirarr) usort($dirarr,array('CFManInterface',"cmpasc"));
			for ($j=0; $j<count($dirarr); $j++)
				echo "<tr bgcolor=#eeeecc><td><input type=checkbox name=\"files[]\" isdir=\"1\" value=\"".$dirarr[$j][0]."\"></td><td class=\"hnm\">&nbsp;<a href=\"#\" onClick=\"return GoToFolder('".$dirarr[$j][0]."');\"><img src=\"folder.gif\" border=0> ".$dirarr[$j][0]."</a></td><td align=right>&nbsp;</td><td class=\"hnm\">&nbsp;".date("d F Y H:i",$dirarr[$j][2])."</td></tr>\n";
			for ($j=0; $j<count($filearr); $j++) {
				echo "<tr bgcolor=#eeeecc><td><input type=checkbox name=\"files[]\" value=\"".$filearr[$j][0]."\" filesize=\"".$filearr[$j][1]."\"></td><td class=\"hnm\">&nbsp;<a href=\"$webdir".$filearr[$j][0]."\" target=\"_blank\"><img src=\"f.gif\" border=0> ".$filearr[$j][0]."</a>".render_edit_link($dir.$filearr[$j][0])."</td><td class=\"hnm\" align=right>".$filearr[$j][1]."&nbsp;</td><td class=\"hnm\">&nbsp;".date("d F Y H:i",$filearr[$j][2])."</td></tr>\n";
			}
		} else {
			$GLOBALS['sort_field_name']=$this->srtfld;
			if ($filearr) usort($filearr,array('CFManInterface',"cmpdesc"));
			if ($this->srtfld==1) $this->srtfld=0;
			$GLOBALS['sort_field_name']=$this->srtfld;
			if ($dirarr) usort($dirarr,array('CFManInterface','cmpdesc'));
			for ($j=0; $j<count($filearr); $j++) {
				echo "<tr bgcolor=#eeeecc><td><input type=checkbox name=\"files[]\" isdir=\"1\" value=\"".$filearr[$j][0]."\"></td><td class=\"hnm\">&nbsp;<a href=\"$webdir".$filearr[$j][0]."\" target=\"_blank\"><img src=\"f.gif\" border=0> ".$filearr[$j][0]."</a>".render_edit_link($dir.$filearr[$j][0])."</td><td class=\"hnm\" align=right>".$filearr[$j][1]."&nbsp;</td><td class=\"hnm\">&nbsp;".date("d F Y H:i",$filearr[$j][2])."</td></tr>\n";
			}
			for ($j=0; $j<count($dirarr); $j++)
				echo "<tr bgcolor=#eeeecc><td><input type=checkbox name=\"files[]\" value=\"".$dirarr[$j][0]."\" filesize=\"".$filearr[$j][1]."\"></td><td class=\"hnm\">&nbsp;<a href=\"#\" onClick=\"return GoToFolder('".$dirarr[$j][0]."');\"><img src=\"folder.gif\" border=0> ".$dirarr[$j][0]."</a></td><td class=\"hnm\" align=right>&nbsp;</td><td class=\"hnm\">&nbsp;".date("d F Y H:i",$dirarr[$j][2])."</td></tr>\n";
		}
	}

	function render() {
		if($this->data['upload']) {
			$this->Upload();
		}
		if($this->data['delete']) {
			$this->Delete();
		}
		if($this->data['paste']) {
			$this->Paste();
		}
		if($this->data['commandbtn']) {
			$this->commandButton($this->data['newfoldername'],intval($this->data['command']));
		}
		$sortorder=$this->data['sortorder'];
		$sortfield=$this->data['sortfield'];
		if ($sortorder!="asc" && $sortorder!="desc" )
			$sortorder=$this->data['sortorder']= "asc";
		if ($sortfield!=1 && $sortfield!=2 && $sortfield!=3)
			$sortfield=$this->data['sortfield']= 0;
		$this->printDir($sortorder,$sortfield);
	}
}

function get_full_virtual_disk_path($path){
	$virtual_disk_basehomedir = $GLOBALS['FMAN_IMAGES_ABS_PATH'];
	$path = $virtual_disk_basehomedir."/".$path;
	return $path;
}


$base_virtual_disk_URL = $GLOBALS['FMAN_IMAGES_URL_PATH'];
function isAllowableFileExt($file){
	$AllowableFileExtArray = Array("jpg", "png", "gif");
//	if(($ext = strtolower(substr( strrchr($file, "."), 1))) && in_array($ext, $AllowableFileExtArray) ) return true;
	return true;
}

function myError($code) {
	echo "BLia";
	exit();
}

function render_edit_link($file){
	if(preg_match('/\.(html?)|(ctx)$/i', $file)){
		return " | <a href=\"page_edit.php?page=$file\" target=_blank>edit</a>";
	}
}

function cmpdesc ($a, $b) {
	global $srtfld;
	if ($a[$srtfld] == $b[$srtfld]) return 0;
	return ($a[$srtfld] > $b[$srtfld]) ? -1 : 1;
}



function getBaseDir() {
	$basedir=get_full_virtual_disk_path("");
	if ($basedir[strlen($basedir)-1]=="/") $basedir=substr($basedir,0,-1);
	return $basedir;
}

function getDir($basedir) {
	$dir=CFileMamager::check_dir($_POST['dir']);
	if (!@is_dir($basedir)) return false;
	if (!@is_dir($basedir.$dir)) $dir="/";
	return $dir;
}

	$basedir=getBaseDir();
	$dir=getDir($basedir);
	if($dir===false) myError(1);

	$selector=$_GET['selector'];

	$sortorder=$_POST['sortorder'];
	$sortfield=$_POST['sortfield'];
	if ($sortorder!="asc" && $sortorder!="desc" )
		$sortorder= "asc";
	if ($sortfield!=1 && $sortfield!=2 && $sortfield!=3)
		$sortfield= 0;


	$fm=new CFManInterface($basedir,$dir,$_POST);


?>


<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="cache-control" content="no-cache">
<title>File Manager</title>
<script type="text/javascript" src="../../tiny_mce_popup.js"></script>
<script language="JavaScript">
<!--


function insertMyImage(href) {
	tinyMCEPopup.editor.execCommand('mceInsertContent',false,'<img src="'+href+'" alt="" />');
	tinyMCEPopup.close();
}

function getFileSize(size) {


	if(size<1024) {
		return size+' bytes';
	}
	if(size<1048576) {
		var t=(size/1024).toFixed(0);
		return t+"Kb";
	}
	var t=(size/1048576).toFixed(0);
	return t+"Mb";
}

function getFileExt(filename) {
	var s=filename.lastIndexOf(".");
	if(s==-1)
		return '';
	if(s>=filename.length-1)
		return -1;
	s=filename.substr(s+1,filename.length-1);
	return s;
}



function insertMyLink(href,filename,filesize) {

	var conf =new Array (
		new Array('pdf','pdfStyle'),
		new Array('doc','docStyle'),
		new Array('zip','zipStyle')
		);

	var style=null;
	var ext=getFileExt(filename).toLowerCase();
	if(ext!='') {
		for(i=0;i<conf.length;i++) {
			if(ext==conf[i][0]) {

				style=conf[i][1];

				break;
			}
		}
	}

	style = 'DownloadLink';
	filename+=" ("+getFileSize(filesize)+")";

	var ed=tinyMCEPopup.editor;
	var n = ed.selection.getNode();
	if(n.nodeName=='A'){
		ed.execCommand('mceInsertLink', false, href);
	} else if(ed.selection.isCollapsed()) {
			href = href.replace('"', "&quot;");
			filename = filename.replace(/&quot;/g, '"');
			ed.execCommand('mceInsertContent', false, '<a href="'+href+'" class="'+style+'">'+filename+'</a>');
	} else {
		ed.execCommand('mceInsertLink', false, href);
	}
tinyMCEPopup.close();
}


var i=1;

history[history.length]='';
history[history.length-1]='';

function br_onclick() {
	if (i<30) {
		j=i;
		i++;
		eval("document.f2.elements.userfile"+j).insertAdjacentHTML("AfterEnd","<br id=\"brid"+i+"\"><input type=\"file\" name=\"userfile"+i+"\" size=\"40\">");
		cf.innerHTML=i;
		document.f2.elements.urlcount.value=i;
	}
}
function bl_onclick() {
	if (i>1) {
		j=i;
		i--;
		eval("document.f2.elements.userfile"+j+".outerHTML=\"\"");
		cf.innerHTML=i;
		document.f2.elements.urlcount.value=i;
		eval("brid"+j+".outerHTML=\"\"");
	}
}

function setsort(sortfield) {
	if (sortfield==document.f2.sortfield.value) {
		document.f2.sortorder.value=='asc'?document.f2.sortorder.value='desc':document.f2.sortorder.value='asc';
	} else {
		document.f2.sortfield.value=sortfield;
	}
	document.f2.submit();
	return false;
}

function GoBack(){
	str=document.f2.dir.value;
	if (str=="/") return false;
	str=str.substring(0,str.lastIndexOf("/"));
	str=str.substring(0,str.lastIndexOf("/")+1);
	document.f2.dir.value=str;
	document.f2.submit();
	return false;
}

function GoToFolder(folder){
	if(folder.substring(0,1)=="/") document.f2.dir.value=folder;
	else document.f2.dir.value+=folder+"/";
	document.f2.submit();
	return false;
}

function CopyToClipboard(){
	tmp="";
	for (i=0; i<document.f2.elements.length; i++) {
		if( (document.f2.elements[i].name=='files[]') && (document.f2.elements[i].checked) )
			tmp+="|"+document.f2.elements[i].value;
	}
	if (tmp.length>1) {
		document.f2.clipboard.value=document.f2.dir.value+tmp;
	} else {
		document.f2.clipboard.value='';
	}

	return false;
}

function CheckClipboard(){
	str=document.f2.clipboard.value;
	if(str.length>1) {
		str=str.substring(0,str.lastIndexOf("|"));
		if (str!=document.f2.dir.value) return true;
	}
	alert("There's nothing to paste!");
	return false;
}

function CheckNewDir(){
	str=document.f2.newfoldername.value;
	if(str.length>=1 && str.indexOf("/")==-1) return true;
	alert(str+" is Invalid Name!");
	return false;
}

function CheckDelete(){
	tmp="";
	for (i=0; i<document.f2.elements.length; i++) {
		if( (document.f2.elements[i].name=='files[]') && (document.f2.elements[i].checked) )
			tmp+=document.f2.elements[i].value+"\n";
	}
	if (tmp.length>1) {
		return confirm("are you sure you want to delete:\n"+tmp);
	} else {
		alert("nothing to delete");
	}
	return false;
}

function TransferSelected(){
  var sf = tinyMCEPopup.getWindowArg('selector_func');
	base_virtual_disk_URL = "<? echo $base_virtual_disk_URL; ?>"+document.f2.dir.value;
	for (i=0; i<document.f2.elements.length; i++) {
		if( (document.f2.elements[i].name=='files[]') && (document.f2.elements[i].checked) ) {

			if (sf=='insertMyImage') insertMyImage(base_virtual_disk_URL+document.f2.elements[i].value+(document.f2.elements[i].getAttribute('isdir')=="1"?"/":""));
			if (sf=='insertMyLink') insertMyLink(base_virtual_disk_URL+document.f2.elements[i].value+(document.f2.elements[i].getAttribute('isdir')=="1"?"/":""), document.f2.elements[i].value, document.f2.elements[i].getAttribute('filesize'));
			if(sf=='customFB'){
				var target_ctrl = tinyMCEPopup.getWindowArg('target_ctrl');
				target_ctrl.value = base_virtual_disk_URL+document.f2.elements[i].value+(document.f2.elements[i].getAttribute('isdir')=="1"?"/":"");
			}
		}
	}
	tinyMCEPopup.close();
}

function checkthis(){
	return true;
}


-->


</script>

</head>
<body bgcolor="#999999" text="#000000" topmargin="2" marginheight="2" leftmargin="2" marginwidth="2">




<table width="100%" border="0" cellspacing="0" cellpadding="4" align="center" bgcolor="#CCCCCC">
  <tr>
    <td valign="top" colspan="2" class="hnm">
<form name="f2" method="post" enctype="multipart/form-data" action="<?echo basename($PHP_SELF);?>" onSubmit="return checkthis();">
<table border=0 cellpadding=2 cellspacing=0 bgcolor=#ffffff width="100%">
<tr><td bgcolor=#cccc99 width=1% class="hnm">&nbsp;Location&nbsp;</td><td bgcolor=#cccc99><input name="dir" value="<? echo $dir ?>">
<input type="submit" value="Go">
<input type="button" value="Back" onClick="return GoBack();">
<input type="submit" value="Delete" name="delete" onClick="return CheckDelete();">
<input type="button" value="Cut" name="Cut" onClick="return CopyToClipboard();">
<input type="submit" value="Paste" name="paste" onClick="return CheckClipboard();">
<input type="button" value="Select" name="fselect" onClick="TransferSelected();">
</td></tr>
</table>
<table border=0 cellpadding=0 cellspacing=1 bgcolor=#ffffff width="100%">
<tr bgcolor=#cccc99>
	<td width=20>&nbsp;</td>
	<td class="hnm">&nbsp;<a href="#" onClick="return setsort('0');">Name <? if ($sortfield=='0') if($sortorder=="asc") echo "<img src='asc.gif' border=0>"; else echo "<img src='desc.gif' border=0>"; ?></a></td>
	<td  class="hnm" width=100 align=right><a href="#" onClick="return setsort('1');"><? if ($sortfield=='1') if($sortorder=="asc") echo "<img src='asc.gif' border=0>"; else echo "<img src='desc.gif' border=0>"; ?> Size</a>&nbsp;</td>
	<td  class="hnm" width=100>&nbsp;<a href="#" onClick="return setsort('2');">Modified <? if ($sortfield=='2') if($sortorder=="asc") echo "<img src='asc.gif' border=0>"; else echo "<img src='desc.gif' border=0>"; ?></a></td>
</tr>

<?
	$fm->render();
//	print_dir($dir,$sortfield,$sortorder);
	printf("<tr bgcolor=\"#D5D5D5\"><td class=\"hnm\" colspan=4>&nbsp;%s</td></tr>", $fm->status_line);
?>
</table>

<hr width="100%" noshade size="1" align=left>

<select name="command">
<option value="1">New Dir</option>
<option value="3">New HTML Document</option>
<option value="2">Rename first checked to</option>
</select>
<input type="text" name="newfoldername" value="" size="20"><input type="submit" value=" Do It " name="commandbtn" onClick="return CheckNewDir();">

<hr width="100%" noshade size="1" align=left>
<table><tr><td>

	<input type="button" value="&lt;&lt;" name="xxl" onClick="bl_onclick(this)">&nbsp;&nbsp;&nbsp;</td>
	<td><div id="cf">1</div></td>
	<td>&nbsp;&nbsp;&nbsp;<input type="button" value="&gt;&gt;" name="xxr" onClick="br_onclick(this)">&nbsp;&nbsp;&nbsp;&nbsp;files
</td></tr></table>
<input type="hidden" name="urlcount" value="1">
<input type="file" name="userfile1" size="40">

	<input type="submit" value="Upload" name="upload">&nbsp;&nbsp;&nbsp;&nbsp;
<input type="hidden" name="sortorder" value="<? echo $fm->sortorder ?>">
<input type="hidden" name="sortfield" value="<? echo $fm->sortfield ?>">
<input type="hidden" name="clipboard" value="<? echo $fm->clipboard ?>">
<input type="hidden" name="resource" value="<? echo $resource ?>">
<input type="hidden" name="selector" value="<? echo htmlspecialchars(stripslashes($_GET['selector'])) ?>">
</form>
    </td>
  </tr>
</table>
</body>
</html>