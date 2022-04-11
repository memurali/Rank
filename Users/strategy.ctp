<html>
	<head>
		
	</head>
	<body>
		<center>
			<input type='checkbox'>
			<table>
				<tr>
					<th>No</th>
					<th>Rundate</th>
					<th>Count</th>
				</tr>
				<tr>
					<?php
					//print_r($strategyarr);
					for($i=0; $i<count($strategyarr); $i++)
					{
						$j=$i+1;
						echo '<tr>';
							echo '<td>'.$j.'</td>';
							echo '<td>'.$strategyarr[$i][0]['Rundate'] .'</td>';
							echo '<td>'.$strategyarr[$i][0]['count'] .'</td>';
						echo '</tr>';
					}
					?>
				</tr>
			</table>
		</center>
	</body>
</html>

