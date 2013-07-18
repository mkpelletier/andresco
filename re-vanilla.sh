#!/bin/bash

echo 
echo Andresco: Moodle-Alfresco Integration by Androgogic
echo Re-vanilla from Andresco to Moodle core files
echo -----------------------------------------------------------------------------

# location of your moodle vanilla
moodle_vanilla={path to moodle vanilla here}

# location of target environment you want to re-vanilla
moodle_target={path to moodle target environment here}

cp -v $moodle_vanilla/admin/repositoryinstance.php $moodle_target/admin/repositoryinstance.php
cp -v $moodle_vanilla/admin/settings/plugins.php $moodle_target/admin/settings/plugins.php
cp -v $moodle_vanilla/files/renderer.php $moodle_target/files/renderer.php
cp -v $moodle_vanilla/mod/resource/mod_form.php $moodle_target/mod/resource/mod_form.php
cp -v $moodle_vanilla/mod/url/mod_form.php $moodle_target/mod/url/mod_form.php
cp -v $moodle_vanilla/repository/filepicker.js $moodle_target/repository/filepicker.js
cp -v $moodle_vanilla/repository/lib.php $moodle_target/repository/lib.php
cp -v $moodle_vanilla/repository/repository_ajax.php $moodle_target/repository/repository_ajax.php

echo -----------------------------------------------------------------------------
echo Re-vanilla complete
echo If you see any errors above you will need to individually examine the relevant file\(s\)
echo
