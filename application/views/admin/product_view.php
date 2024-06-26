<?php
defined("BASEPATH") or exit("No direct script access allowed");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>

    <script src="../assets/js/vendor/jquery.min.js"></script>
    <script src="../assets/js/vendor/popper.min.js"></script>
    <script src="../assets/js/vendor/bootstrap.min.js"></script>
    <script src="../assets/js/vendor/bootstrap-select.min.js"></script>
    <link rel="stylesheet" href="../assets/css/vendor/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/vendor/bootstrap-select.min.css">
    <link rel="stylesheet" href="../assets/css/custom/admin_global_products.css">
    <script src="../assets/js/global/admin_products.js"></script>
	<!-- toastr cdn -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<!--DataTable css and js-->
	<link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.dataTables.min.css">
	<script src="https://cdn.datatables.net/2.0.3/js/dataTables.min.js"></script>
	<script>
		$(document).ready(function() {
			/*NOTE: handles the fetching of category in form's category dropdown*/
					$.ajax({
						url: "<?=base_url('')?>CategoriesController/fetch_category",
						type: 'GET',
						dataType: 'json',
						success: function(response) {
							response.category.forEach(function(category) {
								let name = category.name;
								let id = category.category_id;
								$('#category_picker').append(`<option value='${id}'>${name}</option>`);
							})
						},
						error: function(jgXHR, textStatus, errorThrown) {
							console.error('AJAX Error:', textStatus, errorThrown);
						}
					});
			/*TODO: Fix the products table*/
			function fetchProduct() {
				$.get("<?=base_url('ProductsController/fetch_all_product');?>", function(response) {
					console.log(response);
					$('.products_table').DataTable({
						ajax: {
							url: "<?=base_url('ProductsController/fetch_all_product');?>",
							dataSrc: 'response'
						},
						columns: [
							/*todo: remove the div tag that contains the product name inside span*/
							{ data: 'image_url',
								render: function(data, type, row)
								{
									if (type === 'display' && data) {
										let image_url = '../' + data;
										return '<span>' +
													'<img src="' + image_url + '" alt="' + row.name + '" width="100">' + '<p>' + row.name + '</p>' +
											   '</span>';
									} else {
										return data;
									}
								}
							},
							{ data: 'product_id',
								render: function(data, row) {
									return '<span>' + data + '</span>';
								}
							},
							{ data: 'name', visible: false },
							{ data: 'category_name',
								render: function(data, row) {
									return '<span>' + data + '</span>';
								}
							},
							{ data: 'price',
								render: function(data, row) {
									return '<span>' + data + '</span>';
								}
							},
							{ data: 'stock_level',
								render: function(data, row) {
									return '<span>' + data + '</span>';
								}
							},
							{ data: null,
								render: function(data, row) {
									return '<button class="btn btn-primary btn-sm delete-btn edit_product" data-id="' + row.category_id + '">Edit</button>' +
											'<button class="btn btn-danger btn-sm delete-btn delete_product" data-id="' + row.category_id + '">X</button>';
								}
							}
						]
					});
					$('.dt-search').addClass('search_form');
				}, 'json');
			}
			fetchProduct();

			/*NOTE:This handles the form submission using ajax*/
			$('#add_product_form').submit(function(e) {
				e.preventDefault();
				let formData = new FormData(this);
				$.ajax({
					url: "<?=base_url('')?>ProductsController/process_add_product",
					type: 'POST',
					data: formData,
					processData: false,
					contentType: false,
					dataType: 'json',
					success: function(response) {
						/*Set the new CSRF token to the hidden input*/
						console.log(response);
						$("input[name='<?= $this->security->get_csrf_token_name() ?>']").val(response.response.newCsrfToken);
						if (response.response.status === 'success') {
							$('.clearable').val('');
							toastr["success"](response.response.message);
						} else {
							toastr['error'](response.response.message);
						}
					},
					error: function(jgXHR, textStatus, errorThrown) {
						console.error('AJAX Error:', textStatus, errorThrown);
					}
				});
				return false;
			});
		});
	</script>
</head>
<script>
</script>

