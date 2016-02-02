<!--- USER NAV --->
<div class="userpage-tabs autoselect" rel="{% if usernav_tab is not empty %}{{ usernav_tab }}{% else %}{{ dispatcher.getControllerName() }}{% endif %}">
{# if($user_allowed_gallery_nuke){?
    <a class="stats" href="/adminaction/?action=nuke_gallery&lower=<?=urlencode($artist_lower)?>" >Nuke Gallery</a>
} #}

{% if acl.isAllowed('administer all') %}
    <a class="stats hideonmobile" href="/user/history/<?=$artist_lower?>/">History</a>
{% endif %}
{% if acl.isAllowed('administer all') OR (auth.isLoggedIn() AND user.lower == owner.lower) %}
    <a class="stats hideonmobile" href="/stats/<?=$artist_lower?>/submissions/">Stats</a>
{% endif %}
{% if user.lower == owner.lower %}
    <a class="stats hideonmobile" href="{{ url.route(['module': 'account', 'controller': 'settings', 'action':'profile']) }}">Edit My Profile</a>
{% endif %}
{% if auth.isLoggedIn() AND user.lower != owner.lower %}
    <a class="stats" href="{{ url.get('newpm/'~owner.lower) }}">Note</a>
    {% if is_watching %}
        <a class="stats" href="{{ url.named('user_unwatch', ['username': owner.lower]) }}" class="red">- Unwatch</a>
    {% else %}
        <a class="stats" href="{{ url.named('user_watch', ['username': owner.lower]) }}" class="green">+ Watch</a>
    {% endif %}
{% endif %}

    <a rel="index" href="{{ url.named('user_view', ['username': owner.lower]) }}">Profile</a>
    <a rel="gallery" href="{{ url.named('user_gallery', ['username': owner.lower]) }}">Gallery</a>
    <a rel="scraps" href="{{ url.named('user_scraps', ['username': owner.lower]) }}">Scraps</a>
    <a rel="favorites" href="{{ url.named('user_favorites', ['username': owner.lower]) }}"><span class="hideondesktop">Favs</span><span class="hideonmobile">Favorites</span></a>
    <a rel="journals" href="{{ url.named('user_journals', ['username': owner.lower]) }}">Journals</a>

{% if owner.getVariable('has_commissions') AND user.lower != owner.lower %}
    <a href="{{ url.named('user_commissions', ['username': owner.lower]) }}"><span class="hideondesktop">Comm</span><span class="hideonmobile">Commission</span></a>
{% endif %}
</div>

<table class="userpage-data bg4 borderbot">
    <tr>
    {% if dispatcher.getModuleName() == 'profile' %}
        <td class="col1">
            <a href="{{ url.named('user_view', ['username': owner.lower]) }}">
                <img class="shrinkyicon" alt="{{ owner.username|e }}" src="{{ owner.getAvatar() }}"></a>
            </a>
        </td>
        <td class="col2 p10l">
            <div class="row1">
                <div class="inline"><h2>{{ owner.getSymbol() }} {{ owner.username }}</h2></div>
                {#
                <div class="inline"><?=$admin_user_lookup_url?'<a href="'.$admin_user_lookup_url.'" class="admin_tool"><h2>[ Edit User ]</h2></a>': ''?></div>
                #}

                {% if owner.getVariable('account_disabled') == 1 %}
                <span style="color:orange;font-weight:bold"> <h3>( Account disabled by user )</h3></span>
                {% endif %}
                <br>
                {{ owner.typeartist|e }} | <span class="hideitem2">{{ owner.getStatus() }}</span>
            </div>
            <div class="row2">
                Full Name: {{ owner.fullname|e }}<br>
                Reg'd Since: {{ fa.formatDate(owner.regdate) }}
            </div>
        </td>
    {% else %}
        <td class="col1">
            <span class="usernav_icon_small">
                <a href="{{ url.named('user_view', ['username': owner.lower]) }}">
                    <img class="usernav_icon_resize p20r" alt="{{ owner.username|e }}" src="{{ owner.getAvatar() }}"></a>
                </a>
            </span>
        </td>
        <td class="col2">
            <span class="fontsize20">{{ owner.getSymbol() }} {{ owner.username|e }}</span>
        </td>
    {% endif %}
    </tr>
</table>
<!--- /USER NAV --->