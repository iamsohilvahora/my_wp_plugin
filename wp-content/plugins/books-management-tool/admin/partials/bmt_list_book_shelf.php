<h2>List of books</h2>
<table id="list_of_shelf_book" class="display" style="width:100%">
        <thead>
            <tr>
                <th>#ID</th>
                <th>Name</th>
                <th>Capacity</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
                if(count($book_shelf) > 0){
                    foreach($book_shelf as $key=>$value){ ?>
                        <tr>
                            <td><?php echo $value->id; ?></td>
                            <td><?php echo strtoupper($value->shelf_name); ?></td>
                            <td><?php echo intval($value->capacity); ?></td>
                             <?php 
                             if($value->status){ ?>
                                <td><button class="btn btn-success">Active</button></td>
                             <?php }
                             else{ ?>
                                <td><button class="btn btn-danger">Inactive</button></td>
                             <?php }
                             ?>   
                            <td><button class="btn btn-danger delete-book-shelf" data-id="<?php echo $value->id; ?>">Delete</button></td>
                        </tr>
                    <?php }
                }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th>#ID</th>
                <th>Name</th>
                <th>Capacity</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </tfoot>
    </table>