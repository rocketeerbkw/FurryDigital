{% extends "cpanel.volt" %}

{% block content %}
    {% set title="My Uploads" %}

    <form method="post" action="">
        <input type="hidden" name="csrf" value="{{ csrf.generate('uploads') }}">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">My Uploads</h3>
            </div>
            <div class="panel-body">
                <div class="multirows">
                    <div class="row">
                        <div class="col-md-7">
                            <h3>Assign to Existing Folder</h3>
                            <p>Assign any selected submissions to an existing folder.</p>
                        </div>
                        <div class="col-md-5">
                            {{ select_static('folder_id', folders) }}
                            <button type="submit" name="action" value="assignfolder" class="btn btn-default btn-sm type-edit">Assign</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-7">
                            <h3>Assign to New Folder</h3>
                            <p>Create a new folder, and <em>assign any selected submissions</em> to the newly created folder.</p>
                        </div>
                        <div class="col-md-5">
                            <strong>Folder:</strong> <input type="text" class="textbox" name="new_folder_name" />
                            <button type="submit" name="action" value="createfolder" class="btn btn-default btn-sm p5l type-add">Create</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-7">
                            <h3>Unassign From Folder(s)</h3>
                            <p>Remove the selected submission(s) from all folders they are currently assigned to.</p>
                        </div>
                        <div class="col-md-5">
                            <button type="submit" name="action" value="removefolder" class="btn btn-default btn-sm type-remove">Unassign from Folders</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-7">
                            <h3>Move to Gallery or Scraps</h3>
                            <p>Move selected submissions to your Gallery or Scraps.</p>
                        </div>
                        <div class="col-md-5">
                            <button type="submit" name="action" value="movetoscraps" class="btn btn-default btn-sm type-edit">Move to Scraps</button>
                            <button type="submit" name="action" value="movefromscraps" class="btn btn-default btn-sm type-edit">Move to Gallery</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-7">
                            <h3>Delete Submissions</h3>
                            <p>Permanently delete all selected submissions from your gallery.</p>
                        </div>
                        <div class="col-md-5">
                            <button type="submit" name="action" value="delete" class="btn btn-default btn-sm  type-remove">Delete Submissions</button>
                        </div>
                    </div>
                </div>

                <div class="rounded bg1 p10"><strong>Note:</strong> When removing multiple submissions the page may time out. Some progress will still be made, and you can simply repeat the process until you get the desired results.</div>

                <div class="pagination">
                    {{ paginate(pager) }}
                </div>

                {% if pager %}
                    <div class="grid">
                        <div class="grid-sizer"></div>
                        {% for row in pager %}
                            <div id="sid_{{ row['id'] }}" class="grid-item r-{{ row.getRatingReadable() }} t-{{ row.getUploadTypeName() }}">
                                <a class="image" href="{{ url.get('view/'~row['id']) }}">
                                    <img alt="" src="{{ row['thumbnail_url'] }}">
                                </a>
                                <span class="title" title="{{ row.title }}">
                                    <label>
                                        <input type="checkbox" name="ids[]" value="{{ row.id }}">
                                        {{ row.title }}
                                    </label>
                                </span>
                            </div>
                        {% endfor %}
                    </div>
                {% else %}
                    <div style="text-align: center; font-size: 14pt;"><b><i>There are no submissions to list</i></b></div>
                {% endif %}

                <div class="pagination">
                    {{ paginate(pager) }}
                </div>
            </div>
        </div>
    </form>

    <script type="text/javascript">
    jQuery(function($) {
        $('input[name="ids[]"]').change(function() {
            if($(this).is(":checked"))
                $(this).closest('div').addClass('checked');
            else
                $(this).closest('div').removeClass('checked');
        });
    });
    </script>

    {#
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

            // Handle the 'invert selection' btn btn-default btn-sm
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

            // Handle the 'check-uncheck' btn btn-default btn-sm
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
    #}
{% endblock %}