<html>
	<body>
		<center>
			<span style='float:right;'>
				<?php echo $this->html->link('Results', array('controller' => 'users','action' => 'results'))?>&nbsp;&nbsp;
				<?php echo $this->html->link('Import', array('controller' => 'users','action' => 'import'))?>&nbsp;&nbsp;
				<?php echo $this->html->link('Logout', array('controller' => 'users','action' => 'logout'))?>&nbsp;&nbsp;
			</span>
			<?php
				echo $this->Form->create(false, array('url' => array('controller' => 'users', 
																'action' => 'delete'),
																'id' => 'delete',
																'method'=>'POST'));
			?>
				<table>
					<tr>
						<td>Keyword Group:</td>
						<td>
							<select name='kwgrp' id='kwgrp'>
								<option value=''>--select--</option>
								<?php
								foreach($kwgrparr as $kwgrp)
								{
									echo '<option value='.$kwgrp['tbl_results']['KeywordGroups'].'>'.$kwgrp['tbl_results']['KeywordGroups'].'</option>';
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td>Report Date:</td>
						<td><input type="date" name="reportdate" id="reportdate"></td>
					</tr>
				</table>
				<button type="submit" value="submit">Submit</button>
			<?php echo $this->Form->end();?><br>
		</center>
	</body>
</html>