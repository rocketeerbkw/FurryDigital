{%- macro submission_data(upload) %}
    <!-- Need to remove the imgresizer class when showing the preview (Need to find out why) -->
    <img id="submissionImg" title="Click to change the View" class="imgresizer" alt="{{ upload.title }}" src="{% if upload.upload_type == constant('\Entity\Upload::TYPE_IMAGE') %} {{ upload.getFullUrl() }} {% else %} {{ upload.getFullUrl() }} {% endif %}" style="cursor: pointer;" />
{%- endmacro %}

<div class="bg1">
    <div class="aligncenter imgshad p10b p10t">
        {% if upload.upload_type == constant('\Entity\Upload::TYPE_IMAGE') %}
            {{ submission_data(upload) }}
        {% elseif upload.upload_type == constant('\Entity\Upload::TYPE_VIDEO') %}
            {% if file_mime == 'application/x-shockwave-flash' %}
                <object type="application/x-shockwave-flash" width="{{ upload.width }}" height="{{ upload.height }}" data="{{ upload.getFullUrl() }}">
                    <param name="movie"   value="{{ upload.getFullUrl() }}" />
                    <param name="quality" value="high" />
                </object>
            {% else %}
                <!-- NOT YET IN USE -->
                <video width="320" height="240" autoplay>
                    <source src="{{ upload.getFullUrl() }}">
                    Your browser does not support the video tag.
                </video>
            {% endif %}
        {% elseif upload.upload_type == constant('\Entity\Upload::TYPE_AUDIO') %}
            <span class="imgshad aligncenter">
                {{ submission_data(upload) }}
            </span>
            
            <br />
            <br />
            <audio controls>
                <source src="{{ upload.getFullUrl() }}">
                Your browser does not support the audio element.
            </audio>
        {% elseif upload.upload_type == constant('\Entity\Upload::TYPE_TEXT') %}
            <span class="aligncenter imgshad">
                {{ submission_data(upload) }}
            </span>
            <br />

            <center class="p20l p20r">
                <div align="left" style="max-width:1024px;min-width:400px">
                    <!-- Add document viewer here! -->
                    Could not open this filetype...
                </div>
            </center>
        {% endif %}
    </div>

    <!-- folder minigalleries -->
    <!-- INSERT GALLERY CODE HERE, temporarily removed due and can be found at gallery.code -->
    
    <div class="aligncenter p10b">
        <!--if(array_key_exists('before', $adjacent_submissions[$folder_data['folder_id']])){$sub_data = $adjacent_submissions[$folder_data['folder_id']]['before'][0];?>
            <span class="button hideonmobile submission rounded"><a href="/view/<?=$sub_data['rowid']?>/" class="prev dotted">&#x276e;&#x276e;&nbsp;Older</a></span>
        }-->

        {% if auth.isLoggedIn() %}
            <span class="button submission rounded"><a href="{{ url.named('upload_fav', ['id': upload.id, 'key': upload_csrf_str]) }}">{{ is_favorited }}</a></span>
        {% endif %}
        <!--if($_USER['vars']->get('gallery_navigation')!='minigallery'){
            <span class="button submission rounded"><a href="<?=$folder_data['url']?>" title="<?=$folder_data['num_files']?> submissions">Go to <?=$folder_data['folder_name']?></a></span>
        }-->
        <span class="button submission rounded"><a href="{{ upload.getFullUrl() }}">Download</a></span>
        {% if auth.isLoggedIn() %}
            <span class="button submission rounded"><a href="/newpm/{{ upload.user.lower }}/">Note {{ upload.user.username }}</a></span>
        {% endif %}

        <!--if(array_key_exists('after', $adjacent_submissions[$folder_data['folder_id']])){$sub_data = $adjacent_submissions[$folder_data['folder_id']]['after'][0];?>
            <span class="button hideonmobile submission rounded"><a href="/view/<?=$sub_data['rowid']?>/" class="next dotted">Newer &nbsp;&#x276f;&#x276f;</a></span>
        }-->
    </div>

    <div class="flextitlecol bg4 borderbot">
        <div class="flextitleitem1 bg4">
            <a href="/user/{{ upload.user.lower }}/"><img class="floatleft submissionusericon trans p10l p10r" style="" src="{{ upload.user.getAvatar() }}"></a>
            <div class="submissiontitlecontent trans">{{ upload.title }}</div>
            <div class="auto_link submissiontitleuser"><a href="/user/{{ upload.user.lower }}/"><strong>{{ upload.user.username }}</strong></a> | <abbr class="moment-ago" mtime="{{ upload.created_at }}">{{ created_at }}</abbr></div> 
        </div>

        <div class="flextitleitem2 bg4 aligncenter">
            <div class="flextitlerow" style="width:100%">

                <div class="flextitlestat trans order1 bg4 p10r" style="top:2px">
                    <h3>Views</h3>
                    <span class="">{{ upload.views }}</span>
                </div>

                <div class="flextitlestat trans order2 bg4 p10lr" style="top:2px">
                    <h3>Favs</h3>
                    <span class="">
                        {% if is_owner %}
                            <a href="/favslist/{{ upload.id }}/">{{ upload.favorites|length }}</a>
                        {% else %}
                            {{ upload.favorites|length }}
                        {% endif %}
                    </span>
                </div>

                <div class="flextitlestat trans order3 bg4 p10lr" style="top:2px">
                    <h3>Comments</h3>
                    <span class="">{{ upload.comments|length }}</span>
                </div>

                <div class="flextitlestat trans order4 fontsize16 p10l p20r">
                    <h3>Rating</h3>
                    <div class="bg4 rating-box {{ upload.getRatingReadable() }}">
                        {{ upload.getRatingReadable()|capitalize }}
                    </div>
                </div>
            </div>
        </div>

        <div class="bg3 p20 flextitleitem3 hideondesktop borderbot auto_link">
            {{ upload.description }}
        </div>
    </div>
    
    {% if acl.isAllowed('administer all') %}
        {{ partial("upload/admin_options") }}
    {% endif %}
    
    {% if is_owner %}
        <div class="p20lr p10t p10b bg2 auto_link borderbot">
            <h3 class="inline">Owner Options</h3>
            <a class="p10l" href="/controls/uploads/changeinfo/{{ upload.id }}">Edit Info</a>
            <a class="p10l" href="/controls/uploads/changethumbnail/{{ upload.id }}">Update Thumbnail</a>
            <a class="p10l" href="/controls/uploads/edit/{{ upload.id }}">Update Submission File</a>
        </div>
    {% endif %}

    <div class="bg3 p20 hideonmobile borderbot auto_link">
        {{ upload.description }}
    </div>

    <div class="tags-row p20lr">
        <div class="p10b hideonmobile">
            {% if upload.category %}<strong>Category:</strong> {{ upload.getCategoryReadable() }}  > {% endif %}
            {% if upload.subtype %}{{ upload.getSubtypeReadable() }}{% endif %}
            {% if upload.width and upload.height %} | <strong>Resolution:</strong> {{ upload.width }}x{{ upload.height }}px{% endif %}
            {% if upload.species %}| <strong>Species:</strong> {{ upload.getSpeciesReadable() }}{% endif %}
            {% if upload.gender %}| <strong>Gender:</strong> {{ upload.getGenderReadable() }}{% endif %}
        </div>
        
        {% if keyword_arr %}
            Keywords:
                {% for keyword in keyword_arr %}
                    <span class="tags"><a href="/search/@keywords {{ keyword }}">{{ keyword }}</a></span>
                {% endfor %}
        {% endif %}
    </div>
</div>

{% if acl.isAllowed('administer all') and total_deleted_comments != 0 %}
<div>
    <span class="aligncenter">
            <div class="aligncenter" style="color:orange;padding:5px 0">
                Number of deleted comments on the page: <b>{{ total_deleted_comments }}</b>. Comments removed by staff: <b>{{ total_deleted_comments_by_admin }}</b>, by page owner: <b>{{ total_deleted_comments_by_uploader }}</b>, by comment owner: <b>{{ total_deleted_comments_by_poster }}</b>.
            </div>
    </span>
</div>
{% endif %}

{% if upload.upload_type == constant('\Entity\Upload::TYPE_IMAGE') %}
<script type="text/javascript">
    (function() {
        var small_url   = "{{ upload.getSmallUrl() }}",
            full_url    = "{{ upload.getFullUrl() }}",
            is_full     = {{ fullview }},
            sub_elem    = jQuery("#submissionImg")
        
        function setImage() {
            sub_elem.attr('src', (is_full ? full_url : small_url))
        }
        
        function toggleImageSize() {
            is_full = !is_full
            
            console.log('test')
            
            setImage()
        }
        
        jQuery("#submissionImg").click(toggleImageSize)
    })()
</script>
{% endif %}
