//
//
//
//
//
//
//
// Common functions
//
//
//
//
//
//
//

//
//
// Debug framework. Make it `not work` correctly unless we're using FF with firebug.
//
//
if(typeof(console) == 'undefined' || typeof(console.groupEnd) == 'undefined') var console = {
	log   : function(){},
	debug : function(){},
	info  : function(){},
	warn  : function(){},
	error : function(){},
	assert: function(){},
	dir   : function(){},
	dirxml: function(){},
	trace : function(){},
	group : function(){},
	groupEnd: function(){},
	time  : function(){},
	timeEnd : function(){},
	profile : function(){},
	profileEnd : function(){},
	count   : function(){}
};




//
////
//
//////	 COMMON.JS
//
////
//
function checkAll(docform, elementname, selectit, btn)
{
	var docelements = docform.elements;
	var button = $(btn);
	for (n=0;n< docelements.length;n++){
		if (elementname=='' || elementname && docelements.item(n).name == elementname)
		{
			if(selectit)
				docelements.item(n).checked = true;
			else
				docelements.item(n).checked = false;
		}
	}
}


function atoggle(img,id)
{
	var div = $(id);
	var elm = $(img);

	// if the div is hidden, show it...
	if(div.style.display == 'block')
	{
		div.style.display = '';
		elm.src = STATIC_PATH+'/img/plus.gif';
	}
	else
	{
		div.style.display = 'block';
		elm.src = STATIC_PATH+'/img/minus.gif';
	}
}


function showConfirm(message,url)
{
	if(confirm(message))
	{
		document.location = url;
	}
}



//
//// YAK
//


function updateCounter()
{
	var max_num;
	var rest;
	var temp_msg;
	max_num = 222;

	if(typeof(messageForm) == 'undefined')
	{
		var messageForm;
		messageForm = document.getElementById('JSMessage');
	}

	if(typeof(restCounter) == 'undefined')
	{
		var restCounter;
		restCounter = document.getElementById('chars_left');
	}


	if(messageForm.value != null)
	{
		temp_msg = messageForm.value;
		rest     = max_num - temp_msg.length;
		if (rest < 0 )
		{
			messageForm.value = temp_msg.substring(0, max_num);
			rest = 0;
		}
		restCounter.value = rest;
	}

	return (rest > 0)
}


function toggle(element)
{
	element = $(element);

	if (element.style.display == 'none')
		element.style.display = '';
	else
		element.style.display = 'none';
}





var form_submitted = false;
function submit_form()
{
	if(form_submitted == true)
	{
		return;
	}

	document.myform.mysubmit.value = 'Sending...';
	document.myform.mysubmit.disabled = true;
	form_submitted = true;
	document.myform.submit();
}








////
//
//// FORM ELEMENTS
//
////


////  Removes all options from a given SELECT element, leaving only the first one selected
//
function select_clear(select_id)
{
	var select_element = $(select_id);

	if (select_element == null || select_element.tagName != 'SELECT')
	{
		return;
	}


	select_element.disable();

	while (select_element.length > 1)
	{
		select_element.options[1] = null;
	}
	select_element.options[0].selected = true;

	select_element.enable();
}


//// Adds the OPTIONs to the supplied SELECT, taken from an array of objects
//
//   Objects in the array are supposed to have a "value" and a "name" property
//
function select_build(select_id, item_array)
{
	var select_element = $(select_id);

	if (select_element == null || select_element.tagName != 'SELECT')
	{
		return;
	}

	var orig_size = select_element.length;
	var limit	 = item_array.size();
	for (var i=0; i<limit; i++)
	{
		select_element.options[orig_size+i] = new Option(item_array[i].name, item_array[i].value);
	}
}



////  Selects a specific option in a given select. Selection on option value.
//
function select_choose(select_id, value_to_select)
{
	var select_element = $(select_id);

	if (select_element == null || select_element.tagName != 'SELECT')
	{
		return;
	}

	var optionsLength = select_element.options.length;
	var i;

	for (i=0; i<optionsLength; i++)
	{
		if (select_element.options[i].value == value_to_select)
		{
			select_element.options[i].selected = true;
		}
	}
}










