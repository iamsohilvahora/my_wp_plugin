<?php wp_enqueue_media(); ?>
<div class="container">
  <div class="row">
    <div class="panel panel-primary">
    <div class="panel-heading">Add Playlist</div>
        <div class="panel-body">
            <form class="form-horizontal" action="javascript:void(0)" id="playlist_form">
              <div class="form-group">
                <label for="playlist_name">Playlist Name</label>
                <input type="text" class="form-control" id="playlist_name" name="name" placeholder="Enter Playlist Name" required>
              </div>
              <div class="form-group">
                <button type="button" class="btn btn-info" id="media-upload">Upload Image</button>
                <span><img src="" id="media-image" style="width: 100px;height: 100px;"></span>
                <input type="hidden" id="image-url" name="image-url" />
              </div>
              <div class="form-group">
                <label for="playlist_name">Playlist For:</label>
                <?php
                    $level_arrays = array('beginner', 'intermediate', 'experts');
                    foreach($level_arrays as $level):
                ?>
                <input type="checkbox" class="form-control" name="level[]" value="<?php echo $level; ?>" class="form-control" /><?php echo ucfirst($level); ?>
                <?php endforeach; ?>
              </div>
              <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
  </div>
</div>

