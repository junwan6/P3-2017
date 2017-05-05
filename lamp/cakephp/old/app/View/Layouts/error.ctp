<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

?>
<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
	</title>
	<?php
		echo $this->Html->meta('icon');
    
    // Converted from partials/global_header.html
    echo $this->Html->script(array(
      'http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js',
      '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js',
      '//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js'
    ));
    echo $this->Html->css(array(
      '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/themes/smoothness/jquery-ui.css',
      // original had integrity="sha384-...", crossorigin="anonymous" for 2 following
      'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css',
      '//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css',
      'http://fonts.googleapis.com/css?family=Lato&subset=latin,latin-ext'
    ));
    echo $this->Html->script(array(
      'global.js',
      'index.js'
    ));
    echo $this->Html->css(array(
      'global.css'
    ));

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
</head>
<body>

	<div id="cakephp-container">
		<div id="cakephp-header">
		</div>
		<div id="cakephp-content">
			<?php echo $this->Flash->render(); ?>

			<?php echo $this->fetch('content'); ?>
		</div>
	</div>
	<?php echo $this->element('sql_dump'); ?>
</body>
</html>
