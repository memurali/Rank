<?php
error_reporting(E_ALL ^ E_NOTICE);
set_time_limit(0);
class UsersController extends AppController
{   
    var $helpers = array('Html', 'Form', 'Js', 'Paginator');    
    public $components = array('Paginator', 'RequestHandler');
    public $uses = array('Tblprocess_stats','Tblinter','Tblsum','Tblresults','Tblwhitelist','Tbllogin');
    public function beforeFilter()
    {
		parent::beforeFilter();
		$this->Auth->allow('import','results','strategy','dbchange','test','delete','login','logout');
    }
	public function login()
	{
		if ($this->request->is('post')) 
		{
			$username = $_POST['username'];
			$password = $_POST['password'];
			$password = AuthComponent::password($password);
			$count = $this->Tbllogin->find('count',array('conditions'=> array('username'=>$username,
																	'password'=>$password,
																	'Active'=>'Y')));
			if($count>0)
			{
				$this->Session->write('login', $username);
				$this->redirect('results');
			}
			else
				$this->set('error','username or password is incorrect');
			
		}
		
	}
	public function logout()
	{
		$this->Session->delete('login');
			$this->redirect('login');
	}
	public function import()
    {
        if($this->Session->read('login')=='')
			$this->redirect('login');
		$this->autoLayout = false;
		
		/*** delete records from tblinter table and tblsum table **/
		$this->Tblinter->query('TRUNCATE table tbl_inter');
		$this->Tblsum->query('TRUNCATE table tblsum');
		
		$root = ROOT . '/murali/';
		$date = date('Y-m-d');
		$folder =   $root.'data-in';
		$files = glob($folder."/*.csv");
		if(count($files)>0)
		{			
			foreach($files as $csv)
			{
				$status ='';
				try 
				{		
					ini_set('auto_detect_line_endings',TRUE);
					$file     = fopen($csv, "r");
					$file_name = basename($csv);
					$custag = basename($csv, ".csv"); 
					$fp = file($csv, FILE_SKIP_EMPTY_LINES);
					$rowcount = count($fp)-1;
					$insert_process = "INSERT INTO `tblprocess_stats`(`Filename`, `Record_Count`,`Status`) VALUES ('".$file_name."',".$rowcount.",'In progress')";
					$this->Tblprocess_stats->query($insert_process);
					$firstline = true;
					while (($data = fgetcsv($file)) !== FALSE) 
					{
						if (!$firstline) // ignore header
						{
							if (array(null) !== $data) 
							{ // ignore blank lines
								$Date = date('Y-m-d',strtotime($data[0]));
								$Keyword   = $data[2];
								$Market   = $data[3];
								$Location   = $data[4];
								$Rank   = $data[6];
								$BaseRank   = $data[7];
								$URL   = $data[8];
								$Advertiser   = $data[9];
								$Global   = $data[10];
								$Regional   = $data[11];
								$CPC   = $data[12];
								$Tags   = $data[13];
																
								$insert_data = array(
												"keyword_id"=>0,
												"ReportDate"=>$Date,
												"Keyword"=>$Keyword,
												"KeywordMarket"=>$Market,
												"KeywordLocation"=>$Location,
												"Rank"=>$Rank,
												"Monthly_rank"=>0,
												"Avg_rank"=>0,
												"BaseRank"=>$BaseRank,
												"URL"=>$URL,
												"Advertiser"=>$Advertiser,
												"Global"=>$Global,
												"Regional"=>$Regional,
												"CPC"=>$CPC,
												"Tags"=>$Tags,
												"Customtag"=>$custag,
												"FileName"=>$file_name
											);	
								$this->Tblinter->saveAll($insert_data);
							}
						}
						$firstline = false;
					}
					ini_set('auto_detect_line_endings',FALSE);
					$processidarr = $this->Tblprocess_stats->find('first',array('order'=>'Processid DESC'));
					$processid = $processidarr['Tblprocess_stats']['Processid'];
					
					fclose($file);
					if (!file_exists($root.'data-out/'.$Date)) {
						mkdir($root.'data-out/'.$Date, 0777, true);
					}
					copy($folder.'/'.$file_name, $root.'data-out/'.$Date.'/'.$file_name);
				}
				catch (\Exception $e) 
				{
					$status = $e->getMessage();
				}
				if($status=='')
					$status='Completed';
				$update_qry = "UPDATE `tblprocess_stats` SET `Status`='".$status."' 
								  WHERE `Processid`=".$processid;
				$this->Tblprocess_stats->query($update_qry);
			}
		
			foreach($files as $csv)
			{
				chmod($csv, 0777);
				unlink($csv);
			
			}
			$this->calculation();
			$this->addwhitelist();
			$this->saveresult('update');
			$this->saveresult('insert');
			$this->checkwhitelist();
			$this->redirect('results');
		}
	}
	public function results()
	{
		 if($this->Session->read('login')=='')
			$this->redirect('login');
		$this->autoLayout = false;
		$range="true";
		$filter = 'tbl_results';
		if ($this->request->is('post')) 
		{
			if($_POST["startdate"]!='')
			{
				$startdate = $_POST["startdate"];
				$enddate =$_POST["enddate"];
				if($startdate>$enddate)
				{
					$change_start = $startdate;
					$change_end = $enddate;
					
					$startdate = $change_end;
					$enddate = $change_start;
				}
				
			}
			
		}
		else
		{
			/*$filtr_qry = "SELECT DISTINCT(ReportDate) FROM tbl_results 
						ORDER BY ReportDate DESC LIMIT 4";
			$filtrarr = $this->Tblresults->query($filtr_qry);
			if(count($filtrarr)>=4)
			{
				$enddate = $filtrarr[0]['tbl_results']['ReportDate'];
				$startdate =  $filtrarr[3]['tbl_results']['ReportDate'];
			}
			else
			{*/
				$range="false";
			//}
			
		}
			
		if($range=='true')
		{
			
			$sel_maxdate = "SELECT MAX(`ReportDate`) as ReportDate FROM `tbl_results`";
			$selmaxarr = $this->Tblresults->query($sel_maxdate);
			$maxdate = $selmaxarr[0][0]['ReportDate'];
			$str_maxdate = date('Y-m-d', strtotime($maxdate));
			$str_startdate = date('Y-m-d', strtotime($startdate));
			$str_enddate = date('Y-m-d', strtotime($enddate));
			$str_maxdate.' >='. $str_startdate.') && ('.$str_maxdate .'<='. $str_enddate.'))';
			if(($str_maxdate >= $str_startdate) && ($str_maxdate <= $str_enddate))
			{			
				$sql = "SELECT * FROM `tbl_results` 
						where `ReportDate` between '".$startdate."' AND '".$enddate."' 
						order by KeywordGroups ASC,ReportDate ASC";
						
				$select_result="SELECT DISTINCT ReportDate FROM `tbl_results` 
							where `ReportDate` between '".$startdate."' AND '".$enddate."' 
							order by ReportDate ASC";
						
			}
			else
			{
				$sql="SELECT KeywordGroups,ReportDate,Rank,Volume FROM `tbl_results`  
						WHERE ReportDate BETWEEN '".$startdate."' AND '".$enddate."'
						UNION ALL 
						SELECT KeywordGroups,ReportDate,Rank,Volume FROM tbl_results 
						WHERE ReportDate='".$maxdate."'
						ORDER BY  KeywordGroups ASC,ReportDate ASC";
								
				$select_result="SELECT DISTINCT ReportDate FROM `tbl_results` 
								where `ReportDate` between '".$startdate."' AND '".$enddate."'
								UNION ALL 
								SELECT DISTINCT ReportDate FROM tbl_results 
								WHERE ReportDate='".$maxdate."' ORDER BY ReportDate ASC";
				$filter = 0;
			}
			
			
		}
		else
		{
			$select_result = "SELECT DISTINCT ReportDate FROM `tbl_results` 
							  order by ReportDate ASC";
							  
			$sql = "SELECT * FROM `tbl_results` 
					order by KeywordGroups ASC,ReportDate ASC";
					
		}
		$theadval = $this->Tblresults->query($select_result);
		$tbodyval = $this->Tblresults->query($sql);
		$this->set('theadarr',$theadval);
		$this->set('tbodyarr',$tbodyval);
		$this->set('filter',$filter);
	}
	public function test()
	{
		$this->autoLayout = false;
		$range="true";
		if ($this->request->is('post')) 
		{
			if($_POST["startdate"]!='')
			{
				$startdate = $_POST["startdate"];
				$enddate =$_POST["enddate"];
				if($startdate>$enddate)
				{
					$change_start = $startdate;
					$change_end = $enddate;
					
					$startdate = $change_end;
					$enddate = $change_start;
				}
				
			}
		}
		else
		{
			/*$filtr_qry = "SELECT DISTINCT(ReportDate) FROM tbl_results 
						ORDER BY ReportDate DESC LIMIT 4";
			$filtrarr = $this->Tblresults->query($filtr_qry);
			if(count($filtrarr)>=4)
			{
				$enddate = $filtrarr[0]['tbl_results']['ReportDate'];
				$startdate =  $filtrarr[3]['tbl_results']['ReportDate'];
			}
			else
			{*/
				$range="false";
			//}
			
		}
			
		if($range=='true')
		{
			$select_result="SELECT DISTINCT ReportDate FROM `tbl_results` 
							where `ReportDate` between '".$startdate."' AND '".$enddate."'  
							order by ReportDate ASC";
			$sql = "SELECT * FROM `tbl_results` 
					where `ReportDate` between '".$startdate."' AND '".$enddate."' 
					order by KeywordGroups ASC,ReportDate ASC";
			
		}
		else
		{
			$select_result = "SELECT DISTINCT ReportDate FROM `tbl_results` 
							  order by ReportDate ASC";
							  
			$sql = "SELECT * FROM `tbl_results` 
					order by KeywordGroups ASC,ReportDate ASC";
					
		}
		
		$theadval = $this->Tblresults->query($select_result);
		$tbodyval = $this->Tblresults->query($sql);
		$this->set('theadarr',$theadval);
		$this->set('tbodyarr',$tbodyval);
	}
	public function strategy()
	{
		$this->autoLayout = false;
		$sta_qry = "SELECT Date(Rundate) as Rundate,SUM(Record_Count) as count 
					FROM `tblprocess_stats` 
					WHERE Status='Completed'
				    GROUP BY DATE(Rundate)";
		$sta_arr = $this->Tblprocess_stats->query($sta_qry);	
		$this->set('strategyarr',$sta_arr);
		
		
	
	}
	public function dbchange()
	{
		//$this->Tblresults->query('Truncate tbl_results');
		
		
	}
	public function delete()
	{
		 if($this->Session->read('login')=='')
			$this->redirect('login');
		
		$sel_kwgrp="SELECT DISTINCT `KeywordGroups` FROM `tbl_results` 
					ORDER BY KeywordGroups ASC";
		$kwgrparr = $this->Tblresults->query($sel_kwgrp);
		$this->set('kwgrparr',$kwgrparr);
		
		if ($this->request->is('post')) 
		{
			$reportdate = $_POST['reportdate'];
			$kwgrp = $_POST['kwgrp'];
			if($reportdate!='' && $kwgrp=='')
			{
				$qry = "DELETE FROM `tbl_results` WHERE `ReportDate`='".$reportdate."'";
			}
			else if($reportdate=='' && $kwgrp!='')
			{
				$qry = "DELETE FROM `tbl_results` WHERE `KeywordGroups`='".$kwgrp."'";
			}
			else if($reportdate!='' && $kwgrp!='')
			{
				$qry = "UPDATE `tbl_results` SET `Volume`=0,`Rank`=0 WHERE 
						`KeywordGroups`='".$kwgrp."' AND `ReportDate`='".$reportdate."'";
			}
			$this->Tblresults->query($qry);
		}
	}
	function checkwhitelist()
	{
		$sel_distinct = "SELECT DISTINCT ReportDate FROM `tbl_inter`";
		$selarr = $this->Tblinter->query($sel_distinct);
		foreach($selarr as $selval)
		{
			$Date = $selval['tbl_inter']['ReportDate'];
			$insert_white = "INSERT INTO tbl_results SELECT tag_name,0,'$Date' as Reportdate,
						0 As Rank FROM tbl_whitelist 
						WHERE tag_name NOT IN (SELECT keywordgroups from tbl_results 
						WHERE ReportDate='$Date')";
			$this->Tblresults->query($insert_white);
		}
	}
	function calculation()
	{
		/** update monthrank   ***/
		$update_monthrank = "UPDATE tbl_inter SET Monthly_rank= Rank*Regional 
							 WHERE Rank!='null'";
		$this->Tblinter->query($update_monthrank);
		
		/*** insert data into tblsum **/
		$insert_tblsum = "INSERT INTO `tblsum`(`Customtag`, `Sum_regional`) 
						  SELECT Customtag,SUM(Regional) FROM tbl_inter 
						  WHERE Rank!='null' GROUP BY Customtag";
		$this->Tblsum->query($insert_tblsum);
		
		/**  update avg_rank value **/
		$update_avgrank = "Update `tbl_inter` i, tblsum s set 
							i.Avg_rank= i.Monthly_rank/s.Sum_regional 
							WHERE i.Customtag=s.Customtag AND i.Rank!='null'";
		$this->Tblinter->query($update_avgrank);
	}
	function saveresult($flow)
	{
		if($flow=='update')
		{
			$update_qry = "UPDATE tbl_results as r,
								(SELECT Customtag,ReportDate,
								SUM(Regional) as vol,SUM(Avg_rank) as rankval FROM `tbl_inter` 
								WHERE Rank!='null' GROUP BY Customtag,ReportDate) as i 
							SET r.`Volume`=i.vol, 
							r.`Rank`=i.rankval 
							WHERE r.`KeywordGroups`= i.Customtag AND 
							r.`ReportDate`=i.ReportDate";
			$this->Tblresults->query($update_qry);
			
			$del_qry = "DELETE i FROM `tbl_inter` i, tbl_results r 
						WHERE r.KeywordGroups=i.Customtag AND 
						r.Reportdate=i.Reportdate";
			$this->Tblinter->query($del_qry);
			
		}
		else if($flow=='insert')
		{
			
			$insert_result = "INSERT INTO tbl_results 
							 SELECT customtag,SUM(Regional),ReportDate,SUM(Avg_rank) FROM `tbl_inter` 
							 WHERE Rank!='null' GROUP BY Customtag,ReportDate";
			$this->Tblresults->query($insert_result);
		}
	}
	function addwhitelist()
	{
		$sel_qry = "SELECT DISTINCT i.Customtag FROM tbl_inter i 
					LEFT JOIN tbl_whitelist w ON i.Customtag = w.tag_name 
					WHERE w.tag_name IS NULL";
		$selarr = $this->Tblinter->query($sel_qry);
		if(count($selarr)>0)
		{
			$ins_qry = "INSERT INTO `tbl_whitelist`(`tag_name`) 
						SELECT DISTINCT i.Customtag FROM tbl_inter i LEFT JOIN 
						tbl_whitelist w ON i.Customtag = w.tag_name 
						WHERE w.tag_name IS NULL";
			$this->Tblwhitelist->query($ins_qry);
			foreach($selarr as $selval)
			{
				$kwgrp = $selval['i']['Customtag'];
				$insert_result = "INSERT INTO tbl_results(`ReportDate`) SELECT DISTINCT ReportDate FROM tbl_results";
				$this->Tblresults->query($insert_result);
				$update_result ="UPDATE `tbl_results` SET `KeywordGroups`='".$kwgrp."' WHERE `KeywordGroups`=''";
				$this->Tblresults->query($update_result);
				
			}
		}

	}
}

