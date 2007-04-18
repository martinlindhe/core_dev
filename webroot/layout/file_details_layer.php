<div id="zoom_image_layer" style="display:none">
	
	<center>
		<img id="zoom_image" src="/gfx/ajax_loading.gif" alt="Image"/><br/>
		<input type="button" class="button" value="Close" onclick="zoomHideElements()"/> 
<? if ($session->isAdmin) { ?>
		<input type="button" class="button" value="Delete image" onclick="delete_selected_file('<?=getProjectPath()?>')"/>
		<input type="button" class="button" value="Rotate left" onclick="rotate_selected_file(90,'<?=getProjectPath()?>')"/>
		<input type="button" class="button" value="Rotate right" onclick="rotate_selected_file(-90,'<?=getProjectPath()?>'))"/>
<? } ?>
	</center>

</div>

<script type="text/javascript" src="/js/ext_flashobject.js"></script>
<div id="zoom_audio_layer" style="display:none">

	<center>
		<div id="zoom_audio" style="width: 160px; height: 50px;"></div>
		<br/>
		<input type="button" class="button" value="Close" onclick="zoomHideElements()"/> 
<? if ($session->isAdmin) { ?>
		<input type="button" class="button" value="Delete song" onclick="delete_selected_file('<?=getProjectPath()?>')"/>
<? } ?>
	</center>

</div>

<div id="zoom_file_layer" style="display:none">
	
	<center>
		<input type="button" class="button" value="Close" onclick="zoomHideElements()"/> 
<? if ($session->isAdmin) { ?>
		<input type="button" class="button" value="Delete file" onclick="delete_selected_file('<?=getProjectPath()?>')"/>
<? } ?>
	</center>

</div>
<div id="zoom_fileinfo" style="display:none"></div>