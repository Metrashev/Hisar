<?php

class CFileManPermission {

	private $rootDir;

	public function __construct($rootDir) {
		$this->rootDir = realpath($rootDir);
	}

	public function is_in_root($path) {
		if (strpos($path, '/..')!==false) return false;
		if (strpos($path, '\\..')!==false) return false;

		return (strpos($path, $this->rootDir)===0);
	}

	public function canDelete($name) {
		return $this->is_in_root($name);
	}

	public function canMove($src, $dst) {
		return $this->is_in_root($src) && $this->is_in_root($dst);
	}

	public function canRename($name) {
		return $this->is_in_root($name);
	}

	public function canCreateFile($name) {
		return $this->is_in_root($name);
	}

	public function isFileAcceptable($filename) {
		return preg_match('/\.(html?|jpe?g|gif|png|zip|xls|doc|pdf|rar|ctx)$/i', $filename);
	}

}


class CFileManPermission_Images extends CFileManPermission {

	public function isFileAcceptable($filename) {
		return preg_match('/\.(jpe?g|gif|png|bmp)$/i', $filename);
	}

}


class CFileMamager {

	/**
	 * The filesystem path to the root directory of the file manager
	 * No trailing slash
	 * @var string
	 */
	public $rootDir;

	/**
	 * The virtual path to the currently selected directory
	 * It always starts and ends with a slash
	 * @var string
	 */
	public $currentDir;

	/**
	 * The URL path corresponding to $this->rootDir
	 * Must be specified without trailing slash
	 * @var string
	 */
	public $URL_root_dir = '';

	private $filearr;
	private $dirarr;

	public $quota_limit = 100000000;
	public $quota_inuse = 0;

	private $all_folders;
	public $selected_folder;


	/**
	 * @var CFileManPermission
	 */
	public $file_permissions;


	/**
	 * @param string $root_dir
	 * @param string $current_dir
	 */
	function __construct($root_dir, $current_dir) {
		$this->rootDir = realpath($root_dir);
		$this->currentDir = self::check_dir($current_dir, $this->rootDir);
		$this->file_permissions = new CFileManPermission($this->rootDir);
	}


	/**
	 * Return the full filesystem path for the current dir
	 *
	 * @return string
	 */
	function getCurrentDir() {
		return $this->rootDir . $this->currentDir;
	}


	/**
	 * Return the URL path for the current directory
	 *
	 * @return string
	 */
	function getOffsetDir() {
		return $this->URL_root_dir . $this->currentDir;
	}


	/**
	 * Checks if the given directory can be used as currently selected
	 * Returns the actual directory that will be used
	 *
	 * @param string $dir
	 * @param string $root
	 * @return string
	 */
	static public function check_dir($dir, $root) {
		$real_dir = realpath($root.$dir);

		if (strpos($real_dir, $root)!==0) {
			$dir = '/';
		} else {
			$dir = substr($real_dir, strlen($root));
		}

		$dir = str_replace('\\', '/', $dir);

		if ($dir=='') {
			return '/';
		} else {
			return $dir . '/';
		}
	}


	/**
	 * Check if the given filename is just that
	 *
	 * @param string $file
	 * @return boolean
	 */
	function check_file($file) {
		switch (true) {
			case strlen($file)==0 :
			case $file == '.' :
			case $file == '..' :
			case $file{0}=='/' :
			case $file{0}=='\\' :
			case strpos($file, '\\')>0 :
			case strpos($file, '/')>0 :
				return false;
		}

		return true;
	}


	/**
	 * Returns the contents of the current directory as array
	 *
	 * @return array
	 */
	function getDir() {
		$this->dirarr = array();
		$this->filearr = array();
		return $this->_getdir(0, 0, new RecursiveDirectoryIterator($this->rootDir . $this->currentDir));
	}