//
////
//
//////	 COOKIE.JS
//
////
//
var today       = new Date();
var expiryyear  = new Date(today.getTime() + 365 * 24 * 60 * 60 * 1000);
var expirymonth = new Date(today.getTime() + 30 * 24 * 60 * 60 * 1000);
var expiryday   = new Date(today.getTime() + 24 * 60 * 60 * 1000);

function getCookie(name)
{
	var reg= new RegExp("; "+name+";|; "+name+"=([^;]*)");
	var matches=reg.exec('; '+document.cookie+';');
	if (matches) return ((matches[1])?unescape(matches[1]):'');
	return null;
}

function setCookie(name,value,expires,path,domain,secure)
{
	document.cookie = name + "=" + escape (value) +
	((expires) ? "; expires=" + expires.toGMTString() : "") +
	((path) ? "; path=" + path : "") +
	((domain) ? "; domain=" + domain : "") +
	((secure) ? "; secure" : "");

	return getCookie(name)!=null?true:false;
}

function deleteCookie(name,path,domain)
{
	if (getCookie(name)!=null)
	{
		document.cookie = name + "=" +
		((path) ? "; path=" + path : "") +
		((domain) ? "; domain=" + domain : "") +
		"; expires=Thu, 01-Jan-1970 00:00:01 GMT";
	}
}

function cookieEnabled()
{
	testCookieName="_testCookie_";
	if (setCookie(testCookieName,1))
	{
		deleteCookie(testCookieName);
		return true;
	}
	else return false;
}




//
////
//
//////	 BBCODE.JS
//
////
//
function bbtag(text)
{
	var field = $('JSMessage');

	field.value += ' '+text;
}




//
////
//
//////  TEXTAREA control functions
//
////
//
/**
 * Retrieve currently selected text inside a specified textarea
 *
 * @author                      Original script: guys at http://www.mybboard.net/, this modification: yak, yuri.kushinov@gmail.com
 * @param  {DOMNode} textarea   Textarea to operate on
 * @return                      Currently selected text inside the given textarea
 * @type   String
 * @final
 */
function getSelectedText(textarea)
{
	textarea.focus();
	if(document.selection)
	{
		var selection = document.selection;
		var range = selection.createRange();

		if((selection.type == 'Text' || selection.type == 'None') && range != null)
			return range.text;
	}
	else if(textarea.selectionEnd)
	{
		var select_start = textarea.selectionStart;
		var select_end = textarea.selectionEnd;
		if(select_end <= 2)
			select_end = textarea.textLength;

		var start = textarea.value.substring(0, select_start);
		var middle = textarea.value.substring(select_start, select_end);

		return middle;
	}
	return null;
}






/**
 * Insert the given text before and after the selected text is a specified textarea,
 * OR
 * Insert both pre- and post-pended text at the caret position in the specified textarea.
 *
 * @author                       Original script: guys at http://www.mybboard.net/, this modification: yak, yuri.kushinov@gmail.com
 * @param {DOMNode} textarea     Textarea to operate on
 * @param {String}  open_tag     Prepend the selected text with the value of thos variable
 * @param {String}  close_tag    Append the value of this variable to the selected text. Can be FALSE in case of a self-closing tag.
 * @final
 */
