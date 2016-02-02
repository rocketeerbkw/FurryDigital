<?php
return array(
    'method' => 'post',
    'enctype'       => 'multipart/form-data',

    'elements' => array(
        'formats' => array('markup', array(
            'markup' => '<ul>
                <li><strong>Accepted File Formats:</strong> GIF. While JPG and PNG can be uploaded they will be converted to GIF format, with possible loss of quality.</li>
                <li><strong>Max File Size:</strong> 1MB</li>
                <li><strong>Max Dimensions:</strong> 100x100 pixels</li>
            </ul>',
        )),

        'avatar' => array('file', array(
            'label' => 'Select Avatar File',
            'maxSize' => '1M',
            'allowedTypes' => array('image/jpeg', 'image/png', 'image/gif'),
            'required' => true,
        )),

        // CSRF forgery prevention.
        'csrf' => array('csrf'),

        'submit'        => array('submit', array(
            'type'  => 'submit',
            'label' => 'Upload Avatar',
            'helper' => 'formButton',
            'class' => 'btn btn-default',
        )),

    ),
);