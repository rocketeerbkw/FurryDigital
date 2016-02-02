{% extends "cpanel.volt" %}

{% block content %}
    {% set title="My Uploads" %}

    <form method="post" action="">
        <input type="hidden" name="csrf" value="{{ csrf.generate('uploads') }}">
    <div id="page-controls-submissions" class="twocontent">
        <div class="container-item-top">
            <h3>My Uploads</h3>
        </div>

        <div class="container-item-bot-last">
            <div class="rounded bg1 p10"><strong>Hint:</strong> Clicking the icon on the bottom right your submission's thumbnail will shows which folders is assigned to (if any).</div>

            <div class="folder_management">
                <div class="flex-container-submission p10t">
                    <div class="flex-container-submission-item1 actions">
                        <h3>Assign to Existing Folder</h3>
                        <p>Assign any selected submissions to an existing folder.</p>
                    </div>
                    <div class="flex-container-submission-item2 actions">
                        {{ select_static('folder_id', folders) }}
                        <button type="submit" name="action" value="assignfolder" class="button type-edit">Assign</button>
                    </div>
                </div>

                <div class="flex-container-submission p10t">
                    <div class="flex-container-submission-item1">
                        <h3>Assign to New Folder</h3>
                        <p>Create a new folder, and <em>assign any selected submissions</em> to the newly created folder.</p>
                    </div>
                    <div class="flex-container-submission-item2">
                        <strong>Folder:</strong> <input type="text" class="textbox" name="new_folder_name" />
                        <button type="submit" name="action" value="createfolder" class="button p5l type-add">Create</button>
                    </div>
                </div>

                <div class="flex-container-submission p10t">
                    <div class="flex-container-submission-item1">
                        <h3>Unassign From Folder(s)</h3>
                        <p>Remove the selected submission(s) from all folders they are currently assigned to.</p>
                    </div>
                    <div class="flex-container-submission-item2 valign">
                        <button type="submit" name="action" value="removefolder" class="button type-remove">Unassign from Folders</button>
                    </div>
                </div>
            </div>

            <div class="flex-container-submission scraps_management ">
                <div class="flex-container-submission-item1">
                    <h3>Move to Gallery or Scraps</h3>
                    <p>Move selected submissions to your Gallery or Scraps.</p>
                </div>
                <div class="flex-container-submission-item2 aligncenter">
                    <button type="submit" name="action" value="movetoscraps" class="button type-edit">Move to Scraps</button>
                    <button type="submit" name="action" value="movefromscraps" class="button type-edit">Move to Gallery</button>
                </div>
            </div>

            <div class="flex-container-submission p10b ">
                <div class="flex-container-submission-item1">
                    <h3>Delete Submissions</h3>
                    <p>Permanently delete all selected submissions from your gallery by clicking the button below.</p>
                </div>
                <div class="flex-container-submission-item2">
                    <button type="submit" name="action" value="delete" class="button  type-remove">Delete Submissions</button>
                </div>
            </div>

            <div class="rounded bg1 p10"><strong>Note:</strong> When removing multiple submissions the page may time out. Some progress will still be made, and you can simply repeat the process until you get the desired results.</div>

            {{ paginate(pager) }}

            <br><br>

            {% if pager %}
                <center class="flow manage-submissions with-checkboxes-titles-usernames force-owner-mode thumb-size-<?=$thumbnail_size ?>">
                    {% for row in pager %}
                        <b id="sid_{{ row.id }}" class="r-{{ row.getRatingReadable() }} t-{{ row.getUploadTypeName() }}>"><u><s><a href="{{ url.named('upload_view', ['id': row['id']]) }}" target="_blank"><img alt="" src="{{ row.getThumbnailUrl() }}"/><i class="icon" title="Click for description"></i></a></s></u><small><input type="checkbox" name="ids[]" value="{{ row.id }}"></small><span title="{{ row.title }}">{{ row.title }}</span></b>
                    {% endfor %}
                </center>
            {% else %}
                <div style="text-align: center; font-size: 14pt;"><b><i>There are no submissions to list</i></b></div>
            {% endif %}

            <br><br>

            {{ paginate(pager) }}

        </div>
    </div>
    </form>

    <script type="text/javascript">
        var descriptions = {{ image_data|json_encode }};

        Queue.add(function(){
            // Register the click handlers for easier checkbox usage
            $$('center.flow > b').each(function(elm){
                elm.observe('click', function(evt){
                    var elm = evt.element();
                    console.log(elm);


                    var elm_tagname = elm.tagName.toLowerCase();
                    if(elm_tagname != 'img' && elm_tagname != 'a'){
                        // process clicks only on non-image and non-link elements
                        console.log('process click');
                        var cell  = elm_tagname == 'b' ? elm : elm.up('b');
                        var input = cell.down('input[type="checkbox"]');
                        if(elm_tagname != 'input'){
                            input.checked = !input.checked;
                        }
                        input.checked ? cell.addClassName('checked') : cell.removeClassName('checked');
                    } else {
                        console.log('skip click');
                    }
                });

                // Initialize the checked messages
                var input = elm.down('input[type="checkbox"]');
                input.checked ? elm.addClassName('checked') : elm.removeClassName('checked');
            });

            // Handle the 'invert selection' button
            $$('#page-controls-submissions div.selection_management .invert-selection').each(function(elm){
                elm.observe('click', function(evt){
                    $$('center.flow > b input[type="checkbox"]').each(function(elm){
                        elm.checked = !elm.checked;

                        var cell = elm.up('b');
                        elm.checked ? cell.addClassName('checked') : cell.removeClassName('checked');
                    });
                });
                elm.removeClassName('hidden');
            });

            // Handle the 'check-uncheck' button
            //
            var flag_checked = false;
            $$('#page-controls-submissions div.selection_management .check-uncheck').each(function(elm){
                elm.observe('click', function(evt){
                    $$('center.flow > b input[type="checkbox"]').each(function(elm){
                        elm.checked = !flag_checked;

                        var cell = elm.up('b');
                        elm.checked ? cell.addClassName('checked') : cell.removeClassName('checked');
                    });
                    flag_checked = !flag_checked;
                });
                elm.removeClassName('hidden');
            });


            // do stuff
            $$('center.flow > b i.icon').each(function(elm){
                elm.observe('click', description_icon_click);
            });
        });
    </script>

{% endblock %}