var zoomed_id = 0;
//closeup view of image file
function zoomImage(id, nfo)
{
	var e = document.getElementById('zoom_image');
	e.setAttribute('src', _ext_core+'file.php?id='+id+_ext_ref);

	zoomed_id = id;

	if (nfo) ajax_get_fileinfo(id, _ext_ref);

	hide_element_by_name('file_gadget_content');
	hide_element_by_name('zoom_video_layer');
	hide_element_by_name('zoom_audio_layer');
	hide_element_by_name('zoom_file_layer');
	show_element_by_name('zoom_image_layer');
}

//show file details of general file
function zoomFile(id, nfo)
{
	zoomed_id = id;

	if (nfo) ajax_get_fileinfo(id, _ext_ref);

	hide_element_by_name('file_gadget_content');
	hide_element_by_name('zoom_video_layer');
	hide_element_by_name('zoom_audio_layer');
	hide_element_by_name('zoom_image_layer');
	show_element_by_name('zoom_file_layer');
}

//closeup view of audio file
function zoomAudio(id, name, nfo)
{
	zoomed_id = id;

	empty_element_by_name('zoom_audio');

	url = _ext_core+'flash/mp3_player.swf?n='+name+'&s='+_ext_core+urlencode('file.php?id=')+id+urlencode(_ext_ref);
	trace(url);
	var fo = new SWFObject(url, 'animationName', '180', '45', '8', '#FFFFFF');
	fo.addParam('allowScriptAccess', 'sameDomain');
	fo.addParam('quality', 'high');
	fo.addParam('scale', 'noscale');
	fo.write('zoom_audio');

	if (nfo) ajax_get_fileinfo(id);

	hide_element_by_name('file_gadget_content');
	hide_element_by_name('zoom_video_layer');
	hide_element_by_name('zoom_image_layer');
	hide_element_by_name('zoom_file_layer');
	show_element_by_name('zoom_audio_layer');
}

//closeup view of video file
function zoomVideo(id, name, nfo)
{
	zoomed_id = id;

	empty_element_by_name('zoom_video');

	//url = _ext_core+'flash/flv_player.swf?movie='+urlencode('/video/')+id+'.flv'+urlencode(_ext_ref)+params;
	url = _ext_core+'flash/mediaplayer.swf';

	w = 176;
	h = 154;
	var fo = new SWFObject(url, 'mediaplayer', w, h, '8', '#FFFFFF');
	fo.addParam("allowfullscreen","true");
	fo.addVariable("width",w);
	fo.addVariable("height",h);
	fo.addVariable("file",urlencode('/video/')+id+'.flv');
	fo.addVariable("autostart", true);
	//fo.addVariable("image","video.jpg");
	fo.write('zoom_video');

	if (nfo) ajax_get_fileinfo(id);

	hide_element_by_name('file_gadget_content');
	hide_element_by_name('zoom_audio_layer');
	hide_element_by_name('zoom_image_layer');
	hide_element_by_name('zoom_file_layer');
	show_element_by_name('zoom_video_layer');
}

function zoomShowFileInfo(txt)
{
	var e = document.getElementById('zoom_fileinfo');
	empty_element(e);

	e.innerHTML = txt;
	show_element_by_name('zoom_fileinfo');
}

function zoom_hide_elements()
{
	hide_element_by_name('zoom_video_layer');
	hide_element_by_name('zoom_audio_layer');
	hide_element_by_name('zoom_image_layer');
	hide_element_by_name('zoom_file_layer');
	hide_element_by_name('zoom_fileinfo');
	hide_cropper();
	hide_resizer();

	show_element_by_name('file_gadget_content');

	var e = document.getElementById('zoom_image');
	e.setAttribute('src', _ext_core+'gfx/ajax_loading.gif');

	if (this.curCrop != null) this.curCrop.remove();
}

