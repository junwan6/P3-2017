<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
 
/**
 * Connect independent pages to main controller PagesController (default name, kept)
 * 404 error page controlled by default error handler, using View/Errors/error###.ctp
 * TODO: Modify error handling to treat missing pages as 404 instead of invalid controller 400
 */
  // Home page
  Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'index'));
  // Alias of home page, for linking in relative links
  Router::connect('/index', array('controller' => 'pages', 'action' => 'display', 'index'));
  // Donor page
  Router::connect('/donors', array('controller' => 'pages', 'action' => 'display', 'donors'));
  // Browse page
  Router::connect('/browse', array('controller' => 'pages', 'action' => 'display', 'browse'));

/**
 * Connect pages requiring user data to UserController
 * Includes login, logout, password reset, passports, etc.
 * TODO: Implement UserController
 */
  // Profile page (loads liked/disliked videos)
  Router::connect('/profile', array('controller' => 'user', 'action' => 'display', 'profile'));
  // TODO: Register (create and insert user)
  //Router::connect('/register', array('controller' => 'user',
  //  'action' => 'display', 'profile'));
  // TODO: Login Attempt (AJAX, sends password salt for clientside password hashing)
  //Router::connect('/login-attempt', array('controller' => 'user',
  //  'action' => 'display', 'profile'));
  // TODO: Login (send hashed attempt, create session)
  //Router::connect('/login', array('controller' => 'user',
  //  'action' => 'display', 'profile'));
  // TODO: Login via LinkedIn (implement passport, automatic registration)
  //Router::connect('/auth/linkedin', array('controller' => 'user',
  //  'action' => 'display', 'profile'));
  // TODO: Login via Facebook (as above)
  //Router::connect('/auth/facebook', array('controller' => 'user',
  //  'action' => 'display', 'profile'));
  // TODO: Logout (Clear session)
  //Router::connect('/logout', array('controller' => 'user',
  //  'action' => 'display', 'profile'));
  // TODO: Account recovery (sends email to linked email with reset link)
  //Router::connect('/recover-account', array('controller' => 'user',
  //  'action' => 'display', 'profile'));
  // TODO: Password reset from recovery link (code lookup, password update)
  //Router::connect('/reset-password', array('controller' => 'user',
  //  'action' => 'display', 'profile'));
  // TODO: Set new password while already logged in (password update)
  //Router::connect('/new-password', array('controller' => 'user',
  //  'action' => 'display', 'profile'));

/**
 * Connect pages requiring career information to CareerController
 * Includes SOC codes (Search, random)
 * TODO: Implement CareerController
 * TODO: Combined pages into single load
 */
  // Combined access to eventual single file return
  // focus=video (Find video linked to SOC, questions, etc.)
  // focus=salary (Salary data)
  // focus=education (Education data)
  // focus=skills (Skills data)
  // focus=outlook (Outlook data)
  // focus=world-of-work (Skills, intelligences data)
  // TODO: Single page/function that takes parameters to display correct page
  // Focus not constrained, invalid inputs go to video rather than 404
  Router::connect('/career/:soc/:focus', array('controller' => 'career',
    'action' => 'displayCareer'), array('pass' => array('soc','focus'),
      'soc' => '[0-9]{2}-[0-9]{4}'));
  // Default focus = video, cakephp does not support optional params
  Router::connect('/career/:soc', array('controller' => 'career',
    'action' => 'displayCareer', 'video'), array('pass' => array('soc'),
      'soc' => '[0-9]{2}-[0-9]{4}'));
  // TODO: Automatic redirect to random SOC of available. Changes URL on redirect
  // May take x and y parameters for weighted randomness on World of Work graphic
  //Router::connect('/career/random', array('controller' => 'career',
  //  'action' => 'display', 'salary'));
  // TODO: Search results from search bar or browse search
  //Router::connect('/career/search', array('controller' => 'career',
  //  'action' => 'display', 'salary'));

/**
 * Connect video rating pages to AlgorithmController
 * TODO: Implement AlgorithmController
 */
  // TODO: AJAX on button press, update ratings, return next video SOC
  // Takes filter parameters (updated clientside by JS)
  //Router::connect('/career/:vidrating', array('controller' => 'algorithm',
  //  'action' => 'display', 'salary'), array('pass' => array('vidrating'),
  //    'vidrating' => 'vidup|vidmid|viddown'));

/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';