function performInsert(textarea, open_tag, close_tag)
{
	if(!close_tag){
		close_tag = "";
    }

	textarea.focus();

	if(document.selection)
	{
		// IE
		var selection = document.selection;
		var range = selection.createRange();

		if((selection.type == "Text" || selection.type == "None") && range != null)
		{
			if(close_tag != "" && range.text.length > 0)
			{
				range.text = open_tag+range.text+close_tag;
			}
			else
			{
				range.text = open_tag+close_tag+range.text;
			}
			range.select();
		}
		else
		{
			textarea.value += open_tag+close_tag;
		}
	}
	else if(textarea.selectionEnd)
	{
		// Mozilla
		var select_start = textarea.selectionStart;
		var select_end = textarea.selectionEnd;
		var scroll_top = textarea.scrollTop;

		if(select_end <= 2)
		{
			select_end = textarea.textLength;
		}

		var start = textarea.value.substring(0, select_start);
		var middle = textarea.value.substring(select_start, select_end);
		var end = textarea.value.substring(select_end, textarea.textLength);

        var keep_selected;
		if(select_end - select_start > 0 && close_tag != "")
		{
			keep_selected = true;
			middle = open_tag+middle+close_tag;
		}
		else
		{
			keep_selected = false;
			middle = open_tag+close_tag+middle;
		}

		textarea.value = start+middle+end;

		if(keep_selected == true)
		{
			textarea.selectionStart = select_start;
			textarea.selectionEnd = select_start + middle.length;
		}
		else
		{
			textarea.selectionStart = select_start + middle.length;
			textarea.selectionEnd = textarea.selectionStart;
		}
		textarea.scrollTop = scroll_top;
	}
	else
	{
		textarea.value += open_tag+close_tag;
	}
}








/**
 *
 *
 *
 *
 *
 * Gallery type pages.
 * Submissiontype icon mouseover and mouseout events that show the description popup
 *
 *
 *
 *
 */
