<html>
	<head>
	</head>
	<body>
		<center>
			<?php
					echo $this->Form->create(false, array('url' => array('controller' => 'users', 
																	'action' => 'login'),
																	'id' => 'login',
																	'method'=>'POST'));
			?>
				<table>
					<tr>
						<td>Username:</td>
						<td><input type='text' name='username' id='username'></td>
					</tr>
					<tr>
						<td>Password:</td>
						<td><input type='password' name='password' id='username'></td>
					</tr>
				</table>	
				<button type="submit" value="submit">Submit</button>
			<?php echo $this->Form->end();?><br>
			<?php
				echo $error;
			?>
		</center>
	</body>
</html>