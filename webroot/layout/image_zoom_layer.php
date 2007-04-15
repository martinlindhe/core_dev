<div id="zoom_image_layer" style="display:none">
	
	<center>
		<img id="zoom_image" src="/gfx/ajax_loading.gif" alt="Image"/><br/>
		<div id="zoom_image_info"></div><br/>
		<input type="button" class="button" value="Close" onclick="hide_element_by_name('zoom_image_layer')"/> 
<? if ($session->isAdmin) { ?>
		<input type="button" class="button" value="Delete image" onclick="delete_selected_file()"/>
		<input type="button" class="button" value="Rotate left" onclick="rotate_selected_file(90)"/>
		<input type="button" class="button" value="Rotate right" onclick="rotate_selected_file(-90)"/>
<? } ?>
	</center>

</div>

<script type="text/javascript" src="/js/ext_flashobject.js"></script>
<div id="zoom_audio_layer" style="display:none">

	<center>
		<div id="zoom_audio" style="width: 160px; height: 50px;"></div>
		<br/>
		<input type="button" class="button" value="Close" onclick="hide_element_by_name('zoom_audio_layer')"/> 
<? if ($session->isAdmin) { ?>
		<input type="button" class="button" value="Delete song" onclick="delete_selected_file()"/>
<? } ?>
	</center>

</div>