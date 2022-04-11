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
		    $('#datatbl').DataTable({
				 "pageLength":10,
				 "paging":false,
				 "searching":false,
				 "info":false,
				 "lengthChange":false,
				 "aaSorting": [],
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
				<a href='' id="downloadLink" onclick="exportrecords(this)">Export</a>&nbsp;&nbsp;
				<?php echo $this->html->link('Import', array('controller' => 'users','action' => 'import'))?>
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
			
			<div id="exptbl">
				<?php
				if(count($tbodyarr)>0)
				{
				?>
					<table id="datatbl" border='1px'>
						<thead>
							<tr>
								<th class='kwgrp'><a href='#'>KeywordGroups</a></th>
								<th class='numtd'><a href='#'>Volume</a></th>
								<?php
								foreach($theadarr as $thead)
								{
									echo "<th class='numtd'>".date ("M d",strtotime($thead['tbl_results']['ReportDate']))."</th>";
								}
						
							echo '</tr>';
						?>
						</thead>
						<tbody>
							<?php
							$keywordgroup="";
							$i=0;
							$volume = 0;
							foreach ($tbodyarr as $tbody)
							{
								if($keywordgroup!= $tbody['tbl_results']["KeywordGroups"])
								{
									$volume = $tbody['tbl_results']["Volume"];
									$keywordgroup=$tbody['tbl_results']["KeywordGroups"];
									echo "<tr>";
											echo "<td class='kwgrp'>" . $tbody['tbl_results']["KeywordGroups"]. "</td>
												<td class='numtd'>" .$volume. "</td>
												<td class='numtd'>". $tbody['tbl_results']["Rank"]. "</td>";
											$prev_rank = $tbody['tbl_results']["Rank"];
								}
								else
								{
									$volume = $tbody['tbl_results']["Volume"];
									$rank = $tbody['tbl_results']["Rank"];
									$color='';
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
									echo "<td class='numtd' style='background-color:".$color."'>". $rank. "</td>";	
									$prev_rank=$rank;
								}
								
								$i++;
							}
							echo '</tr>';
							?>
						</tbody>
					</table>
				<?php
				}
				else
				{
					echo 'No results found';
				}
				
				?>
			</div>
		</center>	
	</body>
</html>