// This is a hack :|
//
var _root_container_element = 'li';
var popup_width      = 400;
var popup_x_offset   = 10;
var popup_y_offset   = -2;
var width_autocorrect_offset = 2;
function submissiontype_icon_mouseover(description_fill_callback, evt)
{
	// IE has a top offset of -2 already
	//
	use_popup_y_offset = popup_y_offset;
	if(Prototype.Browser.IE)
		use_popup_y_offset += 2;

	//
	//
	// Create the popup container and elements
	//
	//
	var tmp = $('description_popup');
	if(!tmp)
	{
		tmp           = document.createElement('div');
		tmp.id        = 'description_popup';
		tmp.className = 'invisible';

		tmp2 = document.createElement('h5');
		tmp.appendChild(tmp2);

		tmp3 = document.createElement('span');
		tmp.appendChild(tmp3);

		document.getElementsByTagName('body')[0].appendChild(tmp);
	}


	var container = evt.findElement(_root_container_element);

	// IE shows the tile's ALT text even when whe hover the overlay icon on top of it.
	// So for IE, we'll remove the ALT text on hover.
	//
	if(Prototype.Browser.IE)
		container.getElementsByTagName('img')[0].alt = '';



	console.group('Showing description for gallery tile %o', container);

	var id = /id_(\d+)/.exec(container.id)[1];

	var desc = eval('descriptions.id_'+id);
	if(desc)
	{
		console.info('Description found, %o. Proceeding...', desc);
		console.info('Calling the description fill callback, %o', description_fill_callback);

		// Filter the description popup's content
		//
		var desc_popup   = $('description_popup');
		var data         = Object.clone(desc);
		data.title       = data.title.escapeHTML().gsub("\n", '');
		data.description = desc.description.escapeHTML().gsub(/(\r?\n){3,}/, "\n\n").gsub("\n", '<br />').

			gsub(/:(icon|link)([^:]+):/, function(match){return '<em>'+match[2]+'</em>'}).
			gsub(/:([^:]+)(icon|link):/, function(match){return '<em>'+match[1]+'</em>'}).

			gsub(/\[url=[^\]]+\](.+?)\[\/url\]/, function(match){return '<em>'+match[1]+'</em>'}).
			gsub(/\[url\](.+?)\[\/url\]/, function(match){return '<em>'+match[1]+'</em>'}).

			gsub(/\[b\](.+?)\[\/b\]/, function(match){return '<strong>'+match[1]+'</strong>'}).
			gsub(/\[i\](.+?)\[\/i\]/, function(match){return '<em>'+match[1]+'</em>'}).
			gsub(/\[s\](.+?)\[\/s\]/, function(match){return '<strike>'+match[1]+'</strike>'}).
			gsub(/\[u\](.+?)\[\/u\]/, function(match){return '<u>'+match[1]+'</u>'});


		//
		// Calling the callback to fill the description
		//
		description_fill_callback(desc_popup, data);



		console.log('Calculating popup position...');

		// Figure out the coordinates where to put the description popup
		//
		var viewport_x = document.viewport.getWidth();
		var viewport_y = document.viewport.getHeight();

		var img  = container.select('a img')[0];
		var tmp2 = img.cumulativeOffset();
		var container_x = tmp2.left;
		var container_y = tmp2.top;

		var container_width  = img.getWidth();
		var container_height = img.getHeight();

		//
		//
		// Initial popup placement and height
		//
		//
		var spawn_x = container_x + container_width + popup_x_offset;
		var spawn_y = container_y + use_popup_y_offset;

		var use_popup_width = popup_width;



		//
		// Corrections on axis X
		//

		if(spawn_x + use_popup_width < viewport_x)
		{
			// Horizontal direction = right
			//
			console.info('Horizontal position: right of tile');
		}
		else
		{
			// Horizontal direction = left
			//

			// Initial left placement X
			//
			spawn_x = container_x - popup_x_offset - use_popup_width;

			var tmp3            = document.viewport.getScrollOffsets()[0];
			var left_overflow  = spawn_x - tmp3;
			var max_width      = tmp3 + document.viewport.getDimensions().width;
			var right_overflow = max_width - (container_x + container_width + popup_x_offset + use_popup_width);



			if(left_overflow < 0)
			{
				// Popup went off the left side of the screen
				// We should determine on which side to show it, with reduced width
				//
				console.warn('Not enough space to fit the poup on neither sides');
				console.log('Overflow on the left: %d', left_overflow);
				console.log('Overflow on the right: %d', right_overflow);


				if(left_overflow > right_overflow)
				{
					// More space on the left
					//
					use_popup_width -= Math.abs(left_overflow) + width_autocorrect_offset;
					spawn_x = container_x - popup_x_offset - use_popup_width;
					console.info('Reducing popup width by %d, to %dpx', Math.abs(left_overflow), use_popup_width);
					console.info('Horizontal position: left of tile');
				}
				else
				{
					// More space on the right
					//
					use_popup_width -= Math.abs(right_overflow) + width_autocorrect_offset*2;
					spawn_x = container_x + container_width + popup_x_offset;
					console.info('Reducing popup width by %dpx, to %dpx', Math.abs(right_overflow), use_popup_width);
					console.info('Horizontal position: right of tile');
				}
			}
			else
			{

				console.info('Horizontal position: left of tile');
			}
		}




		//
		// Corrections on axis Y
		// Compensate for the height gain from adding variable length text to the popup.
		//
		var scroll_offset_y = document.viewport.getScrollOffsets()[1];
		var window_max_y    = (window.innerHeight ? window.innerHeight : document.documentElement.clientHeight) + scroll_offset_y;
		var popup_height    = desc_popup.getHeight();
		var popup_max_y     = spawn_y + popup_height;

		if(spawn_y < scroll_offset_y)
		{
			console.warn('Compensating for the popup overflow at the top. Moving the popup down by %dpx', scroll_offset_y - spawn_y);
			spawn_y = scroll_offset_y + 5;
		}
		else if(popup_max_y > window_max_y)
		{
			console.warn('Compensating for the popup overflow at the bottom. Moving the popup up by %dpx', popup_max_y - window_max_y);
			spawn_y -= popup_max_y - window_max_y + 5;
		}


		// Move to the position and show
		//
		desc_popup.style.left = spawn_x + 'px';
		desc_popup.style.top  = spawn_y + 'px';
		console.info('Moving popup to absolute coordinates: %dx%d', spawn_x, spawn_y);

		desc_popup.style.width = use_popup_width+'px';

		desc_popup.removeClassName('invisible');
		console.info('Displaying popup.');
	}
	else
	{
		console.warn('Description not found, aborting');
	}
}

