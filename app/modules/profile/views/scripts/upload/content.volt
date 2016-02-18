{%- macro submission_data(upload) %}
    {% if upload.upload_type == constant('\Entity\Upload::TYPE_IMAGE') %}
        <img id="submissionImg" title="Click to enlarge!" class="imgresizer" alt="{{ upload.title|e }}" src="{{ upload.getSmallUrl() }}"  data-full-url="{{ upload.getFullUrl() }}" data-small-url="{{ upload.getSmallUrl() }}" style="cursor: pointer;">
    {% else %}
        <img id="submissionImg" alt="{{ upload.title|e }}" src="{{ upload.getSmallUrl() }}">
    {% endif %}
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
            <br><br>
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
        <div class="btn-group">
            <a class="btn btn-sm btn-default" href="{{ upload.getFullUrl() }}" target="_blank">Download</a>
        {% if auth.isLoggedIn() %}
            {% if is_favorited %}
                <a class="btn btn-sm btn-danger" href="{{ url.named('upload_fav', ['id': upload.id, 'key': upload_csrf_str]) }}">Unfavorite</a>
            {% else %}
                <a class="btn btn-sm btn-success" href="{{ url.named('upload_fav', ['id': upload.id, 'key': upload_csrf_str]) }}">Favorite</a>
            {% endif %}
            <a class="btn btn-sm btn-default" href="{{ url.route(['module': 'account', 'controller': 'messages', 'action': 'compose']) }}?recipient={{ upload.user.lower }}">Message {{ upload.user.username|e }}</a>
        {% endif %}
        </div>
    </div>

    <div class="flextitlecol bg4 borderbot">
        <div class="flextitleitem1 bg4">
            <a href="/user/{{ upload.user.lower }}/"><img class="floatleft submissionusericon trans p10l p10r" style="" src="{{ upload.user.getAvatar() }}"></a>
            <div class="submissiontitlecontent trans">{{ upload.title|e }}</div>
            <div class="auto_link submissiontitleuser"><a href="/user/{{ upload.user.lower }}/"><strong>{{ upload.user.username|e }}</strong></a> | {{ app.formatDate(upload.created_at) }}</div>
        </div>

        <div class="flextitleitem2 bg4 aligncenter">
            <div class="flextitlerow" style="width:100%">

                <div class="flextitlestat trans order1 bg4 p10r" style="top:2px">
                    <h3>Views</h3>
                    <span>{{ upload.views }}</span>
                </div>

                <div class="flextitlestat trans order2 bg4 p10lr" style="top:2px">
                    <h3>Favs</h3>
                    <span>
                        {{ upload.favorites|length }}
                    </span>
                </div>

                <div class="flextitlestat trans order3 bg4 p10lr" style="top:2px">
                    <h3>Comments</h3>
                    <span>{{ upload.comments|length }}</span>
                </div>

                <div class="flextitlestat trans order4 fontsize16 p10l p20r">
                    <h3>Rating</h3>
                    <div class="bg4 rating-box {{ upload.getRatingReadable() }}">
                        {{ upload.getRatingReadable()|capitalize }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {% if is_owner or acl.isAllowed('manage uploads') %}
        <div class="p20lr p10t p10b bg2 auto_link borderbot">
            <h3 class="inline">Manage This Upload</h3>
            <a class="p10l" href="{{ url.route(['module': 'account', 'controller': 'uploads', 'action': 'edit', 'id': upload.id]) }}">Edit File &amp; Details</a>
        </div>
    {% endif %}

    <div class="bg3 p20 borderbot auto_link">
        {{ parser.message(upload.description) }}
    </div>

    <div class="tags-row p20lr">
        <div class="p10b hideonmobile">
            {% if upload.category %}<strong>Category:</strong> {{ upload.getCategoryReadable() }}  > {% endif %}
            {% if upload.theme %}{{ upload.getThemeReadable() }}{% endif %}
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
jQuery(function($) {
    var is_full = false;
    $("#submissionImg").on('click', function (e) {
        if (is_full)
            $(this).attr('src', $(this).data('small-url'));
        else
            $(this).attr('src', $(this).data('full-url'));

        is_full = !is_full;
    });
});
</script>
{% endif %}
