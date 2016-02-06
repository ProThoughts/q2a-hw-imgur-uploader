<?php include_once( 'ft-nonce-lib.php' ); ?>
<html>
	<head>
		<title>Example use of FT-NONCE-LIBRARY</title>
	</head>
	<body>
		<?php 
		if ( isset( $_POST['form_submitted']) ) {
			if ( isset( $_POST['button_one'] ) && ft_nonce_is_valid( $_POST['_nonce'] , 'button-one' ) ){
				echo "<p>Button One Validated!</p>";
			}elseif( isset( $_POST['button_two'] ) && ft_nonce_is_valid( $_POST['_nonce'] , 'button-two' ) ){
				echo "<p>Button Two Validated!</p>";
			}else{
				echo "<p>Form Not Validated! You do not have permission to do this!<p>";
			}
		}elseif( isset( $_GET['_nonce'] ) ){
			if ( ft_nonce_is_valid( $_GET['_nonce'] , 'link-one' ) ){
				echo "<p>Link One Validated!</p>";
			}elseif( ft_nonce_is_valid( $_GET['_nonce'] , 'link-two' ) ){
				echo "<p>Link Two Validated!</p>";
			}else{
				echo "<p>Link Not Validated! You do not have permission to do this!<p>";
			}			
		}
		?>
		<h3>Form Examples</h3>
		<form action="" method="post" >
			<input type='submit' name='button_one' value="Button One" />
			<input type='hidden' name='form_submitted' value='1' />
			<?php ft_nonce_create_form_input( 'button-one' ); ?>
		</form>
		<br />
		<form action="" method="post" >
			<input type='submit' name='button_two' value="Button Two" />
			<input type='hidden' name='form_submitted' value='1' />
			<?php ft_nonce_create_form_input( 'button-two' ); ?>
		</form>
		<h3>Link Examples</h3>
		<a href="index.php?<?php echo ft_nonce_create_query_string( 'link-one' );?>">Link One</a><br />
		<a href="index.php?<?php echo ft_nonce_create_query_string( 'link-two' );?>">Link Two</a><br />
	</body>
</html>
