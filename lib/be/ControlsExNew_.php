<?php
define('IMAGE_ORDER_UP','/be/i/sorted_up_1.gif');
define('IMAGE_ORDER_DOWN','/be/i/sorted_down_1.gif');

define('IMAGE_ORDER_UP_FIRST','/be/i/sorted_up.gif');
define('IMAGE_ORDER_DOWN_FIRST','/be/i/sorted_down.gif');

//require_once (dirname(__file__) . '/Controls.php');

class Repeater //implements IRenderTemplate
{
    public $DataSource = null;
    /* @var $DataSource DataTable*/
    public $xmlText;
    public $isFile = false;
    public $pageItemsCount;
    public $control_id;
    public $totalItems;
    protected $template = null;
    protected $prerendered = false;
    //	public $startItem;
    public $page;
    /* @var $page Page*/
    protected $xmlFile;
    public $formatFields;
    public $itemCallback = null;

    function __construct($dataSource, $xmlText, $isFile = false, /*$startItem=0,*/ $pageItemsCount =
        10 /*,$usePageBar=true*/, $totalItems = 0, $control_id = '', $page = null)
    {
        $this->DataSource = $dataSource;
        $this->pageItemsCount = (int)$pageItemsCount;
        if (!empty($this->pageItemsCount))
        {
            $this->DataSource->Limit = "limit " . $this->pageItemsCount;
        }
        else
        {
            $this->pageItemsCount = 10;
            $this->DataSource->Limit = "limit 10";
        }

        $this->usePageBar = $usePageBar;
        $this->page = $page;
        $this->startItem = $startItem;
        if (!empty($totalItems))
        {
            $this->totalItems = $totalItems;
        }
        else
        {
            if ($this->DataSource)
            {
                $this->totalItems = $this->DataSource->getCount();
            }
        }
        if (empty($control_id))
        {
            $control_id = 'repeater' . $this->createID();
        }
        else
        {
            $this->control_id = $control_id;
        }
        $this->loadXml($xmlText, $isFile);
    }

    function setFieldFormat($field, $format)
    {
        $this->formatFields[$field] = $format;
    }

    function loadXml($xml, $isFile)
    {
        if ($isFile)
        {
            $dir = $xml;
            //	$dp=strpos( $_SERVER['SCRIPT_FILENAME'],$_SERVER['SCRIPT_NAME']);
            //	$dir='';
            //	if($dp>0) {
            //		$dir=substr($_SERVER['SCRIPT_FILENAME'],0,$dp);
            //		$dir=realpath($dir.'/'.$xml);
            //	}
            //	if(empty($dir)) {
            //		throw new Exception("File <b>{$xml}</b> not Found!");
            //	}
            $this->xmlText = file_get_contents($dir);
            $this->xmlFile = $dir;
        }
        else
        {
            $this->xmlText = $xml;
        }
    }

    function createID()
    {
        if (defined('REPEATER_ID'))
        {
            $rep_id = REPEATER_ID;
            $rep_id++;

        }
        else
        {
            $rep_id = 1;
        }
        define('REPEATER_ID', $rep_id);
        return $rep_id;
    }

    function PreRender()
    {
        if (!$this->prerendered)
        {
            if (!$this->DataSource->isLoaded)
            {
                $this->DataSource->BuildQuery();
            }
            $this->prerendered = true;
        }
    }

    function renderBody()
    {
        //		$p=Profiler::getInstance();
        /* @var $p Profiler*/
        $keys = null;
        $str = '';

        foreach ($this->DataSource->Rows as $k => $v)
        {
            $arr = array();
            $setKeys = empty($keys);
            foreach ($v as $mk => $mv)
            {
                if ($setKeys)
                {
                    $keys[] = '$' . $mk;
                }
                if (isset($this->formatFields[$mk]))
                {
                    $arr[$mk] = FormatUtils::translateFormat($this->formatFields[$mk], $mv);
                }
                else
                {
                    $arr[$mk] = $mv;
                }
            }
            //			$i=$p->createMark('l1');
            //$val=strtr($this->xmlText,$v);
            if (isset($this->itemCallback))
            {
                call_user_func_array($this->itemCallback, array(&$keys, &$arr));
                //	echo "<pre>";
                //	print_r($arr);
                //	echo "</pre>";
            }
            $str .= str_replace($keys, $arr, $this->xmlText);
            //	extract($v);
            //----
            //	ob_start();
            //	include($this->xmlFile);
            //	$val=ob_get_clean();
            //-----
            //	eval("\$val=\"$this->xmlText\";");
            //			$p->closeMark($i);
            //			$p->dump();
            //			echo $val;
            //			echo "<br>";
        }
        return $str;
    }

    function render()
    {
        //		if($this->parentPage)
        $this->PreRender();
        if ($this->page)
        {
            $this->page->importHTML($this->renderBody());
        }
        else
        {
            return $this->renderBody();
        }

    }

    function renderEvents()
    {
        //empty
    }

    public function OnEvent($event_arg, $event_name)
    {
        //empty
    }

}


class CControler
{
    public $viewId;
    public $id;

    function __construct($viewId, $id)
    {
        $this->id = $id;
        $this->viewId = $viewId;
    }

    function getReadOnly($html, $data = '')
    {
        $f = new FormProcessor();
        $f->loadTemplate($html);
        //	if(empty($data)) {
        //		$data=$_POST;
        //	}
        //	$f->fillData($data);
        return $f->getReadOnlyVersion();
    }

    function getExcel($html)
    {

    }

    /***************************/

}

class DataViewTable //extends Table implements IRenderTemplate
{
    public $DataGrids;

    function __construct()
    {

    }

    public function render()
    {
        //	foreach ()
    }
    public function PreRender()
    {
    }
    public function renderEvents()
    {
    }
    public function OnEvent($event_arg, $event_name)
    {
    }
}

class Definer
{
    public static $constants = array();

    function set($name, $value)
    {
        self::$constants[$name] = $value;
    }

    function get($name)
    {
        return self::$constants[$name];
    }
}

class xmlPatcher
{

    function pickAttributes($node)
    {
        $attr = array();
        foreach ($node->attributes() as $k => $v)
        {
            $attr[(string )$k] = (string )$v;
        }
        return $attr;
    }

