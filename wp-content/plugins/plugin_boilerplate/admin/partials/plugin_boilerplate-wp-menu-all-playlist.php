<div class="container">
	<div class="row">
		<div class="panel panel-primary">
		<div class="panel-heading">All Playlist</div>
		    <div class="panel-body">
		        <table id="example" class="display" style="width:100%">
		             <thead>
		                 <tr>
		                     <th>Sr No</th>
		                     <th>Name</th>
		                     <th>Thumbnail</th>
		                     <th>User Level</th>
		                     <th>Action</th>
		                 </tr>
		             </thead>
		             <tbody id="table-playlist">
		             	<?php
		             		// echo WP_PLUGIN_DIR_PATH;
		             		ob_start(); // Start the buffer
		             		include_once WP_PLUGIN_DIR_PATH.'/admin/partials/templates/plugin-template-all-playlist.php';
		             		// read the buffer
		             		$template = ob_get_contents();
		             		// close the buffer
		             		ob_end_clean();
		             		echo $template;
		             	?>
		             </tbody>
		         </table>	
		    </div>
		</div>
	</div>
</div>