	/**
	 * Function analogous to filesize, uses command line calls
	 * Result is in bytes
	 *
	 * @param string $path
	 * @return float
	 */
	static public function dirsize($path) {
		return 0; // remove this to enable functionality - ONLY ON LINUX WITH du INSTALLED!

		$path = escapeshellarg($path);
		$result = `du -sb {$path}`;
		preg_match('/^[0-9]+/', $result, $result);
		return (float)$result[0];
	}


	/**
	 * Returns the contents of a directory as array
	 *
	 * @param integer $dirarr_ind
	 * @param integer $filearr_ind
	 * @param RecursiveDirectoryIterator $it
	 * @return array
	 */
	private function _getdir($dirarr_ind, $filearr_ind, $it) {
		for ( ; $it->valid(); $it->next()) {

			if ($it->isDir() && !$it->isDot()) {
				if ($it->hasChildren()) {
					$this->dirarr[$dirarr_ind][0] = $it->getFilename();
					try {
						$this->dirarr[$dirarr_ind][1] = self::dirsize($it->getRealPath());
					} catch (Exception $e) {}
					$this->dirarr[$dirarr_ind++][2] = $it->getMTime();
				}
			} elseif($it->isFile()) {
				$this->filearr[$filearr_ind][0] = $it->getFilename();
				$this->filearr[$filearr_ind][1] = $it->getSize();
				$this->filearr[$filearr_ind++][2] = $it->getMTime();
			}

		}

		return array(
			'dir' => $this->dirarr,
			'file' => $this->filearr,
		);
	}


	/**
	 * Delete a directory tree
	 *
	 * @param string $dir
	 * @param integer $total_bytes_deleted
	 * @param string $status_line
	 * @return boolean
	 */
	function deltree($dir, &$total_bytes_deleted, &$status_line) {
		return $this->_deltree(
			new RecursiveDirectoryIterator($this->getFullPath($dir)),
			$total_bytes_deleted,
			$status_line);
	}


	/**
	 * @param RecursiveDirectoryIterator $it
	 * @param integer $total_bytes_deleted
	 * @param string $status_line
	 * @return boolean
	 */
	private function _deltree($it, &$total_bytes_deleted, &$status_line) {
		for ( ; $it->valid(); $it->next()) {
			if ($it->isDir() && !$it->isDot()) {

				if ($it->hasChildren()) { // delete all children
					$bleh = $it->getChildren();
					$this->_deltree($bleh, $total_bytes_deleted, $status_line);
				}

				if ($this->file_permissions->canDelete($it->getPathname())) {
					@$res = rmdir($it->getPathname());
				} else {
					$res = false;
				}

				if (!$res) {
					$status_line .= "Error deleting directory <b>".$files."</b><br>";
					return false;
				}

			} elseif($it->isFile()) {

				$fs = $it->getSize();

				if ($this->file_permissions->canDelete($it->getPathname())) {
					@$res = unlink($it->getPathname());
				} else {
					$res = false;
				}

				if (!$res) {
					$status_line .= "Error deleting file <b>".$files."</b>, please contact WebAdmin.<br>";
					return false;
				} else {
					$total_bytes_deleted += $fs;
				}
			}
		}

		return true;
	}


	/**
	 * Returns the full filesystem path for the given file
	 *
	 * @param unknown_type $filename
	 * @return unknown
	 */
	public function getFullPath($filename) {
		return $this->rootDir . $this->currentDir . $filename;
	}


	/**
	 * @param string $name
	 * @return boolean
	 */
	function removeDir($name) {
		if ($this->file_permissions->canDelete($this->getFullPath($name))) {
			@$res = rmdir($this->getFullPath($name));
			return $res;
		}

		return false;
	}


	/**
	 * @param string $name
	 * @return boolean
	 */
	function MakeDir($name) {
		if ($this->file_permissions->canCreateFile($this->getFullPath($name))) {
			@$res = mkdir($this->getFullPath($name), 0755);
			return $res;
		}

		return false;
	}


