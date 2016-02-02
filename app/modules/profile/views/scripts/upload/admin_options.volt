<style type="text/css">
    #reason_textarea{width:100%;height:250px}
    .removal_title{width:100%;margin:7px auto}
    #admin_form_div{margin:10px auto 5px;border:0;width:500px;padding:5px}
    #admin_form_div P{margin:5px auto;font-weight:700}
</style>

<script type="text/javascript">
    var plus_image  = "{{ static_url('/img/plus.gif') }}";
    var minus_image = "{{ static_url('/img/minus.gif') }}";

    Queue.add(function() {
        window['toggleReasonForm'] = function() {
            var admin_form_div = document.getElementById('admin_form_div');
            var toggle_image   = document.getElementById('remove_toggle_image');

            if(admin_form_div.style.display == "none"){
                admin_form_div.style.display = "";
                toggle_image.src=minus_image;
            } else {
                admin_form_div.style.display = "none";
                toggle_image.src=plus_image;
            }
        };

        window['sendAction'] = function(action) {
            var admin_form      = document.getElementById('admin_form');
            var admin_form_div  = document.getElementById('admin_form_div');
            var toggle_image    = document.getElementById('remove_toggle_image');
            var deletion_reason = document.getElementById('reason_textarea');

            admin_form.admin_action.value = action;

            ////  Remove the submission case.
            //    Make sure we have the form visible first nd that the reason was supplied
            //
            var form_visible = (admin_form_div.style.display != "none");

            if(action == 'removesubmission') {
                if(form_visible == false) {
                    alert('The deletion reason was not supplied or the deletion form is invisible.');
                    return false;
                }

                if(deletion_reason.value.length == 0) {
                    alert('Please enter the reason for the removal of this submission.');
                    return false;
                }
            }
            //alert(admin_form.submit);

            admin_form.submit();
            return true;
        };
    });
</script>

<div class="p20lr p10t p10b bg2 auto_link borderbot">
    <h3 class="inline">Admin Options</h3>
    <a class="p10l" href="javascript://" onClick="toggleReasonForm(); return false;">Remove Submission</a>
    <a class="p10l" href="javascript://" onClick="sendAction('rebuildthumb'); return false;">Rebuild Thumbnail</a>
</div>

<center class="bg5">
    <div id="admin_form_div" style="display: none">
        <form id="admin_form" name="admin_form" action="/adminaction/" method="POST">
            <input type="hidden" name="admin_action" value="aaa">
            <input type="hidden" name="id" value="{{ upload.id }}">
            <input type="text" name="title" class="textbox removal_title" value="Submission removal: {{ upload.title }}">
            <textarea name="reason" id="reason_textarea" class="textarea"></textarea><br />
            <div class="lineitem">
                <div class="cell alignleft valigntop"><label style="vertical-align: center; "><input type="checkbox" name="skip_pm"  /> Do not send PM</label></div>
                <div class="cell alignright p10t"><input type="button" class="button" name="delete_submission" value="Proceed" onClick="sendAction('removesubmission'); return false;"></div>
            </div>
        </form>
    </div>
</center>