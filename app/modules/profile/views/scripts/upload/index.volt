{% set title='View' %}

<div id="submission_page" class="p402_premium">

    {% include "upload/content.volt" %}
    
    {% include "upload/comment.volt" %}
</div>

{#
{{ javascript_include('//cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js', false) }}

<script type="text/javascript">
    var ctrl_key_hold  = false;
    var shift_key_hold = false;
    var comment_edit_window_sec = {{ edit_duration_sec }}
    
    $(document).ready(function() {
        jQuery(".moment-ago").each(function() {        
            /* Sets whether we're showing the date (ie "Dec 19th, 2015 6:16 PM") 
             * or the time since that date (ie "3 days ago")
             *
             * NOTE: We have the backend pushing the date at the top in case they disable
             *       javascript.
             */
            function setDate(elem, showDate) {
                var moment_obj = moment.unix(elem.attr("mtime")).utcOffset({{ user.getTimezoneDiff() * 60 }})
                
                // Show the date itself OR the time SINCE that date
                elem.text(showDate ? moment_obj.format('MMM Do, YYYY hh:mm A') : moment_obj.fromNow())
            }
            
            // Add click listener to toggle between date and "time ago"
            jQuery(this).click(function() {
                var elem = jQuery(this),
                    state = !elem.prop("showdate")
                
                elem.prop("showdate", state)         
                
                setDate(elem, state)
            })
            
            // Initialize it for consistancy sake
            setDate(jQuery(this), false)
        })
    })
    
    Queue.add(function(){
        document.observe('keydown', function(evt){
            if(evt.keyCode == 16)
                shift_key_hold = true;
            if(evt.keyCode == 17)
                ctrl_key_hold = true;
        });
        document.observe('keyup', function(evt){
            var selected_tags = $$('#keywords .keyword-active');
            var search_keywords = '';

            var delimiter = shift_key_hold ? ' | ' : ' ';
            for(var i=0, cnt=selected_tags.length; i<cnt; i++)
                search_keywords += selected_tags[i].innerHTML.replace(/[^-_\w\d ]+/, '') + delimiter;

            if(search_keywords)
            {
                search_keywords = search_keywords.substr(0, search_keywords.length-(shift_key_hold?3:1));
                var url = '/search/@keywords '+escape(search_keywords);
                window.location.href = url;
            }

            shift_key_hold = false;
            ctrl_key_hold  = false;
        });

        document.observe('click', function(evt){
            if(!evt.findElement('#keywords'))
                return true;

            var elm = evt.element();
            if(elm.tagName.toLowerCase() != 'a')
                return true;

            if(shift_key_hold || ctrl_key_hold)
            {
                evt.stop();
                elm.toggleClassName('keyword-active');
            }
        });
    });
</script>
#}