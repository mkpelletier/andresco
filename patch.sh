#!/bin/bash

echo 
echo Andresco: Moodle-Alfresco Integration by Androgogic
echo Patching Moodle 2.4 core files
echo -----------------------------------------------------------------------------

# Make sure you specify the location of your Moodle 2.4 installation below
moodle_home={specify_moodle_home_here}

patch -R $moodle_home/admin/repositoryinstance.php < admin/repositoryinstance.php.patch
patch -R $moodle_home/admin/settings/plugins.php < admin/settings/plugins.php.patch
patch -R $moodle_home/files/renderer.php < files/renderer.php.patch
patch -R $moodle_home/mod/resource/mod_form.php < mod/resource/mod_form.php.patch
patch -R $moodle_home/mod/url/mod_form.php < mod/url/mod_form.php.patch
patch -R $moodle_home/repository/filepicker.js < repository/filepicker.js.patch
patch -R $moodle_home/repository/lib.php < repository/lib.php.patch
patch -R $moodle_home/repository/repository_ajax.php < repository/repository_ajax.php.patch

echo -----------------------------------------------------------------------------
echo Patching complete
echo If you see any errors above you will need to individually examine the relevant file\(s\)
echo
