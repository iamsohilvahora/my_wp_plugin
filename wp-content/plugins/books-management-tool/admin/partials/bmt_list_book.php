<h2>List of books</h2>
<table id="list_of_book" class="display" style="width:100%">
        <thead>
            <tr>
                <th>#ID</th>
                <th>Name</th>
                <th>Shelf Name</th>
                <th>Email</th>
                <th>Description</th>
                <th>Book Image</th>
                <th>Publication</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
            if(count($book_list) > 0){
                foreach($book_list as $key=>$value){ ?>
                    <tr>
                        <td><?php echo $value->id; ?></td>
                        <td><?php echo strtoupper($value->name); ?></td>
                        <td>
                            <?php
                                if(!empty($value->shelf_name)){
                                    echo $value->shelf_name;
                                }
                                else{ ?>
                                    <p>Shelf not found</p>
                                <?php }
                            ?>
                        </td>
                        <td><?php echo $value->email; ?></td>
                        <td><?php echo $value->description; ?></td>
                        <td>
                            <?php
                                if(!empty($value->book_image)){ ?>
                                    <img src="<?php echo $value->book_image; ?>" style="width:50px;height:50px;">
                                <?php }
                                else{ ?>
                                    <p>Image not found</p>
                                <?php }
                            ?>
                        </td>
                        <td><?php echo $value->publication; ?></td>
                        <td><?php echo intval($value->amount); ?></td>
                        <?php 
                        if($value->status){ ?>
                        <td><button class="btn btn-success">Active</button></td>
                        <?php }
                        else{ ?>
                        <td><button class="btn btn-danger">Inactive</button></td>
                        <?php }
                        ?>   
                        <td><button class="btn btn-danger delete-book" data-id="<?php echo $value->id; ?>">Delete</button></td>
                    </tr>
                <?php }
            }
        ?>
        </tbody>
        <tfoot>
            <tr>
                <th>#ID</th>
                <th>Name</th>
                <th>Shelf Name</th>
                <th>Email</th>
                <th>Description</th>
                <th>Book Image</th>
                <th>Publication</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </tfoot>
    </table>