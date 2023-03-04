<div class="row">
	<div class="offset-md-2 col-md-8">
		<h1>Create Book Shelf</h1>
		<button class="btn btn-primary" id="first_ajax_request">First AJAX Request</button>
		<form action="javascript:void(0);" id="frm_add_book_shelf">
			<div class="mb-3">
				<label for="" class="form-label">Name</label>
				<input type="text" class="form-control" id="txt_name" name="txt_name" placeholder="Enter book shelf name" required>
			</div>

			<div class="mb-3">
				<label for="" class="form-label">Capacity</label>
				<input type="number" class="form-control" id="txt_capacity" name="txt_capacity" placeholder="Enter capacity" required>
			</div>

			<div class="mb-3">
				<label for="" class="form-label">Enter location</label>
				<input type="text" class="form-control" id="txt_location" name="txt_location" placeholder="Enter location" required>
			</div>

			<div class="mb-3">
				<label for="" class="form-label">Status</label>
				<select name="book_status" id="book_status">
					<option value="1">Active</option>
					<option value="0">inactive</option>
				</select>
			</div>

			<button type="submit" class="btn btn-primary">Submit</button>


		</form> 
	</div>
</div>