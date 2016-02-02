{% extends "cpanel.volt" %}

{% block content %}
    {% set title='My Folders' %}

    {%- macro folder_row(folders) %}
        {% for folder in folders %}
            <tr class="folder-row folder-{{ folder.id }} group-{{ folder.group_id }} order-{{ folder.sort_order }} input">
                <td>&nbsp;</td>
                <td class="name-desc">
                    <a href="{{ url.get('gallery/'~user.lower~'/folder/'~folder.id) }}" class="folder-name dotted" target="_blank"><strong>{{ folder.name|e }}</strong></a>
                    {% if folder.description %}
                        <div><small>
                            {{ folder.description|e }}
                        </small></div>
                    {% endif %}
                </td>
                <td class="actions">
                    {% if loop.first != true %}
                        <a href="{{ url.routeFromHere(['action': 'movefolder', 'id': folder.id, 'direction': 'up']) }}" class="btn btn-sm btn-primary">&uarr;</a>
                    {% endif %}
                    {% if loop.last != true %}
                        <a href="{{ url.routeFromHere(['action': 'movefolder', 'id': folder.id, 'direction': 'down']) }}" class="btn btn-sm btn-primary">&darr;</a>
                    {% endif %}
                    <a href="{{ url.routeFromHere(['action': 'editfolder', 'id': folder.id]) }}" class="btn btn-sm btn-default">Edit</a>
                    <a href="{{ url.routeFromHere(['action': 'deletefolder', 'id': folder.id]) }}" class="btn btn-sm btn-danger">Delete</a>
                </td>
                <td align="center">
                    {{ folder.num_files }}
                </td>
                <td align="center">
                    {{ fa.formatDate(folder.updated_at) }}
                </td>
            </tr>
        {% endfor %}
    {%- endmacro %}

    <div class="hideondesktop">
        <div class="container-item-top">
            <h3>Folder Management</h3>
        </div>

        <div class="container-item-bot">
            We are working on making this page mobile friendly. Rather than offer a sub-par experience, we encourage users to manage their gallery folders using a laptop or desktop.
        </div>
    </div>
    <div class="hideonmobile">
        <div class="container-item-top">
            <h3>My Folders</h3>
        </div>

        <div class="container-item-bot">
            <ul style="margin: 0; padding-left: 16px">
                <li>Folders can be combined into groups, creating a two-level hierarchy.</li>
                <li>It is possible to assign an upload into multiple folders.</li>
                <li>Folders and groups will be displayed everywhere in the same order as presented below.</li>
                <li>Removing a Folder Group will NOT delete any folders it contains. Instead, the assigned folders will simply become un-grouped.</li>
                <li>Deleting folders will NOT remove uploads assigned to it.</li>

                <li><strong>Maximum folders allowed:</strong> {{ max_folders }}.
                <li><strong>Maximum groups allowed:</strong> {{ max_groups }}.</li>
            </ul>
            <br>
            <div class="buttons">
                <a class="btn btn-success" href="{{ url.routeFromHere(['action': 'editfolder']) }}">+ Add Folder</a>
                <a class="btn btn-success" href="{{ url.routeFromHere(['action': 'editgroup']) }}">+ Add Group</a>
            </div>
        </div>

    {% if groups %}
        <div class="container-item-top">
            <h3>Manage Folders in Groups</h3>
        </div>
        <div class="container-item-bot">
            <table cellpadding="0" cellspacing="0" border="0" width="100%" class="table table-striped">
                <colgroup>
                    <col width="3%">
                    <col width="42%">
                    <col width="25%">
                    <col width="15%">
                    <col width="15%">
                </colgroup>
                <thead>
                    <tr>
                        <th colspan="2" align="left">Folders in Groups</th>
                        <th>Actions</th>
                        <th align="center">Uploads</th>
                        <th align="center">Last Updated</th>
                    </tr>
                </thead>
                <tbody>
                {% for group in groups %}
                    <tr class="group-row group-{{ group.id }} order-{{ group.sort_order }} input">
                        <td colspan="2">
                            <div class="fonthighlight"><strong>{{ group.name|e }}</strong></div>
                        </td>
                        <td class="actions">
                            {% if loop.first != true %}
                                <a href="{{ url.routeFromHere(['action': 'movegroup', 'id': group.id, 'direction': 'up']) }}" class="btn btn-sm btn-primary">&uarr;</a>
                            {% endif %}
                            {% if loop.last != true %}
                                <a href="{{ url.routeFromHere(['action': 'movegroup', 'id': group.id, 'direction': 'down']) }}" class="btn btn-sm btn-primary">&darr;</a>
                            {% endif %}
                            <a href="{{ url.routeFromHere(['action': 'editgroup', 'id': group.id]) }}" class="btn btn-sm">Edit</a>
                            <a href="{{ url.routeFromHere(['action': 'deletegroup', 'id': group.id]) }}" class="btn btn-sm btn-danger">Delete</a>
                        </td>
                        <td align="center">
                            &nbsp;
                        </td>
                        <td align="center">
                            {{ fa.formatDate(group.updated_at) }}
                        </td>
                    </tr>
                    {{ folder_row(group.folders) }}
                {% endfor %}
                </tbody>
            </table>
        </div>
    {% endif %}
    {% if folders %}
        <div class="container-item-top">
            <h3>Manage Ungrouped Folders</h3>
        </div>

        <div class="container-item-bot">
            <table cellpadding="0" cellspacing="0" border="0" width="100%" class="table table-striped">
                <colgroup>
                    <col width="3%">
                    <col width="42%">
                    <col width="25%">
                    <col width="15%">
                    <col width="15%">
                </colgroup>
                <thead>
                <tr>
                    <th colspan="2" align="left">Unassigned Folders</th>
                    <th>Actions</th>
                    <th align="center">Uploads</th>
                    <th align="center">Last Updated</th>
                </tr>
                </thead>
                <tbody>
                {{ folder_row(folders) }}
                </tbody>
            </table>
        </div>
    {% endif %}
    </div>
{% endblock %}