function hide_cropper()
{
	if (this.curCrop != null) this.curCrop.remove();
	hide_element_by_name('cropper_toolbar');
}

/* draws a square box on the image, box is resizable to select what area to cut */
function crop_selected_file()
{
	if (this.curCrop != null) this.curCrop.remove();
	this.curCrop = new Cropper.Img("zoom_image", { onEndCrop: onEndCrop1 } );
	show_element_by_name('cropper_toolbar');
	hide_resizer();
}

var cut_x1,cut_y1,cut_x2,cut_y2;
function onEndCrop1(coords, dimensions)
{
	cut_x1 = coords.x1;
	cut_y1 = coords.y1;
	cut_x2 = coords.x2;
	cut_y2 = coords.y2;
}

function crop_selection()
{
	hide_cropper();
	var e = document.getElementById('zoom_image');
	var now = new Date();
	e.src = _ext_core+'image_crop.php?i=' + zoomed_id + '&x1=' + cut_x1 + '&y1=' + cut_y1 + '&x2=' + cut_x2 + '&y2=' + cut_y2 + '&' + now.getTime() + _ext_ref;
}

/* displays a percentage-bar ranging 0-100% and a slider, lets the user move it and see the image resize live in browser. with a save button to commit the resize */
var slide_org_w, slide_org_h, slide_curr_pct;
function resize_selected_file()
{
	hide_cropper();
	show_element_by_name('slider_toolbar');

	e = document.getElementById('zoom_image');
	slide_org_w = e.width;
	slide_org_h = e.height;
	slide_curr_pct = 100;

	if (this.curSlider != null) this.curSlider = null;
 	this.curSlider = new Control.Slider('resize_slider_handle','resize_slider',
		{
			range:$R(25,200),
			onSlide:function(v){
				e = document.getElementById('zoom_image');
				e.width = slide_org_w*(v/100);
			},
			onChange:function(v){
				slide_curr_pct = v;
			},
			sliderValue:100
		}
	);
}

function resize_selection()
{
	var e = document.getElementById('zoom_image');
	var now = new Date();
	e.src = _ext_core+'image_resize.php?i=' + zoomed_id + '&p=' + Math.round(slide_curr_pct) + '&' + now.getTime() + _ext_ref;
	hide_resizer();
}

function cancel_resizer()
{
	e = document.getElementById('zoom_image');
	e.width = slide_org_w;
	hide_resizer();
}

function hide_resizer()
{
	if (this.curSlider != null) this.curSlider = null;
	hide_element_by_name('slider_toolbar');
}

/* displays dialog for moving selected file to another file area category */
function move_selected_file()
{
	alert('fixme: move_selected_file() not yet iplemented');
}

function download_selected_file()
{
	document.location = _ext_core+'file.php?id='+zoomed_id+'&dl'+_ext_ref;
}

function passthru_selected_file()
{
	var w = window.open(_ext_core+'file_pt.php?id='+zoomed_id+_ext_ref, '_blank');
	w.focus();
}

function viewlog_selected_file()
{
	document.location = 'files_viewlog.php?id='+zoomed_id+_ext_ref;
}

function comment_selected_file()
{
	document.location = 'files_comment.php?id='+zoomed_id+_ext_ref;
}

function report_selected_file()
{
	document.location = 'files_report.php?id='+zoomed_id+_ext_ref;
}

//used by image zoomer
function delete_selected_file()
{
	//Send AJAX call for file delete
	ajax_delete_file(zoomed_id);

	//Hide selected file
	zoom_hide_elements();

	//remove zoomed_id thumbnail from file gadget
	hide_element_by_name('file_' + zoomed_id);

	zoomed_id = 0;
}

//used by image zoomer
function rotate_selected_file(angle)
{
	var e = document.getElementById('zoom_image');
	var now = new Date();
	e.src = _ext_core+'image_rotate.php?i=' + zoomed_id + '&a=' + angle + '&' + now.getTime() + _ext_ref;
}
