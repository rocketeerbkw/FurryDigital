{% extends "cpanel.volt" %}

{% block navigation %}
    <h2>Content Upload</h2>

    <h3>{{ type_info['name'] }}</h3>
    <div class="p10">{{ type_info['description'] }}</div>

    <h3>File/Size Limitations</h3>
    <div class="p10">
        <ul>
            <li><strong>Accepted Formats:</strong> {{ type_info['extensions'] }}</li>
            <li><strong>Max File Size:</strong> 10MB</li>
        </ul>
        <br>
        Images larger than 1280x1280 will be resized down to the site limit and converted JPG. If your image is bigger than this, you should resize the image yourself before uploading it to the server.
    </div>

    <h3 class="p10t">Categories & Themes</h3>
    <div class="p10">Picking the proper and relevant categories and themes for your content helps our users locate your content using the Browse system.</div>

    <h3>Content Ratings</h3>
    <div class="p10">Be sure to rate your content appropriately! For more information on our ratings be sure to check out the <a href="{{ url.get('aup') }}" target="_blank"><strong>Acceptable Upload Policy</strong></a>.</div>

    <h3>Linking to Other Users</h3>
    <div class="p10">Need to link to another user in your description? You can easilly link to any any userpage by using <strong>@username</strong> or <strong>:iconusername:</strong></div>
{% endblock %}

{% block content %}
    {% set title='Upload' %}

    {{ form.render() }}
{% endblock %}