    function setNode(&$array, $node, $is_controls = true)
    {
    
        foreach ($node as $k => $v)
        {
            //	var_dump($v);
            //	echo "<br>";

            $kk = (string )$k;
            $vv = (string )$v;
            switch ($kk)
            {
                case 'user_func':
                    {
                        if (!empty($vv))
                        {
                            $array['user_func'] = array((string )$v['class'], $vv);
                        }
                        break;
                    }
                case 'autoload':
                    {
                        if (!empty($vv))
                        {
                            $array['autoload'] = array((string )$v['type'], $vv, $v['extra']);
                        }
                        break;
                    }
                case 'itti_col':
                    {
                        foreach ($v->attributes() as $ak => $av)
                        {
                            $array['attributes'][(string )$ak] = (string )$av;
                        }
                        //	if(!is_array($array['col'])) {
                        //		$array['col']=array();
                        //	}
                        self::setNode($array, $v, false);
                        break;
                    }
                case 'itti_attr':
                    {
                        $attributes = self::pickAttributes($v);
                        if (!empty($vv))
                        {
                            $array['datasource'] = $vv;
                        }
                        $array += $attributes;

                        break;
                    }
                default:
                    {
                        if (class_exists($kk, false))
                        {
                            $cn = $is_controls ? 'controls':'control';
                            if (!is_array($array[$cn]))
                            {
                                $array[$cn] = array();
                            }
                            if ($is_controls)
                            {

                                $ind = count($array[$cn]);
                                $array[$cn][$ind] = array($kk => array());

                                self::setNode($array[$cn][$ind][$kk], $v);

                                foreach ($v->attributes() as $ak => $av)
                                {
                                    $array[$cn][$ind][$kk]['attributes'][(string )$ak] = (string )$av;
                                }

                            }
                            else
                            {
                                $array[$cn][$kk] = array();
                                self::setNode($array[$cn][$kk], $v);
                                foreach ($v->attributes() as $ak => $av)
                                {
                                    $array[$cn][$kk]['attributes'][(string )$ak] = (string )$av;
                                }
                            }
                        }
                        else
                        {
                            $array[$kk] = $vv;
                        }
                        break;
                    }
            }
        }
    }

    function createArray($xml)
    {
    	
        $array = array();
        ob_start();
        include ($xml);
        $xml = ob_get_clean();
        $sxe = simplexml_load_string($xml);
        
        $array = array();
        foreach ($sxe as $field_name => $fields)
        {
            $array[(string )$field_name] = array();
            $array[(string )$field_name]['header'] = array();
            $array[(string )$field_name]['col'] = array();
            
            self::setNode($array[(string )$field_name]['header'], $fields->header[0]);
            foreach ($fields->header[0]->attributes() as $ak => $av)
            {
                $array[(string )$field_name]['header']['attributes'][(string )$ak] = (string )$av;
            }
            self::setNode($array[(string )$field_name]['col'], $fields->col[0]);
            //	$array['header']['caption']=$fields->caption[0];
            //	$array['header']['user_func']=$fields->caption[0];
            //	echo (string)$field_name;
            //	echo '-'.;
            //	echo "<br>";


        }
        return $array;
    }
}

class ControlWriter
{
    static function Write($table, $data, $id, $idKey = 'id')
    {
        $db = getdb();
        /* @var $db CDB */
        if (empty($id))
        {
            $q = array_fill(0, count($data), "?");
            $SQL = "INSERT INTO {$table} (`" . implode("`,`", array_keys($data)) .
                "`) values (" . implode(",", $q) . ")";
            $db->execute($SQL, $data);
            $id = $db->get_id();
        }
        else
        {
            $SQL = "UPDATE {$table} SET `" . implode("`=?,`", array_keys($data)) .
                "`=? where `{$idKey}`='{$id}'";
            $db->Execute($SQL, $data);
        }
        return $id;
    }


}

class CClientControlRepearer
{
    protected $control_id;
    public $template;

    function __construct($control_id, $template)
    {
        $this->control_id = $control_id;
        $this->template = $template;
    }

    function renderScript($extraText = '')
    {
        $str = "<script>";
        $str .= "var {$this->control_id}_i=1;";
        $str .= "var {$this->control_id}_template='<span id=\"itti_{$this->control_id}_\'+i+\'\">" .
            addslashes($this->template) . "</span>'";
        $str .= "</script>";
        if (!defined('ClientControlRepearerScript'))
        {
            $str .= <<< EOD
		<script>
		function br_onclick(control_id) {
				var i=0;
				eval("i="+control_id+"_i;");
				if (i<30) {
				var j=i;
				i++;
				var template='';
				eval("template="+control_id+"_template;");
				eval("template='"+template+"';");
				eval(control_id+"_i=i;");
				var obj=document.getElementById("itti_"+control_id+'_'+j);
				if(obj.insertAdjacentHTML) {
					obj.insertAdjacentHTML("AfterEnd",template);
				}
				else {
					var r = obj.ownerDocument.createRange();
					r.setStartBefore(obj);
					var parsedHTML = r.createContextualFragment(template);
					if (obj.nextSibling) 
						obj.parentNode.insertBefore(parsedHTML,obj.nextSibling);
					else 
						obj.parentNode.appendChild(parsedHTML);
				}
				{$extraText}
			}
		}
		function bl_onclick(control_id) {
			var i=0;
			eval("i="+control_id+"_i;");
			if (i>1) {
				var j=i;
				i--;
				eval(control_id+"_i=i;");
				var obj=document.getElementById('itti_'+control_id+'_'+j);
				obj.parentNode.removeChild(obj);
				{$extraText}
			}
		}
		</script>
EOD;
            define('ClientControlRepearerScript', 1);
        }
        return $str;
    }


}

class CClientFileUploader extends CClientControlRepearer
{
    public $layout = '';
    public $commonName = '';
    public $saveDirectory = '';
    public $controlName = '';

    public $refTable = '';
    public $refID = 0;
    public $files_table = 'files';
    public $access_level = 0;

    public $uploaded_ids = array();


    function __construct($control_id, $template, $layout_file = '', $controlName =
        '')
    {
        parent::__construct($control_id, $template);
        $this->controlName = $controlName;
        if (!empty($layout_file))
        {
            $this->loadLayout($layout_file);
        }

    }

    function loadLayout($filename)
    {
        if (file_exists($filename))
        {
            $str = file_get_contents($filename);
        }
        $this->layout = str_replace("_#ID#_", $this->control_id, $str);
    }

