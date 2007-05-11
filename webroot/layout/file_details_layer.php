<?
	/*
		todo:
			- renama filen och flytta till /core/
				t.ex /core/class.Files.layer.php
					eller nåt liknande, så man ser att den till hör class.Files.php men att det inte är en class-fil
	*/
?>
<div id="zoom_image_layer" style="display:none">
	
	<center>
		<img id="zoom_image" src="/gfx/ajax_loading.gif" alt="Image"/><br/>
		<input type="button" class="button" value="Close" onclick="zoomHideElements()"/> 
		<input type="button" class="button" value="Download" onclick="download_selected_file()"/>
		<input type="button" class="button" value="Pass thru" onclick="passthru_selected_file()"/>

<? if ($session->isAdmin) { ?>
		<input type="button" class="button" value="Cut" onclick="cut_selected_file()"/>
		<input type="button" class="button" value="Resize" onclick="resize_selected_file()"/>
		<input type="button" class="button" value="Rotate left" onclick="rotate_selected_file(90)"/>
		<input type="button" class="button" value="Rotate right" onclick="rotate_selected_file(-90)"/>
		<input type="button" class="button" value="Move image" onclick="move_selected_file()"/>
		<input type="button" class="button" value="Delete image" onclick="delete_selected_file()"/>
<? } ?>
	</center>

</div>

<script type="text/javascript" src="/js/ext_flashobject.js"></script>
<div id="zoom_audio_layer" style="display:none">

	<center>
		<div id="zoom_audio" style="width: 160px; height: 50px;"></div>
		<br/>
		<input type="button" class="button" value="Close" onclick="zoomHideElements()"/> 
		<input type="button" class="button" value="Download" onclick="download_selected_file()"/>
		<input type="button" class="button" value="Pass thru" onclick="passthru_selected_file()"/>

<? if ($session->isAdmin) { ?>
		<input type="button" class="button" value="Move song" onclick="move_selected_file()"/>
		<input type="button" class="button" value="Delete song" onclick="delete_selected_file()"/>
<? } ?>
	</center>

</div>

<div id="zoom_video_layer" style="display:none">

	<center>
		<div id="zoom_video" style="width: 160px; height: 50px;"></div>
		<br/>
		<input type="button" class="button" value="Close" onclick="zoomHideElements()"/> 
		<input type="button" class="button" value="Download" onclick="download_selected_file()"/>
		<input type="button" class="button" value="Pass thru" onclick="passthru_selected_file()"/>

<? if ($session->isAdmin) { ?>
		<input type="button" class="button" value="Move video" onclick="move_selected_file()"/>
		<input type="button" class="button" value="Delete video" onclick="delete_selected_file()"/>
<? } ?>
	</center>

</div>

<div id="zoom_file_layer" style="display:none">
	
	<center>
		<input type="button" class="button" value="Close" onclick="zoomHideElements()"/> 
		<input type="button" class="button" value="Download" onclick="download_selected_file()"/>
		<input type="button" class="button" value="Pass thru" onclick="passthru_selected_file()"/>

<? if ($session->isAdmin) { ?>
		<input type="button" class="button" value="Move file" onclick="move_selected_file()"/>
		<input type="button" class="button" value="Delete file" onclick="delete_selected_file()"/>
<? } ?>
	</center>

</div>
<div id="zoom_fileinfo" style="display:none"></div>