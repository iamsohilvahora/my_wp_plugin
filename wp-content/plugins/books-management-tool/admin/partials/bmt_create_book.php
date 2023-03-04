<?php wp_enqueue_media(); ?>
<div class="row">
	<div class="offset-md-2 col-md-8">
		<h1>Create Book</h1>
		<form action="javascript:void(0);" id="frm_create_book">
			<div class="mb-3">
				<label for="" class="form-label">Select Book Shelf</label>
				<select name="bmt_book_shelf" id="bmt_book_shelf" required>
					<option>Choose Shelf</option>
					<?php
						if(count($book_shelf) > 0){
							foreach ($book_shelf as $key => $value) { ?>
								<option value="<?php echo $value->id; ?>"><?php echo ucwords($value->shelf_name); ?></option>
							<?php }
						}
					?>
				</select>
			</div>

			<div class="mb-3">
				<label for="" class="form-label">Name</label>
				<input type="text" class="form-control" id="book_name" name="book_name" placeholder="Enter name" required>
			</div>

			<div class="mb-3">
				<label for="" class="form-label">Email address</label>
				<input type="email" class="form-control" id="user_email" name="user_email" placeholder="Enter email" required>
			</div>

			<div class="mb-3">
				<label for="" class="form-label">Publication</label>
				<input type="text" class="form-control" id="book_publication" name="book_publication" placeholder="Enter publication" required>
			</div>

			<div class="mb-3">
				<label for="" class="form-label">Description</label>
				<textarea name="book_description" id="book_description" cols="30" rows="4" placeholder="Enter Description" required></textarea>
			</div>

			<div class="mb-3">
				<label for="" class="form-label">Book Image</label>
				<input type="button" value="Upload image" class="form-control" id="book_image" name="book_image" required>
				<img src="" id="show_book_image" style="width:80px;height:80px;" />
				<input type="hidden" name="book_cover_image" id="book_cover_image" value="" />
			</div>

			<div class="mb-3">
				<label for="" class="form-label">Book Cost</label>
				<input type="number" class="form-control" id="book_cost" name="book_cost" placeholder="Enter book cost" required> 
			</div>

			<div class="mb-3">
				<label for="" class="form-label">Status</label>
				<select name="book_status" id="book_status" required> 
					<option value="1">Active</option>
					<option value="0">inactive</option>
				</select>
			</div>

			<button type="submit" class="btn btn-primary">Submit</button>
		</form> 
	</div>
</div>