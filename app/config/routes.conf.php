<?php
return array(

    'default_module' => 'frontend', // Use "frontend", because "default" causes namespacing problems.
    'default_controller' => 'index',
    'default_action' => 'index',

    'custom_routes' => array(
        // Homepage.
        '/' => array(
            'module' => 'frontend',
            'controller' => 'index',
            'action' => 'index',
            'name' => 'home',
        ),
        // Terms of Service
        '/tos' => array(
            'module' => 'frontend',
            'controller' => 'info',
            'action' => 'tos',
            'name' => 'tos',
        ),
        // Code of Conduct
        '/coc' => array(
            'module' => 'frontend',
            'controller' => 'info',
            'action' => 'coc',
            'name' => 'coc',
        ),
        // Staff
        '/staff' => array(
            'module' => 'frontend',
            'controller' => 'info',
            'action' => 'staff',
            'name' => 'staff',
        ),
        // Acceptable Upload Policy
        '/aup' => array(
            'module' => 'frontend',
            'controller' => 'info',
            'action' => 'aup',
            'name' => 'aup',
        ),

        /**
         * Account Module
         */

        '/login' => array(
            'module' => 'account',
            'controller' => 'login',
            'action' => 'index',
            'name' => 'login',
        ),
        '/register' => array(
            'module' => 'account',
            'controller' => 'register',
            'action' => 'index',
            'name' => 'register',
        ),
        '/logout' => array(
            'module' => 'account',
            'controller' => 'logout',
            'action' => 'index',
            'name' => 'logout',
        ),

        // Content Management
        '/submit' => array(
            'module' => 'account',
            'controller' => 'uploads',
            'action' => 'edit',
            'name' => 'submit',
        ),
        '/upload' => array(
            'module' => 'account',
            'controller' => 'uploads',
            'action' => 'edit',
            'name' => 'upload',
        ),
        
        /**
         * Profile Module
         */
        
        '/view/{id:[0-9]+}' => array(
            'name' => 'upload_view',
            'module' => 'profile',
            'controller' => 'upload',
            'action' => 'index',
        ),
        '/journal/{id:[0-9]+}' => array(
            'name' => 'journal_view',
            'module' => 'profile',
            'controller' => 'journals',
            'action' => 'view',
        ),

        '/user/{username:[a-zA-Z0-9_\-]+}' => array(
            'name' => 'user_view',
            'module' => 'profile',
            'controller' => 'index',
            'action' => 'index',
        ),
        '/scraps/{username:[a-zA-Z0-9_\-]+}' => array(
            'name' => 'user_scraps',
            'module' => 'profile',
            'controller' => 'gallery',
            'action' => 'scraps'
        ),
        '/gallery/{username:[a-zA-Z0-9_\-]+}' => array(
            'name' => 'user_gallery',
            'module' => 'profile',
            'controller' => 'gallery',
            'action' => 'index',
        ),
        '/gallery/{username:[a-zA-Z0-9_\-]+}/folder/{folder:[0-9]+}' => array(
            'name' => 'user_gallery_folder',
            'module' => 'profile',
            'controller' => 'gallery',
            'action' => 'index',
        ),
        '/favorites/{username:[a-zA-Z0-9_\-]+}' => array(
            'name' => 'user_favorites',
            'module' => 'profile',
            'controller' => 'favorites',
            'action' => 'index',
        ),
        '/journals/{username:[a-zA-Z0-9_\-]+}' => array(
            'name' => 'user_journals',
            'module' => 'profile',
            'controller' => 'journals',
            'action' => 'index',
        ),
        '/commissions/{username:[a-zA-Z0-9_\-]+}' => array(
            'name' => 'user_commissions',
            'module' => 'profile',
            'controller' => 'commissions',
            'action' => 'index',
        ),
        '/watch/{username:[a-zA-Z0-9_\-]+}' => array(
            'name' => 'user_watch',
            'module' => 'profile',
            'controller' => 'index',
            'action' => 'watch',
        ),
        '/unwatch/{username:[a-zA-Z0-9_\-]+}' => array(
            'name' => 'user_unwatch',
            'module' => 'profile',
            'controller' => 'index',
            'action' => 'unwatch',
        ),
        
        '/view/{id:[0-9]+}/comment/new' => array(
            'name'      => 'upload_comment_new',
            'module'    => 'profile',
            'controller' => 'uploadfeature',
            'action'    => 'reply',
        ),
        
        '/view/comment/edit/{id:[0-9]+}' => array(
            'name'      => 'upload_comment_edit',
            'module'    => 'profile',
            'controller' => 'uploadfeature',
            'action'    => 'edit',
        ),
        
        '/view/comment/hide/{id:[0-9]+}/key/{key:[0-9A-Za-z]+}' => array(
            'name'      => 'upload_comment_hide',
            'module'    => 'profile',
            'controller' => 'uploadfeature',
            'action'    => 'hide',
        ),
        
        '/view/fav/{id:[0-9]+}/key/{key:[0-9A-Za-z]+}' => array(
            'name'      => 'upload_fav',
            'module'    => 'profile',
            'controller' => 'uploadfeature',
            'action'    => 'favorite',
        ),
    ),

);