    function uploadFiles($files)
    {
        if (empty($this->files_table))
            return;
        $db = getdb();
        /* @var $db CDB*/
        $this->uploaded_ids = array();
        $r_id = empty($this->refTable) ? 0:(int)$this->refID;

        foreach ($files[$this->controlName][name] as $k => $v)
        {
            if ($files[$this->controlName]['error'][$k] == 0)
            {
                if (is_uploaded_file($files[$this->controlName]['tmp_name'][$k]))
                {
                    $str = file_get_contents($files[$this->controlName]['tmp_name'][$k]);
                }
                else
                {
                    continue;
                }
                //	$db->Execute("insert into files (file_content) values(?)",array($str));
                $db->Execute("insert into {$this->files_table} (name,content_type,size,last_modified,ref_table,
				ref_record_id,file_content,access_level) 
				values(?,?,?,now(),?,
				?,?,?)", array($v, $files[$this->controlName]['type'][$k], $files[$this->
                    controlName]['size'][$k], (string )$this->refTable, $r_id, $str, (int)$this->
                    access_level));
                $this->uploaded_ids[] = $db->get_id();
            }
        }
        return $this->uploaded_ids;
    }

    private function getUploadedFiles($postData)
    {
        if (empty($postData))
        {
            $postData = $_POST;
        }
        if (isset($postData["hd_ids_{$this->control_id}"]) && !empty($postData["hd_ids_{$this->control_id}"]))
        {
            $ids = explode(",", $postData["hd_ids_{$this->control_id}"]);
            $this->uploaded_ids = array_merge($ids, $this->uploaded_ids);
        }
    }

    function cleanBaseTempRecords($time_tolerance = '1 hour')
    {
        if (!empty($this->files_table))
        {
            $db = getdb();
            /* @var $db CDB*/
            $where = array();
            if (!empty($time_tolerance))
            {
                $where[] = "last_modified+interval {$time_tolerance}<now()";
            }
            if (!empty($this->refTable))
            {
                $where[] = "ref_table='{$this->refTable}'";
            }
            $str_where = empty($where) ? '':' and ' . implode(" AND ", $where);
            $db->execute("delete from {$this->files_table} where ref_record_id=0 {$str_where}");
            return $db->getAffectedRows();
        }
        return 0;
    }

    function renderUploadedFiles($template = '', $useDelete = true)
    {
        if (empty($this->uploaded_ids) || empty($this->files_table))
            return '';
        $hidden = '';
        if (empty($template))
        {
            $colspan = 1;
            //
            if ($useDelete)
            {
                $str_delete = "<td><a href='#' onclick=\"document.getElementById('_#DELID#_').value='_#ID#_';getForm(this).submit();\">Delete</a></td>";
                $colspan = 2;
            }
            $template = "<tr>
			<td><a href='/be/fileProcessor.php?id=_#ID#_' target='_blank'>_#NAME#_</a></td>
			{$str_delete}
			</tr>";
            if ($useDelete)
            {
                $hidden = "<tr><td colspan='{$colspan}'><input type='hidden' name='_#DELID#_' id='_#DELID#_' value='' style='display:none' /></td></tr>";
            }
        }
        $str_template = '';
        $db = getdb();
        foreach ($this->uploaded_ids as $v)
        {
            $row = $db->getrow("select * from {$this->files_table} where id='{$v}' ");
            $replace = array('_#DELID#_' => "hd_del_{$this->control_id}", '_#ID#_' => (int)
                $v, '_#NAME#_' => $row['name'], );
            $str_template .= str_replace(array_keys($replace), $replace, $template);
        }
        //return $str_template.str_replace("_#DELID#_","hd_del_{$this->control_id}",$hidden);
        return $str_template;
    }

    function deleteById($id)
    {
        if (empty($id) || empty($this->files_table))
            return;
        $db = getdb();
        $db->execute("delete from {$this->files_table} where id='{$id}'");
        $key = array_search($id, $this->uploaded_ids);
        if ($key !== false)
            unset($this->uploaded_ids[$key]);
    }



    function render($postData = array(), $onUploadFunc = '', $includeForm = true, $includeExtraScript = true)
    {
        if (isset($postData["upload_{$this->control_id}"]))
        {
            if (!empty($onUploadFunc))
            {
                call_user_func($onUploadFunc, $_FILES);
            }
            else
            {
                $this->uploadFiles($_FILES);
            }
        }
        $this->getUploadedFiles($postData);

        if ((int)$postData["hd_del_{$this->control_id}"])
        {
            $this->deleteById((int)$postData["hd_del_{$this->control_id}"]);
        }

        $str = $this->renderScript($includeExtraScript ? 'document.getElementById("cf_' .
            $this->control_id . '").innerHTML=i;document.getElementById(\'urlcount_' . $this->
            control_id . '\').value=i;':'');
        if ($includeForm)
        {
            $str .= "<form method='post' enctype='multipart/form-data' action='{$_SERVER['REQUEST_URI']}' name='form_{$this->control_id}' >";
        }

        $str .= $this->layout;
        $str .= "<input type='hidden' value='" . implode(",", $this->uploaded_ids) .
            "' id='hd_ids_{$this->control_id}' name='hd_ids_{$this->control_id}' />";
        if ($includeForm)
        {
            $str .= "</form>";
        }
        return $str;
    }
}

class ExcelExporter
{
    static function export($array, $col_offset = 0, $title = '', $filename = '')
    {
        $workbook = new Spreadsheet_Excel_Writer();
        $workbook->setVersion(8);

        $format_title = &$workbook->addFormat();
        $format_title->setBold();

        $format_red = &$workbook->addFormat();

        // Creating a worksheet
        $worksheet = &$workbook->addWorksheet($t_title);
        $worksheet->setInputEncoding('UTF-8');

        $worksheet->write(0, 0, $title, $format_title);

        $c_row = 0;
        foreach ($array as $k => $v)
        {
            $col = 0;
            foreach ($v['header'] as $hv)
            {
                $worksheet->write($c_row, $col, $hv, $format_title);
                $col++;
            }
            $c_row++;
            $f_index = 0;
            foreach ($v['rows'] as $r_row)
            {
                foreach ($r_row as $rk => $rv)
                {
                    $worksheet->write($c_row, $rk + $col_offset, $rv);
                }
                $c_row++;
            }
            $c_row++;
        }
        if (!empty($filename))
        {
            $workbook->send($filename);
        }
        $workbook->close();
    }
}

class DataGridNew
{
    public $control_id;
    public $page_size;
    public $old_page;
    public $currentPage;
    public $OrderFields = array();
    public $currentorder = array();
    public $DataSource;

    public $data_unique_field = "id";

    public $postData = array();

    public $masterControls = array();
    public $template;

    public $candrag = false;
    
    public $attributes=array();
    
    public $hasExcelExport=false;
    public $hasPrint=false;
    public $is_excel_render=false;
    public $is_print_render=false;
    public $excel_options=false;
    public $excel_matrix=array();
    public $matrix_header=array();
    
    public $use_ajax=false;

    public $styleNames = array(0 => "tr_norRow", 1 => "tr_altRow", 2 => "tr_overRow");

    

