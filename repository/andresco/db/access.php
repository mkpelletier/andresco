<?php

/**
 * Andresco Repository Security
 *
 * Part of the Andresco Project
 * http://code.google.com/p/andresco
 * 
 * @copyright 	2012+ Androgogic Pty Ltd
 * @author		Praj Basnet
 *
 **/

$capabilities = array(

    'repository/andresco:view' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'user' => CAP_ALLOW
        )
    )
);
