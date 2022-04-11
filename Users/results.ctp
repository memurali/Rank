<!DOCTYPE html>
<html>
	<head>
		<style>
			  table 
			  {
				border-collapse:collapse;
			  }
			  td, th 
			  { 
				  padding: 20px;  
				  text-align: center;
				  display: inline-block;
				  width: 100px;
				  height: 10px;
				  object-fit: cover;
			  }
			  .kwgrp
			  {
				width:150px;
				height:1px;
			  }
			  .numtd
			  {
				width:50px;
				height:1px;
			  }
		</style>
		<?php
		//echo $this->Html->css('dataTables.foundation.min.css');
		echo $this->Html->script('jquery.js');
		echo $this->Html->script('datatables.min.js');
		?>
		<script type="text/javascript">
		$(document).ready(function() {
			var delay = (function() {
				var timer = 0;
				return function(callback, ms) {
					clearTimeout(timer);
					timer = setTimeout(callback, ms);
				};
			})();
			
			$('#datatbl').DataTable({
				 "pageLength":10,
				 "paging":false,
				 "searching":false,
				 "info":false,
				 "lengthChange":false,
				 "aaSorting": [],
		   });
		   $('#btnimport').click(function()
		   {
				setTimeout(function() {

					$('#btnimport').removeAttr('href');

				}, 1000);
				
		   });
	    });
		function exportrecords(elem) {
		    var table = document.getElementById("exptbl");
			var html = table.outerHTML; 
			var url = 'data:application/vnd.ms-excel,' + escape(html); // Set your html table into url 
			elem.setAttribute("href", url);
			elem.setAttribute("download", "record.xls"); // Choose the file name
		    return false;
		}
		</script>
	</head>
	<body>
		<center>
			<span style='float:right;'>
				<?php echo $this->html->link('Import', array('controller' => 'users','action' => 'import'),array('id'=>'btnimport'))?>&nbsp;&nbsp;
				<a href='' id="downloadLink" onclick="exportrecords(this)">Export</a><br>
				<?php echo $this->html->link('Delete', array('controller' => 'users','action' => 'delete'))?>&nbsp;&nbsp;
				<?php echo $this->html->link('Logout', array('controller' => 'users','action' => 'logout'))?>
			</span>
			<?php
				echo $this->Form->create(false, array('url' => array('controller' => 'users', 
																'action' => 'results'),
																'id' => 'view',
																'method'=>'POST'));
			?>
				<b>Startdate:</b><input type="date" name="startdate" id="startdate">
				<b>Endate:</b><input type="date" name="enddate" id="enddate">
				<button type="submit" value="submit">Submit</button>
			<?php echo $this->Form->end();?><br>
			
			<?php
			if(count($tbodyarr)>0)
			{
				$keywordgroup="";
				//$i=0;
				$volume = 0;
				$trow='';
				$tdkwgrp='';
				$tdvol='';
				$tdrank='';
				$tbodyval='';
				$prev_rank = 0;
				$color='';
				
				for($i=0; $i<=count($tbodyarr); $i++)
				{
					if($i==count($tbodyarr))
					{
						$tbodyval.= $trow.$tdkwgrp.$tdvol.$tdrank;
						break;
					}
					$rank = $tbodyarr[$i][$filter]["Rank"];
					$volume = $tbodyarr[$i][$filter]["Volume"];
					if($keywordgroup!=$tbodyarr[$i][$filter]["KeywordGroups"])
					{
						if($i>0)
							$tbodyval.= $trow.$tdkwgrp.$tdvol.$tdrank;
						$tdrank='';
						$keywordgroup=$tbodyarr[$i][$filter]["KeywordGroups"];
						$trow="<tr>";
						$tdkwgrp="<td class='kwgrp'>" . $keywordgroup. "</td>";
						$tdrank="<td class='numtd'>". $rank. "</td>";	
						$tdvol="<td class='numtd'>" .number_format($volume,0). "</td>";
						$prev_rank = $rank;
					}
					else
					{
					
						$tdvol="<td class='numtd'>" .number_format($volume,0). "</td>";
						if($prev_rank>$rank)
						{
							$diff = $rank-$prev_rank;
							$percentage = ($diff/60)*100;
							if($percentage<30)
								$color = '#d9ead3';
							if((30<$percentage) && ($percentage<60))
								$color = '#b6d7a8';
							if($percentage>60)
								$color = '#93c47d';
							
						}
						else if($prev_rank<$rank)
						{
							$diff = $prev_rank-$rank;
							$percentage = ($diff/60)*100;
							if($percentage<30)
								$color = '#f4cccc';
							if((30<$percentage) && ($percentage<60))
								$color = '#ea9999';
							if($percentage>60)
								$color = '#e06666';
						}
						$tdrank.="<td class='numtd' style='background-color:".$color."'>". $rank. "</td>";	
						$prev_rank=$rank;
						$color='';
					}
					
				}
							
			}
			else
			{
				echo 'No results found';
			}
			?>
			<div id="exptbl">
				<table id="datatbl" border='1px'>
					<thead>
						<tr>
							<th class='kwgrp'><a href='#'>KeywordGroups</a></th>
							<th class='numtd'><a href='#'>Volume</a></th>
							<?php
							foreach($theadarr as $thead)
							{
								echo "<th class='numtd'>".date ("M d",strtotime($thead[$filter]['ReportDate']))."</th>";
							}
					
						echo '</tr>';
					?>
					</thead>
					<tbody>
						<?php echo $tbodyval; ?>
					</tbody>
				</table>
			
			</div>
		</center>	
	</body>
</html>