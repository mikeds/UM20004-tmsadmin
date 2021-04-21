<div class="row">
  	<div class="col-xl-12">
    	<form role="form" action="<?=isset($form_url) ? $form_url : '#'?>" method="POST" enctype="multipart/form-data">
			<div class="row">
				<div class="col-xl-12">
					<div class="card">
						<div class="card-header">
							<?=isset($title) ? $title : ""?>
						</div>
						<div class="card-body">
							<div class="row"> 
								<div class="col-md-12">
									<?=(isset($notification) ? (!empty($notification) ? $notification : '' ) : '') ?>
								</div>     
							</div>
							<div class="row">
								<div class="col-xl-4">
									<div class="form-group">
										<label>Email Address <span class="text-danger">*</span></label>
										<input name="email-address" class="form-control" placeholder="Email Address" value="<?=isset($post['email-address']) ? $post['email-address'] : ""?>">
										<span class="text-danger"><?=form_error('email-address')?></span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xl-4">
									<div class="form-group">
										<label>Password <span class="text-danger">*</span></label>
										<input type="password" name="password" class="form-control" placeholder="Password" value="<?=isset($post['password']) ? $post['password'] : ""?>">
										<span class="text-danger"><?=form_error('password')?></span>
									</div>
								</div>
								<div class="col-xl-4">
									<div class="form-group">
										<label>Repeat Password <span class="text-danger">*</span></label>
										<input type="password" name="repeat-password" class="form-control" placeholder="Repeat Password" value="<?=isset($post['repeat-password']) ? $post['repeat-password'] : ""?>">
										<span class="text-danger"><?=form_error('repeat-password')?></span>
									</div>
								</div>
							</div>
							<!-- <div class="row">
								<div class="col-xl-4">
									<div class="form-group">
										<label>Dealer Code </label>
										<input name="merchant-code" class="form-control" placeholder="Merchant Code" value="<?=isset($post['merchant-code']) ? $post['merchant-code'] : ""?>">
										<span class="text-danger"><?=form_error('merchant-code')?></span>
									</div>
								</div>
							</div> -->
							<div class="row">
								<div class="col-xl-4">
									<div class="form-group">
										<label>First Name <span class="text-danger">*</span></label>
										<input name="first-name" class="form-control" placeholder="First Name" value="<?=isset($post['first-name']) ? $post['first-name'] : ""?>">
										<span class="text-danger"><?=form_error('first-name')?></span>
									</div>
								</div>
								<div class="col-xl-4">
									<div class="form-group">
										<label>Middle Name </label>
										<input name="middle-name" class="form-control" placeholder="Middle Name" value="<?=isset($post['middle-name']) ? $post['middle-name'] : ""?>">
										<span class="text-danger"><?=form_error('middle-name')?></span>
									</div>
								</div>
								<div class="col-xl-4">
									<div class="form-group">
										<label>Last Name <span class="text-danger">*</span></label>
										<input name="last-name" class="form-control" placeholder="Last Name" value="<?=isset($post['last-name']) ? $post['last-name'] : ""?>">
										<span class="text-danger"><?=form_error('last-name')?></span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xl-4">
									<div class="form-group">
										<label>Gender <span class="text-danger">*</span></label>
										<?=isset($gender) ? $gender : ""?>
										<span class="text-danger"><?=form_error('gender')?></span>
									</div>
								</div>
								<div class="col-xl-4">
									<div class="form-group">
										<label>Date of Birth <span class="text-danger">*</span></label>
										<input name="dob" type="date" class="form-control" value="<?=isset($post['dob']) ? $post['dob'] : ""?>">
										<span class="text-danger"><?=form_error('dob')?></span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xl-4">
									<div class="form-group">
										<label>Mobile No. </label>
										<input name="mobile-no" class="form-control" placeholder="Mobile No." value="<?=isset($post['mobile-no']) ? $post['mobile-no'] : ""?>">
										<span class="text-danger"><?=form_error('mobile-no')?></span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xl-4">
									<div class="form-group">
										<label>House No./ Unit No. / Building <span class="text-danger">*</span></label>
										<input name="house-no" class="form-control" placeholder="House No./ Unit No. / Building" value="<?=isset($post['house-no']) ? $post['house-no'] : ""?>">
										<span class="text-danger"><?=form_error('house-no')?></span>
									</div>
								</div>
								<div class="col-xl-4">
									<div class="form-group">
										<label>Street <span class="text-danger">*</span></label>
										<input name="street" class="form-control" placeholder="Street" value="<?=isset($post['street']) ? $post['street'] : ""?>">
										<span class="text-danger"><?=form_error('street')?></span>
									</div>
								</div>
								<div class="col-xl-4">
									<div class="form-group">
										<label>Barangay </label>
										<input name="brgy" class="form-control" placeholder="Barangay" value="<?=isset($post['brgy']) ? $post['brgy'] : ""?>">
										<span class="text-danger"><?=form_error('brgy')?></span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xl-4">
									<div class="form-group">
										<label>City <span class="text-danger">*</span></label>
										<input name="city" class="form-control" placeholder="City" value="<?=isset($post['city']) ? $post['city'] : ""?>">
										<span class="text-danger"><?=form_error('city')?></span>
									</div>
								</div>
								<div class="col-xl-4">
									<div class="form-group">
										<label>Provice/Region <span class="text-danger">*</span></label>
										<?=isset($province) ? $province : ""?>
										<span class="text-danger"><?=form_error('province')?></span>
									</div>
								</div>
							</div>
							<?php if (isset($is_update)) { ?>
							<div class="row">
								<div class="col-xl-12">
									<div class="form-control">
										<input type="checkbox" id="status" name="status" value="1" <?=isset($post["status"]) ? $post["status"] : ""?>>
										<label for="status">&nbsp; Check to activate.</label>
									</div>
								</div>
							</div><br>
							<?php } ?>
							<div class="row">
								<div class="col-xl-4">
									<div class="form-group">
										<button type="submit" class="btn btn-block btn-success">SAVE</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
  	</div>
</div>