	/**
	 * @param string $name
	 * @return boolean
	 */
	function createFile($name) {
		if ($this->file_permissions->canCreateFile($this->getFullPath($name))) {
			@$res = touch($this->getFullPath($name));
			return $res;
		}

		return false;
	}


	/**
	 * @param string $filename
	 * @return boolean
	 */
	function isFile($filename) {
		return is_file($this->getFullPath($filename));
	}


	/**
	 * @param string $name
	 * @return boolean
	 */
	function isDir($name) {
		return is_dir($this->getFullPath($name));
	}


	/**
	 * @param string $old_name
	 * @param string $new_name
	 * @return boolean
	 */
	function rename($old_name, $new_name) {
		if ($this->file_permissions->canRename($this->getFullPath($new_name))) {
			@$res = rename($this->getFullPath($old_name), $this->getFullPath($new_name));
			return $res;
		}

		return false;
	}


	/**
	 * @param array $file_struct
	 * @return boolean
	 */
	function move_uploaded_file($file_struct) {
		if ($this->file_permissions->canMove($this->getFullPath($file_struct['name']), $this->getFullPath($file_struct['name']))) {
			@$res = move_uploaded_file($file_struct['tmp_name'], $this->getFullPath($file_struct['name']));
			return $res;
		}

		return false;
	}


	/**
	 * @param string $name
	 * @return boolean
	 */
	function unlinkFile($name) {
		if ($this->file_permissions->canDelete($this->getFullPath($name))) {
			@$res = unlink($this->getFullPath($name));
			return $res;
		}

		return false;
	}


	/**
	 * @param string $name
	 * @return integer
	 */
	function getFileSize($name) {
		return filesize($this->getFullPath($name));
	}


	/**
	 * @param string $src
	 * @param string $dst
	 * @return boolean
	 */
	function moveFile($src, $dst) {
		if ($this->file_permissions->canMove($this->rootDir.$src, $this->getFullPath($dst))) {
			@$res = rename($this->rootDir.$src, $this->getFullPath($dst));
			return $res;
		}

		return false;
	}


	/**
	 * @return array
	 */
	function getAllDirs() {
		$this->all_folders = array();
		$this->selected_folder = -1;

		$this->_getalldirs(0, $this->rootDir, new RecursiveDirectoryIterator($this->rootDir), "");

		return $this->all_folders;
	}


	/**
	 * @param integer $dirarr_ind
	 * @param string $cd
	 * @param RecursiveDirectoryIterator $it
	 * @param string $parent_root
	 */
	function _getalldirs($dirarr_ind, $cd, $it, $parent_root) {
		for ( ; $it->valid(); $it->next()) {
			if ($it->isDir() && !$it->isDot()) {

				$this->all_folders[] = array(
					'pid' => $dirarr_ind-1,
					'fn'  => $it->getFilename(),
					'pr'  => $parent_root . '/' . $it->getFilename(),
				);

				$c = count($this->all_folders);

				$n_cd = (substr($cd,-1)=="/" ? $cd . $it->getFilename() : $cd . '/' . $it->getFilename());

				if ($n_cd.'/'==$this->getCurrentDir()) {
					$this->selected_folder = $c;
				}

				$this->_getalldirs($c, $n_cd, new RecursiveDirectoryIterator($n_cd), $parent_root."/".$it->getFilename());
			}
		}
	}

}


class CFManInterface {

	/**
	 * @var CFileMamager
	 */
	public $file_manager;
	protected $files;
	protected $data;
	protected $cur_dir;
	protected $srtfld;

	public $status_line;
	public $sortfield;
	public $sortorder;
	public $clipboard='';
	public $resource='';

	public $img_dir='./';

