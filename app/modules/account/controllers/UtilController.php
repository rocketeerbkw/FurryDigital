<?php
namespace Modules\Account\Controllers;

class UtilController extends BaseController
{
    public function fixfoldersAction()
    {
        // TODO: Rewrite

        /*
        _guestcheck();

        $path_to_artfolder_skel = '/srv/www/static_assets/artfolder_skel';
        $path_to_artfolder      = ART_DIR.'/art'.CURRENT_ART_FOLDER_NUMBER.'/'.$_USER['lower'];
        $path_check = ART_DIR.'/art/'.$_USER['lower'];

        if(!file_exists($path_check) || !realpath($path_check)) {
            // symlink in /art does not exist
            // or the symlink is not valid
            //echo '<!-- fixing -->';

            if(!file_exists($path_to_artfolder)) {
                //echo '<!-- target eoes not exist -->';
                $command  = 'cp -R '.escapeshellarg($path_to_artfolder_skel). ' '.escapeshellarg($path_to_artfolder);

                // copy skeleton directory
                exec($command, $cmd_output, $cmd_result);

                if($cmd_result > 0) {
                    // error on copy
                    echo 'Errors encountered while fixing artfolders. Please contact admin@furry.digital to let us know.';
                    exit;
                }
            }

            $command2 = 'chmod -R 775 '.escapeshellarg($path_to_artfolder);
            // set permissions on skeleton diretory
            exec($command2);

            $current_dir = getcwd();
            // enter primary art folder
            chdir(ART_DIR.'/art/');
            // symlink the secondary art folder back into the primary
            symlink('../art'.CURRENT_ART_FOLDER_NUMBER.'/'.$_USER['lower'], $_USER['lower']);
            chdir($current_dir);
            //
        } else {
            // everything seems to be okay
            //echo '<!-- skipping -->';
        }
        */
    }
}