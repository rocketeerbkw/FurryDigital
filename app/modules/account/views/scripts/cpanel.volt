<div id="row">
    <div class="col-md-2">
    {% block navigation %}
        <div id="controlpanelnav" class="hideonmobile">
            <h3>Customize</h3>
            <ul>
                <li><a href="{{ url.route(['module': 'account', 'controller': 'settings', 'action': 'index']) }}">Site Settings</a></li>
                <li><a href="{{ url.route(['module': 'account', 'controller': 'settings', 'action': 'profile']) }}">Edit Profile Page</a></li>
                <li><a href="{{ url.route(['module': 'account', 'controller': 'avatar']) }}">My Avatar</a></li>
            </ul>

            <h3>My Content</h3>
            <ul>
                <li><a href="{{ url.route(['module': 'account', 'controller': 'uploads']) }}">Uploads</a></li>
                <li><a href="{{ url.route(['module': 'account', 'controller': 'journals']) }}">Journals</a></li>
                <li><a href="{{ url.route(['module': 'account', 'controller': 'folders']) }}">Folders</a></li>
                <li><a href="{{ url.route(['module': 'account', 'controller': 'favorites']) }}">Favorites</a></li>
                <li><a href="{{ url.route(['module': 'account', 'controller': 'watches']) }}">Watches</a></li>
                <li><a href="{{ url.route(['module': 'account', 'controller': 'shouts']) }}">Shouts</a></li>
            </ul>

            <h3>Support</h3>
            <ul>
                <li><a href="{{ url.route(['module': 'account', 'controller': 'tickets']) }}">Trouble Tickets</a></li>
            </ul>
        </div>
    {% endblock %}
    </div>
    <div class="col-md-10">
        {% block content %}

        {% endblock %}
    </div>
</div>