    function createFromArray($array, $renderEvents = false)
    {
        if (isset($array['uf_par']) && is_array($array['uf_par']))
        {
            $renderEvents = $array['uf_par']['renderEvents'];
            $array = $array['uf_par']['array'];
        }
        
        
        $dt = $array['DataTable'];
        $this->DataSource = new DataTable();
        $this->DataSource->Table = $dt['table'];
        $this->DataSource->CountExpresion = $dt['count_expression'];
        $this->DataSource->CountRows = $dt['count_rows'];
        $this->DataSource->CustomSelect = $dt['custom_select'];

        $this->DataSource->SelectFields = $dt['fields'];

        
        if (isset($dt['order_fields']))
        {
        	if(is_array($dt['order_fields'])) {
            	$this->DataSource->OrderFields = $dt['order_fields'];
            	//$this->setOrder($this->DataSource->OrderFields);
        	}
        	else {
        		if(trim($dt['order_fields'])!="") {
        			$this->DataSource->OrderFields = array($dt['order_fields']);        			
        		}
        	}
        }
        if (isset($dt['group_by']) && !empty($dt['group_by']))
        {
            $this->DataSource->GroupFields = explode(",", $dt['group_by']);
            array_filter($this->DataSource->GroupFields);
        }
        if (isset($dt['with_rollup']) && $dt['with_rollup'])
        {
            $this->DataSource->hasRollUp = true;
        }
        if (isset($dt['where']) && !empty($dt['where']))
        {
            $this->DataSource->AddWhere($dt['where'], '', '');
        }
        if (isset($array['page_size']))
        {
            $this->page_size = intval($array['page_size']);
        }
        if (isset($array['hasExcelExport']) && $array['hasExcelExport'])
        {
            $this->hasExcelExport = true;
        }
        if (isset($array['hasPrint']) && $array['hasPrint'])
        {
            $this->hasPrint = true;
        }
        if(isset($array["excel_options"])) {
        	$this->excel_options=$array["excel_options"];
        }
        if (isset($array['OnExcelRender']) && !empty($array['OnExcelRender']))
        {
            $this->OnExcelRender = $array['OnExcelRender'];
        }//pivotData
        if (isset($array['pivotData']) && !empty($array['pivotData']))
        {
            $this->pivotData = $array['pivotData'];
        }
        
        // AJAX
        $this->use_ajax=(int)$array['use_ajax'];
        
        if ($this->page_size > 0)
        {
            $this->DataSource->Limit = "limit " . $this->page_size * $this->currentPage .
                ',' . $this->page_size;
        }
        if (isset($array['OnItemDataBound']) && !empty($array['OnItemDataBound']))
        {
            $this->OnItemDataBound = $array['OnItemDataBound'];
        }
        if (isset($array['OnBeforeItemDataBound']) && !empty($array['OnBeforeItemDataBound']))
        {
            $this->OnBeforeItemDataBound = $array['OnBeforeItemDataBound'];
        }
        if (isset($array['OnOrderChange']) && !empty($array['OnOrderChange']))
        {
            $this->OnOrderChange = $array['OnOrderChange'];
        }
        /*$columns = $array['columns'];
        foreach ($columns as $val)
        {
            $header = $val['header'];
            $col = $val['col'];
            if (!is_array($header) || !is_array($col))
                continue;
            $bf = new BoundField();
            
            $bf->Header->Caption = $header['caption'];
            if (isset($header['orderable']))
            {
                $bf->Header->orderable = $header['orderable'];
            }
            $bf->Header->order_field = $header['order_field'];
            $bf->Header->attributes = $header['attributes'];
            if (!empty($header['controls']) && is_array($header['controls']))
            {
                foreach ($header['controls'] as $ck => $control)
                {
                    $bf->Header->Control = $this->createControl($control);
                    break;
                }
            }
            if (!empty($header['user_func']))
            {
                $bf->Header->userFunc = $header['user_func'];
            }
            $bf->DataSouceValue = $col['datasource'];
            if (!empty($col['datatype']))
            {
                $bf->setDataSourceType($col['datatype']);
            }
            if (!empty($col['format_string']))
            {
                $bf->FormatString = $col['format_string'];
            }
            if (!empty($col['unique_field']))
            {
                $bf->unique_field = $col['unique_field'];
            }
            $bf->attributes = $col['attributes'];
            //	if(is_array($col['control'])) {
            //		$bf->ControlInstance['realControl']=$this->createControl($col['control']);
            //	}
            //	else {
            $bf->ControlInstance = $col['control'];
            //	}
            $bf->ControlAttributes = $col['control_attributes'];
            if (!empty($col['user_func']))
            {
                $bf->userFunc = $col['user_func'];
            }
            if (!empty($col['autoload']))
            {
                $bf->autoLoad = $col['autoload'];
            }
            $this->AddField($bf);
        }*/
        /*if ($renderEvents)
        {
            if (isPostback)
            {
            	
                //	$this->DataSource->OrderFields=array();
                //	$this->renderEvents($this->DataSource);
            }
            else
            {
                //$this->setOrder($this->parseOrderString($array['DataTable']['order_fields'],null));
                //$this->DataSource->OrderFields=array($array['DataTable']['order_fields']);
                echo $os = $this->parseOrderString($array['DataTable']['order_fields'], $this->
                    DataSource->columns);
                $this->setOrder($os);
                if (is_array($array['DataTable']['order_fields']))
                {
                    $this->DataSource->OrderFields = $array['DataTable']['order_fields'];
                }
                else
                {
                    $this->DataSource->OrderFields = array($array['DataTable']['order_fields']);
                }
            }
        }
        else*/
        {
            $this->commonSetOrder($this->DataSource);
        }
        /*if($this->hasExcelExport) {
        $div=new Div();
        $div->_setAttribute('align','right');
        $div->_setAttribute('style','padding:0px 5px 5px 5px;');
        $a=new Input($this->control_id.'_xls',$this->control_id.'_xls','submit','Download');
        $div->Add($a);
        $this->Add($div);
        
        }*/
    }


    function __construct($control_id, $template = '', $page_size = 25, $postData =
        array())
    {
    	
        $this->control_id = $control_id;
        $this->page_size = $page_size;
        $this->Order_fields = array();
        $this->template = $template;
        $this->readOrder();
        $this->loadCurrentPage();
        $this->postData = $postData;
    }