function submissiontype_icon_mouseout(evt)
{
	$('description_popup').addClassName('invisible');
	// Processing ends when popup is closed
	//
	console.info('MouseOut event received. Hiding popup');
	console.groupEnd();
}

function check_password_strength(password)
{
    //
    // Calculate password bits
    //
    var alpha       = "abcdefghijklmnopqrstuvwxyz";
    var upper       = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    var upper_punct = "~`!@#$%^&*()-_+=";
    var digits      = "1234567890";

    var totalChars = 0x7f - 0x20;
    var alphaChars = alpha.length;
    var upperChars = upper.length;
    var upper_punctChars = upper_punct.length;
    var digitChars = digits.length;
    var otherChars = totalChars - (alphaChars + upperChars + upper_punctChars + digitChars);

    var bits = 0;
    if(password.length)
    {
        var fAlpha      = false;
        var fUpper      = false;
        var fUpperPunct = false;
        var fDigit      = false;
        var fOther      = false;
        var charset     = 0;

        for(var i=0; i<password.length; i++)
        {
            var character = password.charAt(i);

            if(alpha.indexOf(character) != -1)
                fAlpha = true;
            else if(upper.indexOf(character) != -1)
                fUpper = true;
            else if(digits.indexOf(character) != -1)
                fDigit = true;
            else if(upper_punct.indexOf(character) != -1)
                fUpperPunct = true;
            else
                fOther = true;

        }

        if(fAlpha)
            charset += alphaChars;
        if(fUpper)
            charset += upperChars;
        if(fDigit)
            charset += digitChars;
        if(fUpperPunct)
            charset += upper_punctChars;
        if(fOther)
            charset += otherChars;

        bits = Math.log(charset) * (password.length / Math.log(2));
    }
    //
    //

    if (bits >= 128) {
        return 'best';
    }
    else if (bits < 128 && bits >= 64) {
        return 'strong';
    }
    else if (bits<64 && bits>=56) {
        return 'medium';
    }
    else if (bits<56) {
        return 'weak';
    }
    else {
        return 'unrated';
    }
}


function register_logout_onclick_handler(){
    var logout_links = $$('.logout-link');
    if(logout_links.length){
        logout_links.invoke('observe', 'click', function(evt){
            if(!confirm("Are you sure you want to log out?")){
                evt.stop();
            }
        });
    }
}




function gallery_toggle_descriptions(){
    var galleries = $$('center.flow');
    var cookie_name = 'nodesc';
    var cookie_path = '/';
    var cookie_domain = '.furry.digital';
    if(galleries){
        galleries[0].hasClassName(cookie_name) ? deleteCookie(cookie_name, cookie_path, cookie_domain) : setCookie(cookie_name, 1, expiryyear, cookie_path, cookie_domain);
        galleries.invoke('toggleClassName', 'nodesc');
    }
}

function parse_bbcode(text){
    return  text.
            gsub(/(\r?\n){3,}/, "\n\n").gsub("\n", '<br />').

            gsub(/:(icon|link)([^:]+):/, function(match){return '<em>'+match[2]+'</em>'}).
            gsub(/:([^:]+)(icon|link):/, function(match){return '<em>'+match[1]+'</em>'}).

            gsub(/\[url=[^\]]+\](.+?)\[\/url\]/, function(match){return '<em>'+match[1]+'</em>'}).
            gsub(/\[url\](.+?)\[\/url\]/, function(match){return '<em>'+match[1]+'</em>'}).

            gsub(/\[b\](.+?)\[\/b\]/, function(match){return '<strong>'+match[1]+'</strong>'}).
            gsub(/\[i\](.+?)\[\/i\]/, function(match){return '<em>'+match[1]+'</em>'}).
            gsub(/\[s\](.+?)\[\/s\]/, function(match){return '<strike>'+match[1]+'</strike>'}).
            gsub(/\[u\](.+?)\[\/u\]/, function(match){return '<u>'+match[1]+'</u>'});
}