	protected $icons = array(
		// Image
		'.jpeg' => 'pic.gif',
		'.jpg'  => 'pic.gif',
		'.gif'  => 'pic.gif',
		'.png'  => 'pic.gif',
		'.bmp'  => 'pic.gif',

		// Sound
		'.mp3'  => 'snd.gif',
		'.wav'  => 'snd.gif',
		'.wma'  => 'snd.gif',
		'.ogg'  => 'snd.gif',
		'.m3u'  => 'snd.gif',
		'.pls'  => 'snd.gif',
		'.mid'  => 'snd.gif',
		'.midi' => 'snd.gif',
		'.aiff' => 'snd.gif',
		'.asf'  => 'snd.gif',
		'.aac'  => 'snd.gif',
		'.m4a'  => 'snd.gif',

		// Video
		'.avi'  => 'vid.gif',
		'.wmv'  => 'vid.gif',
		'.mpeg' => 'vid.gif',
		'.mpg'  => 'vid.gif',
		'.mov'  => 'vid.gif',
		'.mkv'  => 'vid.gif',
		'.m4v'  => 'vid.gif',

		// MS Office
		'.doc'  => 'doc.gif',
		'.docx' => 'doc.gif',
		'.rtf'  => 'doc.gif',
		'.xls'  => 'xls.gif',
		'.xlsx' => 'xls.gif',
		'.ppt'  => 'ppt.gif',
		'.pptx' => 'ppt.gif',
		'.pps'  => 'ppt.gif',
		'.ppsx' => 'ppt.gif',

		'.html' => 'htm.gif',
		'.htm'  => 'htm.gif',
		'.pdf'  => 'pdf.gif',
		'.swf'  => 'swf.gif',
		'.zip'  => 'zip.gif',
		'.rar'  => 'zip.gif',
		'.cab'  => 'zip.gif',
		'.txt'  => 'txt.gif',
		'.ini'  => 'txt.gif',
		'.inf'  => 'txt.gif',
		'.xml'  => 'txt.gif',
		'.exe'  => 'exe.gif',
	);

	/* @var $file_manager CFileMamager*/

	function __construct($virtual_dir, $currentDir, $data) {
		$this->file_manager = new CFileMamager($virtual_dir, $currentDir);
		$this->cur_dir = $currentDir;
		$this->files = $data['files'];
		$this->data = $data;
		$this->clipboard = $this->data['clipboard'];
	}

	function getIconName($filename) {
		$ext = strtolower(strrchr($filename, '.'));

		if (isset($this->icons[$ext])) return $this->icons[$ext];

		return 'def.gif';
	}

	function commandButton($newfoldername, $command) {

		if (!$this->file_manager->check_file($newfoldername)) {

			$this->status_line .= "Invalid name <b>$newfoldername</b>";

		} else {

			switch ($command) {
				case 1:
					if (!$this->file_manager->MakeDir($newfoldername))
						$this->status_line .= "Error creating folder <b>$newfoldername</b>";
					break;
				case 3:
					if (!$this->file_manager->createFile($newfoldername.".html"))
						$this->status_line .= "Error creating file <b>$newfoldername</b>";
					break;
				case 2:
					if ($this->file_manager->isFile($newfoldername)) {
						$this->status_line .= "There already is a file with this name<br>";
					} elseif( $this->file_manager->isDir($newfoldername) ) {
						$this->status_line .= "There already is a folder with this name<br>";
					} elseif(!$this->file_manager->check_file($this->files[0])) {
						$this->status_line .= "Invalid name <b>".$this->files[0]."</b><br>";
					} elseif (!$this->file_manager->rename($this->files[0],$newfoldername) ) {
						$this->status_line .= "Error renaming <b>".$this->files[0]."</b>.<br>";
					}
					break;
			}

		}

		return $this->status_line;
	}