    public function readOrder($new_order=null)
    {
    	if(!is_null($new_order)) {
    		$_POST[$this->control_id]['neworder']=$new_order;
    	}
        $this->currentorder = $_POST[$this->control_id]['neworder'];
        $a = explode(",", $this->currentorder);
        $this->currentorder = array();
        foreach ($a as $v)
        {
        	$v=trim($v);
            if ($v == '')
                continue;
            switch ($v[0])
            {
            case "+":
                {
                        $this->currentorder[substr($v, 1)] = 'ASC';
                        break;
                }
            case "-":
                {
                    $this->currentorder[substr($v, 1)] = 'DESC';
                    break;
                }
            case '=':
                {
                    $t = substr($v, 1);
                    if (isset($this->controls[$t]))
                    {
                        unset($this->currentorder[$t]);
                        break;
                    }
                }
            case '^':
            case '*':
                {
                	$t = substr($v, 1);
                    if (isset($this->currentorder[$t]))
                    {
                        switch ($this->currentorder[$t])
                        {
                            case 'ASC':
                                {
                                    $this->currentorder[$t] = 'DESC';
                                    break;
                                }
                            case 'DESC':
                                {
                                    if ($v[0] == '^')
                                        unset($this->currentorder);
                                    else
                                        unset($this->currentorder[$t]);
                                    break;
                                }
                        }
                    }
                    else
                    {
                        $this->currentorder[$t] = 'ASC';
                    }
                    if ($v[0] == '^')
                    {
                        if ($this->currentorder[$t] != '')
                            $this->currentorder = array($t => $this->currentorder[$t]);
                    }
                    break;
                }
                default: {
                	$this->currentorder[$v] = 'ASC';
                }
            }
        }
    }

    public function getOutputOrder()
    {
        if (is_array($this->currentorder) && !empty($this->currentorder))
        {
            $o_val = array();
            foreach ($this->currentorder as $k => $v)
            {
                if ($v == 'ASC')
                {
                    $o_val[] = "+{$k}";
                }
                if ($v == 'DESC')
                {
                    $o_val[] = "-{$k}";
                }
            }
            $o_val = implode(",", $o_val);
        }
        else
        {
            $o_val = '';
        }
        return $o_val;
    }

    function parseOrderString($str_order)
    {
        if (empty($str_order))
            return array();
        $s = explode(",", $str_order);
        $array = array();
        foreach ($s as $v)
        {
            $v = trim($v);
            $k = strpos($v, " ");
            if ($k === false)
            {
                $f = $v;
                $k = 'ASC';
            }
            else
            {
                $f = trim(substr($v, 0, $k));
                $k = trim(substr($v, $k));
                $k = strtoupper($k);
            }
            //	if(isset($columns[$f])) {
            $array[$f] = $k;
            //	}
        }
        return $array;
    }

    function setOrder($order_array)
    {
        $this->currentorder = $order_array;
    }

    function getOrderString()
    {
        if (!empty($this->currentorder))
        {
            return Control_Utils::getGetString($this->currentorder, ",", '', ENC_NONE, ' ');
        }
        return '';
    }