var description_timer, description_elm;
function description_icon_mouseover(evt){
    console.group('description_icon_mouseover()');
    var delay = 200;
    window['description_elm'] = evt.element().up('b');
    console.log('waiting %d msec before proceeding', delay);
    description_timer = setTimeout(_description_icon_mouseover, delay);
}
function _description_icon_mouseover(){
	_description_show_for_container($(window['description_elm']));
}

function description_icon_mouseout(evt){
    console.log('description_icon_mouseout()');
    clearTimeout(description_timer);
    description_popup_hide();
    console.groupEnd();
}

function description_icon_click(evt){
    console.group('description_icon_click()');
    console.log('preventing click event');
    evt.stop();

    description_popup_hide();

    var description_elm = evt.element();
    var cell = description_elm.up('b');
    console.log('cell: %o', cell);

    if(cell){
        _description_show_for_container(cell);
    } else {
        console.log('image cell not found');
    }
    console.groupEnd();
}

function _description_show_for_container(cell){
    console.group('_description_show_for_container()');

    var tmp = /^sid_(\d+)$/.exec(cell.id);
    if(tmp){
        var submission_id = tmp[1];
        console.log('submission id: %d', submission_id);

        var description_data = descriptions[submission_id];
        if(description_data){
            console.log('description_data: %o', description_data);

            //
            // content
            //
            var title       = parse_bbcode(description_data.title);
            var description = parse_bbcode(description_data.description);
            var username    = description_data.username;
            var lower       = description_data.lower;
            var avatar_mtime= description_data.avatar_mtime;

            var container = cell.up('center.flow');
            var view_mode = /with-[a-z_-]+/.exec(container.className);
            var force_owner_mode = container.hasClassName('force-owner-mode');

            console.log('view_mode: %s', view_mode);

            var img_container = cell.down('s').down('a');
            console.log('img_container: %o', img_container);

            // create popup containeer
            var popup_elm = new Element('div', {
                id: 'description_popup',
                className: 'invisible'
            });
            var title_elm = new Element('h5', {
                className: 'header'
            });
            title_elm.innerHTML = title;


            var description_elm = new Element('div', {
                className: 'wrapper'
            });
            description_elm.innerHTML = description;

            popup_elm.appendChild(title_elm);

            if(view_mode != 'with-titles' && !force_owner_mode){
                title_elm.innerHTML = title_elm.innerHTML + ' <small>by</small> ' + username;
                if(!avatar_mtime) {
                    var d = new Date();
                    var month = d.getUTCMonth() + 1;
                    var day   = d.getUTCDate();
                    var year  = d.getUTCFullYear();
                    day   = day   < 10 ? '0'+day  :''+day;
                    month = month < 10 ? '0'+month:''+month;
                    avatar_mtime = year+month+day;
                }
                var avatar_elm = new Element('img', {
                    src: '//a.facdn.net/'+avatar_mtime+'/'+lower+'.gif',
                    className: 'avatar'
                });
                description_elm.insert({top:avatar_elm});
            }
            popup_elm.appendChild(description_elm);
            document.body.appendChild(popup_elm);

            //
            // positioning
            //
            var thumb = cell.down('a');

            var thumb_position = thumb.cumulativeOffset();
            var thumb_width    = thumb.getWidth();
            var popup_width    = popup_elm.getWidth();
            var document_width = document.viewport.getWidth();
            var center_point   = parseInt(thumb_position.left + thumb_width/2, 10);

            console.log('thumb_position: %d', thumb_position);
            console.log('thumb_width: %d', thumb_width);
            console.log('popup_width: %d', popup_width);
            console.log('document_width: %d', document_width);
            console.log('center_point: %d;  half the document width: %d', center_point, document_width/2);

            var position_left, position_top, width, diff;
            width = popup_width;

            if(center_point > document_width/2) {
                console.log('thumb on the right side of the screen');
                console.log('more space on the left side');
                position_left = thumb_position.left - 10 - popup_width;
                position_top  = thumb_position.top;
                // popup width correction
                if(position_left < 0) {
                    console.log('popup will go offscreen on the left, correcting width')
                    diff = -position_left + 5;
                    console.log('diff: %d', diff);
                    width -= diff;
                    position_left = 5;
                    console.log('width: %d', width);
                }
                console.log('position_left: %d', position_left);
                console.log('position_top: %d', position_top);
            } else {
                console.log('thumb on the left side of the screen');
                console.log('more space on the right side');
                position_left = thumb_position.left + thumb_width + 10;
                position_top  = thumb_position.top;
                // popup width correction
                if(position_left+popup_width > document_width) {
                    console.log('popup will go offscreen on the right, correcting width')
                    diff = position_left+popup_width + 5 - document_width;
                    console.log('diff: %d', diff);
                    width  -= diff;
                    console.log('width: %d', width);
                }
                console.log('position_left: %d', position_left);
                console.log('position_top: %d', position_top);
            }

            popup_elm.setStyle({
                left: position_left + 'px',
                top: position_top + 'px',
                width: width + 'px'
            });

            popup_elm.removeClassName('invisible');

            document.observe('click', description_popup_hide);
        } else {
            console.log('description data not found');
        }
    } else {
        console.log('image id was not found');
    }
    console.groupEnd();
}
function description_popup_hide(evt){
    console.group('description_popup_hide()');

    if(!evt || (evt && !evt.findElement('#description_popup'))){
        console.log('removing description popup');
        var popup_elm = $('description_popup');
        popup_elm && popup_elm.remove();

        console.log('unregistering the popup hide on click anywhere on page callback');
        document.stopObserving('click', description_popup_hide);
        console.groupEnd();
    }
}