<body>
    <div class="wrapper">
        <header>
            <h1>Let’s provide fresh items for everyone.</h1>

            <!-- <p class="text-danger">Welcome, <?= $this->session->userdata("adminFirstName"); ?></p> -->
            <h2 class="">Products</h2>
            <div>
                <a class="switch" href="catalogue.html">Switch to Shop View</a>
                <button class="profile">
                    <img src="../assets/images/profile.png" alt="#">
                </button>
            </div>
            <div class="dropdown show">
                <a class="btn btn-secondary dropdown-toggle profile_dropdown" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></a>
                <div class="dropdown-menu admin_dropdown" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item" href="<?= base_url("AdminsController/process_logout") ?>">Logout</a>
                    <a class="dropdown-item" href="<?= base_url("AdminsController/view_login_form") ?>">Settings</a>
                </div>
            </div>
        </header>
        <aside>
            <a href="#"><img src="../assets/images/organi_shop_logo_dark.svg" alt="Organic Shop"></a>
            <ul>
                <li><a href="admin_orders.html">Orders</a></li>
                <li class="active"><a href="#">Products</a></li>
                <li><a href="<?=base_url("AdminsController/view_category")?>">Categories</a></li>
            </ul>
        </aside>
        <section>
            <!--<form action="process.php" method="post" class="search_form">
                <input type="text" name="search" placeholder="Search Products">
            </form>-->
            <!-- NOTE: This is the button modal categories -->
            <button class="add_product" data-toggle="modal" data-target="#add_product_modal">Add Product</button>
            <!-- NOTE: This is the sidebar button -->
            <form action="process.php" method="post" class="status_form">
                <h3>Categories</h3>
                <ul>
                    <li>
                        <button type="submit" class="active">
                            <span>36</span><img src="../assets/images/all_orders_icon.svg" alt="#">
                            <h4>All Products</h4>
                        </button>
                    </li>
                    <li>
                        <button type="submit">
                            <span>36</span><img src="../assets/images/pending_icon.svg" alt="#">
                            <h4>Pending</h4>
                        </button>
                    </li>
                </ul>
            </form>
            <!-- NOTE: INSERT products here using AJAX -->
            <div class="data_table">
                <table class="products_table">
                    <thead>
                        <tr>
                           <!-- <th>
                                <h3>All Products</h3>
                            </th>-->
							<th></th>
                            <th>ID #</th>
							<th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Inventory</th>
							<th></th><!--<th>TODO: Insert the buttons here</th>-->
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
							<td></td>
							<td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
							<td></td>
                            <!--<td>
                                <span>
                                    <button class="edit_product">Edit</button>
                                    <button class="delete_product">X</button>
                                </span>
                                <form class="delete_product_form" action="process.php" method="post">
                                    <p>Are you sure you want to remove this item?</p>
                                    <button type="button" class="cancel_remove">Cancel</button>
                                    <button type="submit">Remove</button>
                                </form>
                            </td>-->
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
        <!-- FEATURE: This is modal for Adding a Product -->
        <div class="modal fade form_modal" id="add_product_modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <button data-dismiss="modal" aria-label="Close" class="close_modal"></button>
                    <form id='add_product_form'  method='post' enctype="multipart/form-data">
						<input type='hidden' name='<?= $this->security->get_csrf_token_name() ?>' value='<?= $this->security->get_csrf_hash() ?>'>
                        <h2>Add a Product</h2>
                        <ul>
                            <li>
                                <input type="text" name="product_name" class="clearable" required>
                                <label>Product Name</label>
                            </li>
                            <li>
                                <textarea name="description" class="clearable" required></textarea>
                                <label>Description</label>
                            </li>
                            <li>
                                <label>Category</label>
                                <select id='category_picker' class="selectpicker clearable" name='category'>
									<option disabled selected >Select a category</option>
                                </select>
                            </li>
                            <li>
                                <input type="number" name="price" placeholder="1" class="clearable" required>
                                <label>Price</label>
                            </li>
                            <li>
                                <input type="number" name="inventory" placeholder="1" class="clearable" required>
                                <label>Inventory</label>
                            </li>
                            <div>
								<label>Upload Images (5 Max)</label>
								<input type="file" class="d-block clearable" name="uploadedImages[]" multiple accept="image/*" max="5" >
							</div>
                        </ul>
                        <button type="button" data-dismiss="modal" aria-label="Close">Cancel</button>
                        <button type="submit">Save</button>
                    </form>""
                </div>
            </div>
        </div>
    </div>
    <div class="popover_overlay"></div>
</body>

</html>
