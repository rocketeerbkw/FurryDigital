<?php
/**
 * Master Submission Edit Form
 */

use Entity\Upload;
use FA\Legacy\Utilities as FAUtils;

$di = \Phalcon\Di::getDefault();
$config = $di['config'];

return array(
    'method'        => 'post',
    'enctype'       => 'multipart/form-data',
    'groups'        => array(

        'files' => array(
            'legend'        => 'Select Files to Submit',
            'elements'      => array(

                'submission'      => array('file', array(
                    'label' => 'File to Submit',
                    'maxSize' => '10M',
                    'description' => 'If you do not want to replace the existing uploaded file, leave this field empty.',
                )),

                'rebuild_thumbnail' => array('radio', array(
                    'label'     => 'Rebuild the thumbnail from this file',
                    'description' => 'If you select "Yes" to this option (and don\'t upload a custom thumbnail below), a new one will be generated for this submission based on the new file you upload above, replacing any existing thumbnail.',
                    'options'   => array(0 => 'No', 1 => 'Yes'),
                    'default'   => 0,
                    'required'  => true,
                )),

                'thumbnail'      => array('file', array(
                    'label' => 'Custom Thumbnail',
                    'allowedTypes' => array('image/jpeg', 'image/gif', 'image/png'),
                    'description' => 'If you would like to use a custom thumbnail image for this submission, upload it here. It will automatically be scaled down to the correct size.',
                )),

            ),
        ),

        'metadata' => array(
            'legend'        => 'Submission Details',
            'elements'      => array(

                'title' => array('text', array(
                    'label' => 'Title',
                    'maxlength' => 60,
                    'class' => 'full-width',
                    'required' => true
                )),

                'description' => array('textarea', array(
                    'label' => 'Description',
                    'maxlength' => 65535,
                    'class' => 'full-width full-height',
                    'required' => true,
                )),

                'rating' => array('radio', array(
                    'label' => 'Rating',
                    'default' => 0,
                    'options' => array(
                        Upload::RATING_GENERAL => '<b>General:</b> Suitable for all-ages.',
                        Upload::RATING_MATURE  => '<b>Mature:</b> Gore, violence or tasteful/artistic nudity or mature themes.',
                        Upload::RATING_ADULT   => '<b>Adult:</b> Explicit or imagery otherwise geared towards adult audiences.',
                    ),
                    'required' => true,
                )),

                'category' => array('select', array(
                    'label' => 'Category',
                    'options' => FAUtils::reverseArray($config->fa->categories->toArray()),
                    'layout' => 'col50',
                )),

                'theme' => array('select', array(
                    'label' => 'Theme',
                    'options' => FAUtils::reverseArray($config->fa->art_types->toArray()),
                    'layout' => 'col50',
                )),

                'species' => array('select', array(
                    'label' => 'Species',
                    'options' => FAUtils::reverseArray($config->fa->species->toArray()),
                    'layout' => 'col50',
                )),

                'gender' => array('select', array(
                    'label' => 'Gender',
                    'options' => FAUtils::reverseArray($config->fa->genders->toArray()),
                    'layout' => 'col50',
                )),

                'keywords'  => array('textarea', array(
                    'label' => 'Keywords',
                    'description' => 'Separate keywords using spaces (e.g. "fox lion male men friends fishing"). Keywords helps user find your submission in the search engine. Per site policy, keywords must be related directly to the content of your submission. Misleading or abusive keywords are not permitted.',
                    'class' => 'full-width',
                    'maxlength' => 65535
                )),

            ),
        ),

        'settings' => array(
            'legend'        => 'Site Settings',
            'elements'      => array(

                'lock_comments'  => array('radio', array(
                    'label' => 'Disable Comments',
                    'default' => 0,
                    'options' => array(0 => 'No', 1 => 'Yes'),
                )),

                'is_scrap'  => array('radio', array(
                    'label' => 'Put in "Scraps" Section',
                    'default' => 0,
                    'options' => array(0 => 'No', 1 => 'Yes'),
                )),

            ),
        ),

        /*
         * TODO: Reimplement folder support
        'folders_grp' => array(
            'legend' => 'Folders',
            'elements' => array(

                /*
                 *
                 <?if(count($groups_with_folders)){?>
        <div class="assign_folders clearfloat">
            <h2 class="pseudo_header">Assign submission to folders (optional)</h2>
            <?foreach($groups_with_folders as $group){?>
                <fieldset>
                    <legend><?=$group['group_name']?></legend>
                <?foreach($group['folders'] as $folder){?>
                    <div class="folder_name">
                        <input id="folder-id-<?=$folder['folder_id']?>" type="checkbox" name="folder_ids[]" value="<?=$folder['folder_id']?>"/>
                        <label for="folder-id-<?=$folder['folder_id']?>"><?=$folder['folder_name']?></label>
                    </div>
                <?}?>
                </fieldset>
            <?}?>
        </div>
    <?}?>

    <div class="create_folder">
        <h2 class="pseudo_header">Assign the submission to a new folder (optional)</h2>
        <div class="actions">
            <strong>Folder name:</strong> <input type="text" class="textbox" name="create_folder_name" />
        </div>
        <p><strong>Note:</strong> Folders have more options than just a name. Please visit the <a href="/controls/folders/submissions/" class="dotted" target="_blank">folder management</a> control panel later to specify them and organize the folders in groups and in the order you see fit.</p>
    </div>

            ),
        ),
        */

        'submit_grp' => array(
            'elements'      => array(

                // CSRF forgery prevention.
                'csrf' => array('csrf'),

                'submit'        => array('submit', array(
                    'type'  => 'submit',
                    'label' => 'Submit',
                    'helper' => 'formButton',
                    'class' => 'btn btn-default'
                )),

            ),
        ),

    ),
);