function highlight_new_comments(minutes) {
    var comments = $$('.container-comment');
    var comment_timestamp;
    var highlight_period = 60*minutes;
    for(var i=0, cnt=comments.length; i<cnt; i++) {
        comment_timestamp = parseInt(comments[i].readAttribute('data-timestamp'), 10);
        if(comment_timestamp + highlight_period > server_timestamp) {
            // comment is new
            comments[i].addClassName('new');
        } else {
            // comment is old
            comments[i].removeClassName('new');
        }
    }
}


function edit_links_hide_handler() {
    var edit_links = $$('a.edit-link');
    var current_server_datetime = parseInt(((new Date()).getTime())/1000 + server_timestamp_delta, 10);

    if(edit_links.length) {
        var num_active = 0;
        for(var i=0, cnt=edit_links.length, container, comment_date; i<cnt; i++) {
            container = edit_links[i].up('table.container-comment');
            comment_date = parseInt(container.readAttribute('data-timestamp'), 10);

            if(current_server_datetime - comment_date < comment_edit_window_sec) {
                num_active++;
            } else {
                edit_links[i].up('span').addClassName('hidden');
            }

        }
        if(num_active) {
            setTimeout(edit_links_hide_handler, 5000);
        }
    }
}


function readable_date_min(time_left) {
    var minutes = Math.floor(time_left / 60);
    var seconds = time_left % 60;

    minutes = minutes.toString();
    seconds = seconds.toString();

    minutes = minutes.length == 1 ? '0' + minutes : minutes;
    seconds = seconds.length == 1 ? '0' + seconds : seconds;

    return minutes+'m'+seconds+'s';
}

 

function init_news_block() {
    var news_block = $('news');
    if(news_block){
        news_block.down('.dismiss').observe('click', function(evt){
            var date = parseInt($('news').className.replace('date-', ''), 10);
            if(date){
                setCookie(news_cookie_name, date, expiryyear, '/', cookie_domain);
                news_block.remove();
            }
        });
    }
}

