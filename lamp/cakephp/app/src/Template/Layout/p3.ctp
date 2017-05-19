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
 * @since         0.10.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
    </title>
    <?= $this->Html->meta('icon') ?>

    <?php
      // Converted from partials/global_header.html
      echo $this->Html->script(array(
        'https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js',
        '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js',
        '//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js'
      ));
      echo $this->Html->css(array(
        '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/themes/smoothness/jquery-ui.css',
        // original had integrity="sha384-...", crossorigin="anonymous" for 2 following
        'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css',
        '//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css',
        'https://fonts.googleapis.com/css?family=Lato&subset=latin,latin-ext'
      ));
      echo $this->Html->script(array(
        'global.js',
        // TODO: Figure out what is used from index.js, separate out
        'Pages/index.js'
      ));
      echo $this->Html->css(array(
        'global.css'
      ));
    ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>
    <?= $this->element('navbar'); ?>
    <?= $this->Flash->render() ?>
    <div class="cakephp-container clearfix">
        <?= $this->fetch('content') ?>
    </div>
    <footer>
    </footer>
</body>
</html>