    function commonSetOrder(&$dt)
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST")
        {
            $order = $this->getOrderString();
            if ($order != '')
            {
                $dt->OrderFields = array($order);
            }
        }
    }

    function getCurrentPage()
    {
        return $this->currentPage;
        
    }

    function setCurrentPage($cur_page)
    {
        $this->currentPage = $cur_page;
         $this->DataSource->Limit = "limit " . $this->page_size * $this->currentPage .
               ',' . $this->page_size;
    }

    function loadCurrentPage()
    {
        $this->setCurrentPage(intval($_POST[$this->control_id]['page']));
        $this->old_page = $this->currentPage;
    }

    function getPageSize()
    {
        return $this->page_size;
    }

    private function getImage($field, &$fo)
    {
        $img_src = '';
        if ($this->currentorder[$field] == 'ASC')
        {
            if ($fo != '' && $fo == strtolower($field))
            {
                $img_src = IMAGE_ORDER_DOWN_FIRST;
                $fo = '';
            }
            else
            {
                $img_src = IMAGE_ORDER_DOWN;
            }
        }
        if ($this->currentorder[$field] == 'DESC')
        {
            if ($fo != '' && $fo == strtolower($field))
            {
                $img_src = IMAGE_ORDER_UP_FIRST;
                $fo = '';
            }
            else
            {
                $img_src = IMAGE_ORDER_UP;
            }
        }
        return $img_src;
    }
    
    function getTdText($td) {
    	$str=array();
    	if(!$td) {
    		return "";
    	}
    	if($td->hasChildNodes()) {
    		foreach ($td->childNodes as $v) {
    			
    			if($v->tagName=="img") {
    				continue;    				
    			}
    			$str[]=$this->getTdText($v);
    		}
    	}
    	else {
    		return $td->nodeValue;
    	}
    	return implode('',$str);
    }

    function renderHead(&$dom)
    {
    	
        $xp = new DOMXPath($dom);
        $thead_a = $xp->query("//thead/tr/td/a");
        /* @var $thead_a DomNodeList */
        if (is_array($this->currentorder))
        {
            $fo_a = array_keys($this->currentorder);
            $fo = strtolower($fo_a[0]);
        }
        if($this->is_excel_render) {
        	$this->matrix_header=array();
        }
        if ($thead_a && $thead_a->length)
        {
            foreach ($thead_a as $v)
            {
            	
                if ($v->hasAttribute('order'))
                {
                    $fld = $v->getAttribute("order");
                    if(is_array($fo_a)) {
	                    $sort_index=array_search($fld,$fo_a);
	                    if($sort_index===null||$sort_index===false) {
	                    	$sort_index=null;
	                    }
                    }
                    $src = $this->getImage($fld, $fo);
                    $v->setAttribute("onclick", '_table_setOrder(this,"' . $this->control_id .
                        "[neworder]" . '","' . $v->getAttribute('order') . '")');
                    $v->setAttribute("href", "#");
                    $v->removeAttribute("order");
                    if (!empty($src))
                    {
                        $img = new DOMElement("img");
                        /* @var $v DOMElement */
                        //$v->insertBefore($img,$v->firstChild);
                        $v->appendChild($img);
                        $img->setAttribute("border", "0");
                        $img->setAttribute("src", $src);
                        
                        $ho="";
                        if(!is_null($sort_index)) {
                        	$sort_index=(int)$sort_index+1;
                        	$sup=new DOMElement("sup");
                        	$v->appendChild($sup);
                        	$sup->nodeValue=$sort_index;
                        	if($sort_index>1) {
                        		$ho=2;
                        	}
                        }
                        
                        $parent=$v->parentNode;
                        if($parent) {
                        	$parent->setAttribute("class","header_ord{$ho}");
                        }
                    }
                }
            }
        }
        $thead_td = $xp->query("//thead/tr/td");
        if ($thead_td && $thead_td->length)
        {
            $td_index = 0;
            foreach ($thead_td as $v)
            {
            	if($this->is_excel_render) {
            		
            		if($this->is_excel_render) {
	            		if(is_array($this->excel_options)&&is_array($this->excel_options['skip_columns'])) {
	            			$field_name=$v->getAttribute("field_name");
	            			if(!in_array($field_name,$this->excel_options['skip_columns'])) {
	            				$this->matrix_header[]=$this->getTdText($v);
	            			}
	            		}
						else {
	            			$this->matrix_header[]=$this->getTdText($v);
	            		}
	            	}
            	}
            	if ($v->hasAttribute('candrag'))
                {
                    $this->candrag = true;
                    if ($v->hasAttribute("id"))
                    {
                        $v->setAttribute("id", $this->control_id . '_' . $v->getAttribute("id"));
                    }
                    else
                    {
                        $v->setAttribute("id", $this->control_id . '_td' . $td_index);
                    }
                }
                $td_index++;
            }
        }
    }

    function replaceSpecialConstants($string, $index, $value, $escate = false)
    {
        $rep_index = $this->page_size * $this->currentPage + $index;
        $arr = array(
			'_#INDEX#_' => $escate ? mysql_real_escape_string($rep_index):$rep_index,
            '_#VAL#_' => $escate ? mysql_real_escape_string($value):$value, 
			'_#UNIQUE#_' =>$this->DataSource->Rows[$index][$this->data_unique_field],
            '_#CONTROL#_' => $this->control_id,
        	'_#ID#_'=> $this->DataSource->Rows[$index]['id'],
        );

        return str_replace(array_keys($arr), $arr, $string);
    }

    function replaceAttributes(&$node, $index, $value)
    {
        /* @var $node DOMElement*/

        if ($node->hasAttributes())
        {
            foreach ($node->attributes as $name => $attr)
            {
            	
                $attr->nodeValue = $this->replaceSpecialConstants(htmlspecialchars($attr->nodeValue), $index, $value);
            }
        }
    }

    function setValue(&$node)
    {
        $name = $node->getAttribute("name");
        $value = Control_Utils::getPostArray($name, $this->postData);
        $tagName = (string )$node->tagName;
        $tagName = strtolower($tagName);


        switch ($tagName)
        {
            case "input":
                {
                    $type = "text";
                    if ($node->hasAttribute("type"))
                    {
                        $type = (string )$node->getAttribute("type");
                        $type = strtolower($type);
                    }
                    switch ($type)
                    {
                    	case "hidden":
	                    case "text":
	                        {
	
	                                $node->setAttribute("value", $value);
	                                break;
	                        }
	                    case "checkbox":
	                        {
	                            if ((string )$value !== "")
	                            {
	                                $node->setAttribute("checked", "checked");
	                            }
	                            break;
	                        }
	                    case "radio":
	                        {
	                            if ($node->hasAttribute("value"))
	                            {
	                                $val = (string )$node->getAttribute("value");
	                                if ($val === (string )$value)
	                                {
	                                    $node->setAttribute("checked", "checked");
	                                }
	                            }
	                            break;
	                        }
	                    }
	                    break;
                }
            case "textarea":
                {

                    $node->nodeValue = htmlspecialchars($value);
                    break;
                }
            case "select":
                {
                    if ($node->hasChildNodes())
                    {
                        foreach ($node->childNodes as $child)
                        {
                            if ($child instanceof DOMText)
                            {
                                continue;
                            }
                            if ($child->hasAttribute("value"))
                            {
                                $val = (string )$child->getAttribute("value");
                                if (is_string($value))
                                {
                                    if ((string )$val === (string )$value)
                                    {
                                        $child->setAttribute("selected", "selected");
                                        break;
                                    }
                                }
                                if (is_array($value))
                                {
                                    $i = array_search($val, $value);
                                    if ($i !== false)
                                    {
                                        $child->setAttribute("selected", "selected");
                                        unset($value[$i]);
                                        if (empty($value))
                                        {
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    break;
                }
        }

    }

    private function processChildren(&$node, &$dom, $index)
    {
    	
    	
        foreach ($node->childNodes as $ch)
        {
            if ($ch instanceof DOMText)
            {
                continue;
            }
            $fields = array("field_name" => "", "userfunc" => "", "class" => "", "sql" => "",
                "arrayname" => "", "format" => "","multi_sql"=>'',"multi_arrayname"=>'' );
            foreach ($fields as $k => &$v)
            {

                if ($ch->hasAttribute($k))
                {
                    $v = $ch->getAttribute($k);
                }
            }
            $value = "";
            
        	if($this->OnBeforeItemDataBound) {
		       	call_user_func($this->OnBeforeItemDataBound,-1,$node,$this->DataSource,$ch,$this);
			}
			$encode_chars=$ch->hasAttribute("encode_chars");
            
            if (isset($this->DataSource->Rows[$index][$fields['field_name']]))
            {
                if (((string )$ch->nodeValue) !== "")
                {
                	$value = $this->replaceSpecialConstants($ch->nodeValue, $index, $this->
                        DataSource->Rows[$index][$fields['field_name']]);
                }
                else
                {
                    $value = $this->DataSource->Rows[$index][$fields['field_name']];
                }
            }
            if (!empty($fields['userfunc']))
            {
            	$value = (string )call_user_func(array($fields['class'], $fields['userfunc']),
                    array($this->DataSource->Rows[$index][$fields['field_name']], $index, $this, $ch,$dom));
            }
       		 if (!empty($fields["multi_sql"]))
            {
            	if(!empty($this->DataSource->Rows[$index][$fields['field_name']])) {
	                $db = getdb();
	
	               	$sql = $this->replaceSpecialConstants($fields["multi_sql"], $index, $this->DataSource->Rows[$index][$fields['field_name']]);
	                try {
	                $value = $db->getcol($sql);
	                }
	                catch(Exception $e) {
	                	$value=array();
	                }
	                $value=implode(', ',$value);
            	}
            	else {
            		$value="";
            	}
                
            }
            if (!empty($fields["sql"]))
            {
                $db = getdb();
                $sql = $this->replaceSpecialConstants($fields["sql"], $index, $this->DataSource->
                    Rows[$index][$fields['field_name']]);
                $value = (string )$db->getone($sql);
            }
            if (!empty($fields['arrayname']))
            {
                if (isset($GLOBALS[$fields['arrayname']]))
                {
                    $value = (string )$GLOBALS[$fields['arrayname']][$this->DataSource->Rows[$index][$fields['field_name']]];
                }
                else
                {
                    @$arr = unserialize($fields['arrayname']);
                    $value = (string )$arr[$this->DataSource->Rows[$index][$fields['field_name']]];
                }
            }
			 if (!empty($fields['multi_arrayname']))
            {
            	if (isset($GLOBALS[$fields['multi_arrayname']]))
                {
                	$values=explode(',',$this->DataSource->Rows[$index][$fields['field_name']]);
                	$value=array();
                	foreach ($values as $vv) {
                		if(isset($GLOBALS[$fields['multi_arrayname']][$vv])) {
                			$value[]=$GLOBALS[$fields['multi_arrayname']][$vv];
                		}
                	}
                    $value=implode(", ",$value);
                }
                
            }
            if (!empty($fields['format']))
            {
                $value = FormatUtils::translateFormat($fields["format"], $value);
            }
            if ($ch->tagName == "ITTI")
            {
                $par = $ch->parentNode;
                $par->removeChild($ch);
                $ch = $par;
            }
            $this->replaceAttributes($ch, $index, (string )$this->DataSource->Rows[$index][$fields['field_name']]);
            if ($ch->hasAttribute("name"))
            {
                $this->setValue($ch);
            }
			
            
            
            if (((string )$value) !== "")
            {
            	if($encode_chars) {
            		Master::importHTML($dom,$value,$node,$ch);
            	}
            	else {
                	$ch->nodeValue = htmlspecialchars($value);
            	}
            	if($this->is_excel_render) {
            		if(is_array($this->excel_options)&&is_array($this->excel_options['skip_columns'])) {
            			$fn=$fields['field_name'];
            			if(!in_array($fn,$this->excel_options['skip_columns'])) {
            				$this->excel_matrix[$index][]=$value;
            			}
            		}
					else {
            			$this->excel_matrix[$index][]=$value;
            		}             		
            	}
            }

            if ($ch->hasChildNodes())
            {
                $this->processChildren($ch, $dom, $index);
            }
        }
    }
    
    function getControls() {
	   		$data = $this->postData[$this->control_id . "_hd"];
	   		
	        if (!empty($data))
	        {
	            $data = unserialize(bzdecompress(base64_decode(urldecode($data))));
	        }
	
	
	        if (is_array($data))
	        {
	        	$this->removeControlFields($data);
	        	if(is_array($this->postData[$this->control_id]["fields"])) {
	            	$data = Control_Utils::array_merge_recursive_custom($data, $this->postData[$this->control_id]["fields"]);	           
	        	}
	        }
	        else
	        {
	            if (is_array($this->postData[$this->control_id]["fields"]))
	            {
	                $data = $this->postData[$this->control_id]["fields"];
	   
	            }
	        }
	      
	        return $data;
    }

    function renderBody(&$dom)
    {
    	
        $xp = new DOMXPath($dom);

        $realBody = $dom->getElementsByTagName("tbody");
        $realBody = $realBody->item(0);
        $tbody = $xp->query("//tbody/tr");
        
        if($this->is_excel_render) {
        	$this->excel_matrix=array();
        }
        
        if(empty($this->DataSource->Rows)) {
        	$realBody->parentNode->removeChild($realBody);
        	return;
        }
        
        /* @var $thead_a DomNodeList */
        $index = 0;
    	
        if ($tbody && $tbody->length)
        {

            for ($i = 0; $i < count($this->DataSource->Rows) - 1; $i++)
            {	            
            	
                $new_node = $tbody->item(0)->cloneNode(true);
                $realBody->appendChild($new_node);
            }
        }
        
        

        $tbody = $xp->query("//tbody/tr");
        /* @var $thead_a DomNodeList */
        $index = 0;
        if ($tbody && $tbody->length)
        {
            foreach ($tbody as $v)
            {

                //$v ->tr
				
                $v->setAttribute("class", $this->styleNames[$index % 2]);
                $v->setAttribute("onmouseover", 'this.className="' . ($this->styleNames[2]) .'"');
                $v->setAttribute("onmouseout", 'this.className="' . ($this->styleNames[$index %2]) . '"');

                if ($v->hasChildNodes())
                {
                    //try {
                    $this->processChildren($v, $dom, $index);
                    //}
                    //catch(Exception $e) {
                    //	var_dump($e);
                    //}
                }
                $index++;
            }
        }
        
    }
    
    function renderXls(&$dom) {
    	 $this->DataSource->Limit="";
	     $this->DataSource->buildquery();
	     $this->renderHead($dom);
	     $this->renderBody($dom);
	     
	    $array=array();
	    $array[0]['header']=$this->matrix_header;
	    $array[0]['rows']=$this->excel_matrix;
	    
	 
	    $i=(int)ob_get_level();
		if($i<1) {
			$i=1;
		}
		while($i>0) {
			ob_end_clean();
			$i--;
		}
		require_once (dirname(__FILE__).'/Spreadsheet/Excel/Writer.php');
		
		ExcelExporter::export($array,0,'','x.xls');
		
		exit;

    }
    
    function renderPrint(&$dom) {
    	 $this->DataSource->Limit="";
	     $this->DataSource->buildquery();
	     $this->is_excel_render=true;
	     $this->renderHead($dom);
	     $this->renderBody($dom);
	     
	    $array=array();
	    $array[0]['header']=$this->matrix_header;
	    $array[0]['rows']=$this->excel_matrix;
	    
	 
	    $i=(int)ob_get_level();
		if($i<1) {
			$i=1;
		}
		while($i>0) {
			ob_end_clean();
			$i--;
		}
		echo <<<EOD
		<table width="100%" cellpadding="5" cellspacing="0" border="1">
EOD;
		foreach ($array as $k=>$v) {
			echo "<tr>";
			foreach ($v['header'] as $hv) {
				if(empty($hv)) {
					$hv="&nbsp;";
				}
			//	$hv=iconv("utf-8","windows-1251",$hv);
				echo "<th>{$hv}</th>";
				//$worksheet->write($c_row, $col, $hv, $format_title);
				//$col++;
			}
			echo "</tr>";
			foreach ($v['rows'] as $r_row) {
				echo "<tr>";
				foreach ($r_row as $rk=>$rv) {
					if(empty($rv)) {
						$rv="&nbsp;";
					}
					//$rv=iconv("utf-8","windows-1251",$rv);
					echo "<td>{$rv}</td>";
				}
				echo "</tr>";	
				ob_flush();			
			}
		}
		
		echo "</table>";
		ob_end_flush();
		die;

    }
    
    function removeControlFields(&$data) {
    	if(is_array($this->postData[$this->control_id]["hd"]["fields"])) {
    		foreach ($this->postData[$this->control_id]["hd"]["fields"] as $hd_k=>$hd_v) {
    			if(is_array($hd_v)) {
    				foreach ($hd_v as $m_key=>$m_val) {
    					unset($data[$hd_k][$m_key]);
    				}
    			}
    		}
    	}
    }
    
    function setCheckBox($par) {
    	$id=$par[0];
    	$index=$par[1];
    	$row=$par[2]->DataSource->Rows[$index];
    	$val=$this->postData[$this->control_id]['fields']['_hch_sel_'][$row['id']];

    	$this->postData[$this->control_id]['fields']['_ch_sel_'][$row['id']]=$val==1?"1":"";	//prazno za da go unchekne
    	$_POST[$this->control_id]['fields']['_ch_sel_'][$row['id']]=$val==1?"1":"";
    	
    }

    function render()
    {
        $data = $this->postData[$this->control_id . "_hd"];
        if (!empty($data))
        {
            $data = unserialize(bzdecompress(base64_decode(urldecode($data))));
        }

        if (is_array($data))
        {
        	$this->removeControlFields($data);
        	if(is_array($this->postData[$this->control_id]["fields"])) {
        		$data = Control_Utils::array_merge_recursive_custom($data, $this->postData[$this->control_id]["fields"]);
            }
        	
           
            /*foreach ($data as $k=>&$v) {
            if(is_array($this->postData[$this->control_id]["fields"][$k])) {
            $v=$this->postData[$this->control_id]["fields"][$k]+$v;
            }				
            }*/
        }
        else
        {
            if (is_array($this->postData[$this->control_id]["fields"]))
            {
                $data = $this->postData[$this->control_id]["fields"];
   
            }
        }
        if (is_array($data))
        {
            $this->postData[$this->control_id]["fields"] = $data;
        }
        
        if (!file_exists($this->template))
        {
            return "<div class='error'>No template specified!</div>";
        }
        ob_start();
        include ($this->template);
        $str_template = ob_get_clean();
        $dom = new DOMDocument("1.0", "UTF-8");
        $dom->encoding = 'UTF-8';
        
        $dom->loadXML($str_template);
        $dom->standalone = true;
        //$b=$dom->getElementsByTagName("a");
        
        $this->is_excel_render=isset($_POST[$this->control_id.'_xls']);
        if($this->is_excel_render) {
	       return $this->renderXls($dom);
        }
        
        $this->is_print_render=((int)($_POST[$this->control_id.'bt_print'])==1);
        if($this->is_print_render) {
        	return $this->renderPrint($dom);
        }

        $this->renderHead($dom);
        $this->renderBody($dom);

        $table = $dom->getElementsByTagName("table");
        foreach ($table as $t)
        {
            $t->setAttribute("id", $this->control_id);
        }
        $html = $dom->getElementsByTagName("html");//root trqbwa da go ima

        $hd = new DOMElement("input");
        $text = '';
        foreach ($html as $h)
        {
            if ($h->hasChildNodes())
            {
                $h->appendChild($hd);
                $this->appendScript($h);
                $this->appendDragScript($h);
                $hd->setAttribute("type", "hidden");
                $hd->setAttribute("name", $this->control_id . "[neworder]");
                $hd->setAttribute("id", $this->control_id . '_neworder');
                $hd->setAttribute("value", $this->getOutputOrder());

                foreach ($h->childNodes as $ch)
                {
                    $text .= trim($dom->saveXML($ch));
                }
            }
            //$dom->saveXML($h);
        }
        $hd = "";
        
        if (is_array($data) && !empty($data))
        {
            $val = urlencode(base64_encode(bzcompress(serialize($data))));
            $hd = <<< EOD
			<input type="hidden" name="{$this->control_id}_hd" value="{$val}" />
EOD;
        }
        $xls_buttons=array();
        
        if($this->hasPrint) {
        	$xls_buttons[]=<<<EOD
        	<input onclick="getForm(this).target='_blank';document.getElementById('{$this->control_id}bt_print').value='1';getForm(this).submit();getForm(this).target='';document.getElementById('{$this->control_id}bt_print').value='';" type="button" class="print_button" value="Print" />
        	<input type="hidden" name="{$this->control_id}bt_print" value="" />
EOD;
        }
        if($this->hasExcelExport) {
        	$xls_buttons[]="<input class='excel_button' type=\"submit\" name=\"{$this->control_id}_xls\" value=\"\" />";
        }
        
        $xls="";
        if(!empty($xls_buttons)) {
        	//$xls="<div style='width:100%;' align=\"right\">".implode("<img src='/be/i/design/v_separator.png' />",$xls_buttons)."</div>";
        	$xls="<td align=\"right\" style='padding-right:5px;'>".implode("<img align='absmiddle' src='/be/i/design/v_separator.png' />&nbsp;&nbsp;",$xls_buttons)."</td>";
        }
        //else {
        //	$xls="";
        //}
        
        $header=<<<EOD
<div class="listTableHeader">
<table class="listTable" cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td><div>List</div></td>
	{$xls}
	</tr>
</table>
</div>
EOD;
        
		$text = html_entity_decode($text,ENT_NOQUOTES,"UTF-8");
        //return $xls.$text . $hd;
        return $header.$text . $hd;

    }

    private function appendScript($node)
    {
        if (defined("dgn_script"))
        {
            return "";
        }
        define("dgn_script", 1);
        $script = new DOMElement("script");
        $node->appendChild($script);
        
        if($this->use_ajax) {
        	$fld=<<<EOD
        	/*var val=form_obj.elements[element].value;
			form_obj.elements[element].removeNode(true);*/
			myRender(obj);
			return false;
EOD;
        }
        else {
        	$fld="form_obj.submit();";
        }
        
        $script->nodeValue = str_replace(array("\r", "\n"), "", "
function _table_setOrder(obj,element,new_order) {
var form_obj=_scr_findForm(obj);
	if(!form_obj)	return;
	
	if(event.ctrlKey) {
	
		form_obj.elements[element].value+=\",=\"+new_order;
	}
	else {
		form_obj.elements[element].value+=\",^\"+new_order;
	}
	event.cancelBubble=true;
	event.returnValue=false;
	{$fld}
}

function _scr_findForm(elem) {
	 form_obj = elem;
	 while (form_obj.tagName!='FORM') {
	    form_obj = form_obj.parentNode;
	    if (!form_obj) {
	      alert('Form not found! Please put the list control in a form!'); return 0;
	   }
	 }
	  return form_obj;
}
");
    }
    
    private function appendDragScript($node)
    {
    	if(!$this->candrag) {
			return "";
		}
		$str="";
        if (!defined("dgn_drag_script"))
        {
        	$scr=new DOMElement("script");
        	$node->appendChild($scr);
        	$scr->setAttribute("src","/table.js");
        	$scr->nodeValue=" ";
            //return $str="<script src="/table.js"></script>";
        }
        define("dgn_drag_script", 1);
        $script = new DOMElement("script");
        $node->appendChild($script);
		$init="";
		if(!defined("drag_init")) {
			$init="drag_init();";
			define("drag_init",1);
		}
        $script->nodeValue = str_replace(array("\r", "\n"), "", "
        {$init}
loadOrder(\"{$this->control_id}\");
reorderCells('{$this->control_id}',dragObj.order['{$this->control_id}']);
");
    }
}

?>