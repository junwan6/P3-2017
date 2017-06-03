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
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Core\Plugin;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

/**
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 *
 */
Router::defaultRouteClass(DashedRoute::class);

Router::scope('/', function (RouteBuilder $routes) {
    /**
     * Here, we are connecting '/' (base path) to a controller called 'Pages',
     * its action called 'display', and we pass a param to select the view file
     * to use (in this case, src/Template/Pages/home.ctp)...
     *
    $routes->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);

     **
     * ...and connect the rest of 'Pages' controller's URLs.
     *
    $routes->connect('/pages/*', ['controller' => 'Pages', 'action' => 'display']);
     */

    /**
     * Connect independent pages to main controller PagesController (default name, kept)
     * 404 error page controlled by default error handler, using View/Errors/error###.ctp
     * TODO: Modify error handling to treat missing pages as 404 instead of invalid controller 400
     */
    // Home page
    $routes->connect('/', ['controller' => 'pages', 'action' => 'display', 'index']);
    // Alias of home page, for linking in relative links
    $routes->connect('/index', ['controller' => 'pages', 'action' => 'display', 'index']);
    // Donor page
    $routes->connect('/donors', ['controller' => 'pages', 'action' => 'display', 'donors']);
    // Browse page
    $routes->connect('/browse', ['controller' => 'pages', 'action' => 'display', 'browse']);

    /**
     * Connect pages requiring user data to UserController
     * Includes login, logout, password reset, passports, etc.
     * TODO: Implement UserController
     */
    
    // Login (send hashed attempt, create session)
    $routes->connect('/login', ['controller' => 'user',
      'action' => 'login']);
    // Register (create and insert user)
    $routes->connect('/signup', ['controller' => 'user', 'action' => 'signup']);
    // Profile page (loads liked/disliked videos)
    $routes->connect('/profile', ['controller' => 'user',
      'action' => 'display', 'profile']);
    // TODO: Login via LinkedIn (implement passport, automatic registration)
    //$routes->connect('/auth/linkedin', ['controller' => 'user',
    //  'action' => 'display', 'profile']);
    // TODO: Login via Facebook (as above)
    //$routes->connect('/auth/facebook', ['controller' => 'user',
    //  'action' => 'display', 'profile']);
    // Logout (Clear session)
    $routes->connect('/logout', ['controller' => 'user',
      'action' => 'logout']);
    // TODO: Account recovery (sends email to linked email with reset link)
    $routes->connect('/recover-account', ['controller' => 'user',
      'action' => 'recover']);
    // TODO: Password reset from recovery link (code lookup, password update)
    //$routes->connect('/reset-password', ['controller' => 'user',
    //  'action' => 'display', 'profile']);
    // TODO: Set new password while already logged in (password update)
    //$routes->connect('/new-password', ['controller' => 'user',
    //  'action' => 'display', 'profile']);

    /**
     * Connect pages requiring career information to CareerController
     * Includes SOC codes (Search, random)
     */
    // Combined access to eventual single file return
    // focus=video (Find video linked to SOC, questions, etc.)
    // focus=salary (Salary data)
    // focus=education (Education data)
    // focus=skills (Skills data)
    // focus=outlook (Outlook data)
    // focus=world-of-work (Skills, intelligences data)
    $routes->connect('/career/:soc/:focus', ['controller' => 'career',
      'action' => 'displayCareerSingle'], ['pass' => ['soc', 'focus'],
        'soc' => '[0-9]{2}-[0-9]{4}']);
    // If bare soc entered, redirect to video
    $routes->connect('/career/:soc', ['controller' => 'career',
      'action' => 'displayCareerSingle'], ['pass' => ['soc'],
        'soc' => '[0-9]{2}-[0-9]{4}']);
    // If no soc given, go to random soc
    // Disabled, instead goes to error page
    //$routes->connect('/career/*', ['controller' => 'career',
    //  'action' => 'redirectRandom']);
    // Special case of above, explicitly defined as link exists
    // May take x and y parameters for weighted randomness on World of Work graphic
    $routes->connect('/career/random', ['controller' => 'career',
      'action' => 'redirectRandom']);
    // Search results from search bar or browse search
    $routes->connect('/career/search', ['controller' => 'career',
      'action' => 'search']);
    // AJAX JSON for autocomplete results
    $routes->connect('/career/autocomplete', ['controller' => 'career',
      'action' => 'getAutoComplete']);
    /* Old separate-page-per-type, may be more stable but requires load for each page
    $routes->connect('/career/:soc/:focus', ['controller' => 'career',
      'action' => 'displayCareer'], ['pass' => ['soc','focus'],
        'soc' => '[0-9]{2}-[0-9]{4}',
        'focus' => 'video|salary|education|skills|outlook|world\-of\-work']);
    // Invalid focus goes to video via Controller to modify URL
    $routes->connect('/career/:soc/*', ['controller' => 'career',
      'action' => 'displayCareer'], ['pass' => ['soc'],
        'soc' => '[0-9]{2}-[0-9]{4}']);
    */
    
    /**
     * Connect administrator actions to AdminController
     * TODO: Implement AdminController
     */
    // Above, for a specific soc code (no support planned for set of socs)
    $routes->connect('/admin/videos',
      ['controller' => 'admin', 'action' => 'displayVideos', 'all']);
    // Above, for a specific soc code or set of socs
    // Cannot enforce regex on multiple args, processing done in controller
    $routes->connect('/admin/videos/*',
      ['controller' => 'admin', 'action' => 'displayVideos']);
    // Back-end page/AJAX to receive a file, create a folder, and update rows
    $routes->connect('/admin/upload', ['controller' => 'admin',
      'action' => 'uploadVideos']);
    // Filesystem maintenance page, listing orphaned videos and folders
    $routes->connect('/admin/orphans', ['controller' => 'admin',
      'action' => 'displayOrphans']);
    // TODO: Summary page of traffic, etc. Entry point for admin portal
    $routes->connect('/admin/', ['controller' => 'admin',
      'action' => 'displaySummary']);
    $routes->connect('/admin/summary', ['controller' => 'admin',
      'action' => 'displaySummary']);
    // Display user data (single user, view history, option to adminize)
    $routes->connect('/admin/user/:uid', ['controller' => 'admin',
      'action' => 'displayUser'], ['pass'=>['uid'], 'uid'=>'[0-9]+']);
    // Adds or removes privileges from a user
    // TODO: More complex grant/revoke, MySQL model?
    $routes->connect('/admin/user/:uid/:to', ['controller' => 'admin',
      'action' => 'setAdmin'], ['pass'=>['uid', 'to'],
      'uid'=>'[0-9]+', 'to'=>'admin|unadmin']);

    /**
     * Connect video rating pages to AlgorithmController
     * TODO: Implement AlgorithmController
     */
    // TODO: AJAX on button press, update ratings, return next video SOC
    // Takes filter parameters (updated clientside by JS)
	$routes->connect('/algorithm/checkrating/:rating/:soc', ['controller' => 'algorithm',
	  'action' => 'checkRating'], ['pass' => ['rating', 'soc'], 'rating' => 'up|mid|down|none']);
    $routes->connect('/algorithm/nextcareer/:rating/:old_soc', ['controller' => 'algorithm',
      'action' => 'nextCareer'], ['pass' => ['rating', 'old_soc'], 'rating' => 'up|mid|down']);

    /**
     * Connect catchall routes for all controllers.
     *
     * Using the argument `DashedRoute`, the `fallbacks` method is a shortcut for
     *    `$routes->connect('/:controller', ['action' => 'index'], ['routeClass' => 'DashedRoute']);`
     *    `$routes->connect('/:controller/:action/*', [], ['routeClass' => 'DashedRoute']);`
     *
     * Any route class can be used with this method, such as:
     * - DashedRoute
     * - InflectedRoute
     * - Route
     * - Or your own route class
     *
     * You can remove these routes once you've connected the
     * routes you want in your application.
     */
    $routes->fallbacks(DashedRoute::class);
});

/**
 * Load all plugin routes. See the Plugin documentation on
 * how to customize the loading of plugin routes.
 */
Plugin::routes();