	function Upload() {
		for ($i=1;$i<=max(1,(int)$this->data['urlcount']);$i++) {
			$uf=$_FILES["userfile".$i];

			if(!$this->file_manager->check_file($uf['name']))
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
		$files = $this->data['files'];

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
		$srcdir=CFileMamager::check_dir($clipboard[0], $this->file_manager->rootDir);
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
		$dir = $this->cur_dir;
		$webdir = $this->file_manager->getOffsetDir();
		//$webdir = $base_virtual_disk_URL."/".$this->data['resource'];
		//if ($webdir[strlen($webdir)-1]=="/")  $webdir=substr($webdir,0,-1);
		//$webdir.=$dir;
		$this->sortorder = $sortorder;
		$this->sortfield = $sortfield;

		$root = $this->file_manager->rootDir;

		$array = $this->file_manager->getDir();
		$filearr = $array['file'];
		$dirarr = $array['dir'];
		$this->srtfld = $sortfield;

		$result = '';

		if ($sortorder=="asc") {
			$GLOBALS['sort_field_name'] = $this->srtfld;
			if ($filearr) usort($filearr,array('CFManInterface',"cmpasc"));
			if ($this->srtfld==1) $this->srtfld = 0;

			$GLOBALS['sort_field_name'] = $this->srtfld;

			if ($dirarr) usort($dirarr,array('CFManInterface',"cmpasc"));

			for ($j=0; $j<count($dirarr); $j++) {
				$date = $dirarr[$j][2]>0 ? date("j/M/Y H:i", $dirarr[$j][2]) : '';
				$dirarr[$j][1] = number_format($dirarr[$j][1]);
				$result .= <<<EOD
				<tr>
					<td>
						<input type="checkbox" name="files[]" value="{$dirarr[$j][0]}">
					</td>
					<td>
						<a href="#" onClick="return GoToFolder('{$dirarr[$j][0]}');">
							<img src="{$this->img_dir}folder.gif" border="0" hspace="2" align="absmiddle">{$dirarr[$j][0]}
						</a>
					</td>
					<td align="right">{$dirarr[$j][1]}&nbsp;</td>
					<td nowrap="nowrap">&nbsp;{$date}&nbsp;</td>
				</tr>
EOD;
			}

			for ($j=0; $j<count($filearr); $j++) {
				$icon = $this->getIconName($filearr[$j][0]);

				$date = date("j/M/Y H:i", $filearr[$j][2]);
				$filearr[$j][1] = number_format($filearr[$j][1]);
				$result .= <<<EOD
				<tr>
					<td>
						<input type="checkbox" name="files[]" value="{$filearr[$j][0]}" filesize="{$filearr[$j][1]}">
					</td>
					<td>
						<a href="{$webdir}{$filearr[$j][0]}" target="_blank">
							<img src="{$this->img_dir}{$icon}" border="0" hspace="2" align="absmiddle">{$filearr[$j][0]}
						</a>
					</td>
					<td align="right">{$filearr[$j][1]}&nbsp;</td>
					<td>&nbsp;$date</td>
				</tr>
EOD;
			}

		} else {

			$GLOBALS['sort_field_name'] = $this->srtfld;
			if ($filearr) usort($filearr, array('CFManInterface','cmpdesc'));
			if ($this->srtfld==1) $this->srtfld = 0;

			$GLOBALS['sort_field_name'] = $this->srtfld;
			if ($dirarr) usort($dirarr, array('CFManInterface','cmpdesc'));

			for ($j=0; $j<count($filearr); $j++) {
				$icon = $this->getIconName($filearr[$j][0]);

				$date = date("j/M/Y H:i", $filearr[$j][2]);
				$filearr[$j][1] = number_format($filearr[$j][1]);
				$result .= <<<EOD
				<tr>
					<td>
						<input type="checkbox" name="files[]" value="{$filearr[$j][0]}" filesize="{$filearr[$j][1]}">
					</td>
					<td>
						<a href="{$webdir}{$filearr[$j][0]}" target="_blank">
							<img src="{$this->img_dir}{$icon}" border="0" hspace="2" align="absmiddle">{$filearr[$j][0]}
						</a>
					</td>
					<td align="right">{$filearr[$j][1]}&nbsp;</td>
					<td>&nbsp;$date</td>
				</tr>
EOD;
			}

			for ($j=0; $j<count($dirarr); $j++) {
				$date = date("j/M/Y H:i", $dirarr[$j][2]);
				$result .= <<<EOD
				<tr>
					<td>
						<input type="checkbox" name="files[]" value="{$dirarr[$j][0]}">
					</td>
					<td>
						<a href="#" onClick="return GoToFolder('{$dirarr[$j][0]}');">
							<img src="{$this->img_dir}folder.gif" border="0" hspace="2" align="absmiddle">{$dirarr[$j][0]}
						</a>
					</td>
					<td align="right">&nbsp;</td>
					<td>&nbsp;{$date}</td>
				</tr>
EOD;
			}
		}

		return $result;
	}


	/**
	 * This is automatically called on render
	 *
	 */
	public function processCommands() {
		if ($this->data['upload']) {
			$this->Upload();
		}

		if ($this->data['delete']) {
			$this->Delete();
		}

		if ($this->data['paste']) {
			$this->Paste();
		}

		if ($this->data['commandbtn']) {
			$this->commandButton($this->data['newfoldername'], intval($this->data['command']));
		}
	}


	function render() {
		$this->processCommands();

		$sortorder=$this->data['sortorder'];
		$sortfield=$this->data['sortfield'];
		if ($sortorder!="asc" && $sortorder!="desc" )
			$sortorder=$this->data['sortorder']= "asc";
		if ($sortfield!=1 && $sortfield!=2 && $sortfield!=3)
			$sortfield=$this->data['sortfield']= 0;

		return $this->printDir($sortorder,$sortfield);
	}

}


class CFManInterface_Images extends CFManInterface {

