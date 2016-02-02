{% extends "cpanel.volt" %}

{% block content %}
    {% set title='Avatar' %}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Avatar Upload Policy</h3>
        </div>
        <div class="panel-body">
            Avatars must be work safe. As such, they may be rated no higher than the site's <strong>General</strong> rating.
            <ul class="p10t">
                <li><strong>Language:</strong> Avatars may contain some profanity, though language used in a sexual context is not permitted. Dialogue may be suggestive, contain sexual innuendo, discussion of adult themes, and the expression of views and opinions that users may find offensive, disrespectful, or controversial.</li>
                <li><strong>Nudity/Sexual Situations:</strong> Avatars may not be adult or pornographic in nature.</li>
                <li><strong>Violence:</strong> Avatars may contain mild, violence, but may not be gory, pervasive, or sexual in nature.</li>
                <li><strong>Rapidly Flashing:</strong> Rapidily flashing images which can inflict seizures.</li>
                <li><strong>Copyright:</strong> Be mindful when using artistic works which were not created by you. We will remove avatars at the copyright or character owner's request.</li>
            </ul>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Avatar Gallery</h3>
        </div>
        <div class="panel-body">
            <div class="p10t" style="width:120px">
                <img class="aligncenter" src="{{ user.getAvatar(true) }}" alt="User Avatar"/>
                <span class="fontsize12 aligncenter">Current Avatar</span>
            </div>
            <br/>
            To switch to another previously uploaded avatar, click one of the images below.
            <div style="margin: 15px 0">
                <div class="avatar-list clearfix">
                {% for avatar_key, avatar_info in avatars %}
                    <div class="aligncenter" style="width: 120px; float: left; margin-right: 10px;">
                        <a href="{{ url.routeFromHere(['action': 'choose', 'id': avatar_key]) }}" title="Select This Avatar"><img src="{{  avatar_info['url'] }}" alt=""></a><br>
                        <span class="fontsize12 aligncenter">
                            <a style="cursor: pointer;" onclick="showConfirm('Are you sure you want to delete this avatar?', '{{ url.routeFromHere(['action': 'delete', 'id': avatar_key]) }}');">Delete</a>
                        </span>
                    </div>
                {% endfor %}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Upload New Avatar</h3>
                </div>
                <div class="panel-body">
                    {{ form.render() }}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Use Avatar from Gravatar.com</h3>
                </div>
                <div class="panel-body">
                    <p>Instead of uploading an avatar, you can use your avatar from <a href="http://gravatar.com" target="_blank">Gravatar.com</a>, a globally recognized avatar service that lets you upload a single avatar for multiple popular web sites. Note that we will <b>only use avatars with a "G" rating</b> from Gravatar.</p>

                    <a class="p10" style="display: block; width:120px" href="http://gravatar.com" target="_blank">
                        <img class="aligncenter" src="{{ gravatar }}" alt="Gravatar" />
                        <span class="fontsize12 aligncenter">Your Gravatar</span>
                    </a>

                    <a class="btn btn-default" href="{{ url.routeFromHere(['action': 'gravatar']) }}">Use Avatar from Gravatar.com</a>
                </div>
            </div>
        </div>
    </div>

{% endblock %}