	function getAllDirs() {
		return $this->file_manager->getAllDirs();
	}

	function getSelectedDirIndex() {
		return (int)$this->file_manager->selected_folder;
	}

	function Delete() {
		$filename = $this->data['delete'];

		if ($this->file_manager->check_file($filename) && $this->file_manager->isFile($filename)) {
			if (!$this->file_manager->unlinkFile($filename)) {
				$this->status_line.="Error deliting file <b>".$files[$i]."</b><br>";
			}
		}

	}

	function cmpasc($a, $b) {
		return strnatcasecmp($a[0], $b[0]);
	}

	function printDir($sortorder, $sortfield) {
		$dir = $this->cur_dir;
		$webdir=$this->file_manager->getOffsetDir();
	//	$webdir = $base_virtual_disk_URL."/".$this->data['resource'];
	//	if ($webdir[strlen($webdir)-1]=="/")  $webdir=substr($webdir,0,-1);
	//	$webdir.=$dir;
		$this->sortorder=$sortorder;
		$this->sortfield=$sortfield;
		$root=$this->file_manager->rootDir;
		$array=$this->file_manager->getDir();
		$filearr=$array['file'];
		$dirarr=$array['dir'];
		$this->srtfld=$sortfield;

		usort($filearr, array(__CLASS__, 'cmpasc'));
		
		foreach ($filearr as $k=>$file) {
			if (!CFileManPermission_Images::isFileAcceptable($file[0]))
				unset($filearr[$k]);
		}

		if (empty($filearr)) {
			return <<<EOD
<div style="padding-top:2px;">
No images in this folder
</div>
EOD;
		}

		$result = '';

		foreach ($filearr as $file) {
			$edit = <<<EOD
<div class="thumb_del"><a href="#" onclick="return CheckDelete(&quot;{$file[0]}&quot;)"><img src="images/delete.gif" alt="Delete" /></a></div>
EOD;

			$filename = htmlspecialchars($file[0]);

			$result .= <<<EOD
<div class="thumb_floater" style="display: none;">
<div class="image_thumb">
<div class="thumb_label">{$filename}</div>
<div class="thumb_inner" align="center">
<a href="#" onclick="TransferSelected('{$file[0]}', '{$file[1]}')"><img src="{$webdir}{$file[0]}" alt="{$file[0]}" title="{$file[0]}" class="thumb"></a>
{$edit}
</div>
</div>
</div>
EOD;
		}

		return $result;
	}

}

