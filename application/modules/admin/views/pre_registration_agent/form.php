<br><br>
<div class="row">
  	<div class="col-xl-12">
	  	<h3><?=isset($title) ? $title : ""?></h3>
	</div>
</div>
<div class="row"> 
	<div class="col-md-12">
		<?=(isset($notification) ? (!empty($notification) ? $notification : '' ) : '') ?>
	</div>     
</div>
<div class="row">
  	<div class="col-xl-12">
    	<form role="form" action="<?=isset($form_url) ? $form_url : '#'?>" method="POST" enctype="multipart/form-data">
			<div class="row">
				<div class="col-xl-12">
					<div class="card">
						<div class="card-header">
							Personal Information
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-xl-12">
									<div class="form-group">
										<label>Profile Picture <span class="text-danger">*</span></label><br>
										<span>
											<?php if(isset($post['profile-picture'])) { ?>
												<a href="<?=$post['profile-picture']?>" target="_blank">View Profile Picture</a>
											<?php } else { echo "* No Profile Picture"; }?>
										</span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xl-4">
									<div class="form-group">
										<label>First Name <span class="text-danger">*</span></label>
										<input name="first-name" class="form-control" placeholder="First Name" value="<?=isset($post['first-name']) ? $post['first-name'] : ""?>" disabled>
										<span class="text-danger"><?=form_error('first-name')?></span>
									</div>
								</div>
								<div class="col-xl-4">
									<div class="form-group">
										<label>Middle Name </label>
										<input name="middle-name" class="form-control" placeholder="Middle Name" value="<?=isset($post['middle-name']) ? $post['middle-name'] : ""?>" disabled>
										<span class="text-danger"><?=form_error('middle-name')?></span>
									</div>
								</div>
								<div class="col-xl-4">
									<div class="form-group">
										<label>Last Name <span class="text-danger">*</span></label>
										<input name="last-name" class="form-control" placeholder="Last Name" value="<?=isset($post['last-name']) ? $post['last-name'] : ""?>" disabled>
										<span class="text-danger"><?=form_error('last-name')?></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div><br>
			<div class="row">
				<div class="col-xl-12">
					<div class="card">
						<div class="card-header">
							Login Information
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-xl-4">
									<div class="form-group">
										<label>Email Address <span class="text-danger">*</span></label>
										<input type="email" name="email-address" class="form-control" placeholder="Email Address" value="<?=isset($post['email-address']) ? $post['email-address'] : ""?>" disabled>
										<span class="text-danger"><?=form_error('email-address')?></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div><br>
			<div class="row">
				<div class="col-xl-12">
					<div class="card">
						<div class="card-header">
							Number Information
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-xl-4">
									<div class="form-group">
										<label>Mobile Number <span class="text-danger">*</span></label>
										<input name="mobile-no" class="form-control" placeholder="Mobile Number" value="<?=isset($post['mobile-no']) ? $post['mobile-no'] : ""?>" disabled>
										<span class="text-danger"><?=form_error('mobile-no')?></span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xl-4">
									<div class="form-group">
										<label>Date of Birth <span class="text-danger">*</span></label>
										<input name="dob" class="form-control" placeholder="Date of Birth" value="<?=isset($post['dob']) ? $post['dob'] : ""?>" disabled>
										<span class="text-danger"><?=form_error('dob')?></span>
									</div>
								</div>
								<div class="col-xl-4">
									<div class="form-group">
										<label>Place of Birth <span class="text-danger">*</span></label>
										<input name="pob" class="form-control" placeholder="Place of Birth" value="<?=isset($post['pob']) ? $post['pob'] : ""?>" disabled>
										<span class="text-danger"><?=form_error('pob')?></span>
									</div>
								</div>
								<div class="col-xl-4">
									<div class="form-group">
										<label>Gender <span class="text-danger">*</span></label>
										<input name="gender" class="form-control" placeholder="Gender" value="<?=isset($post['gender']) ? $post['gender'] : ""?>" disabled>
										<span class="text-danger"><?=form_error('gender')?></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div><br>
			<div class="row">
				<div class="col-xl-12">
					<div class="card">
						<div class="card-header">
							Address Information
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-xl-4">
									<div class="form-group">
										<label>House No./Unit no./Building <span class="text-danger">*</span></label>
										<input name="house-no" class="form-control" placeholder="House No./Unit no./Building" value="<?=isset($post['house-no']) ? $post['house-no'] : ""?>" disabled>
										<span class="text-danger"><?=form_error('house-no')?></span>
									</div>
								</div>
								<div class="col-xl-4">
									<div class="form-group">
										<label>Street <span class="text-danger">*</span></label>
										<input name="street" class="form-control" placeholder="Street" value="<?=isset($post['street']) ? $post['street'] : ""?>" disabled>
										<span class="text-danger"><?=form_error('street')?></span>
									</div>
								</div>
								<div class="col-xl-4">
									<div class="form-group">
										<label>Barangay<span class="text-danger">*</span></label>
										<input name="barangay" class="form-control" placeholder="Barangay" value="<?=isset($post['barangay']) ? $post['barangay'] : ""?>" disabled>
										<span class="text-danger"><?=form_error('barangay')?></span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xl-4">
									<div class="form-group">
										<label>City <span class="text-danger">*</span></label>
										<input name="city" class="form-control" placeholder="City" value="<?=isset($post['city']) ? $post['city'] : ""?>" disabled>
										<span class="text-danger"><?=form_error('city')?></span>
									</div>
								</div>
								<div class="col-xl-4">
									<div class="form-group">
										<label>State/Province <span class="text-danger">*</span></label>
										<input name="province" class="form-control" placeholder="City" value="<?=isset($post['province']) ? $post['province'] : ""?>" disabled>
										<span class="text-danger"><?=form_error('province')?></span>
									</div>
								</div>
								<!-- <div class="col-xl-4">
									<div class="form-group">
										<label>Country/Region <span class="text-danger">*</span></label>
										<?=isset($country) ? $country : ""?>
										<span class="text-danger"><?=form_error('country')?></span>
									</div>
								</div> -->
							</div>
							<div class="row">
								<div class="col-xl-4">
									<div class="form-group">
										<label>Postal Code <span class="text-danger">*</span></label>
										<input name="postal-code" class="form-control" placeholder="Postal Code" value="<?=isset($post['postal-code']) ? $post['postal-code'] : ""?>" disabled>
										<span class="text-danger"><?=form_error('postal-code')?></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div><br>
			<div class="row">
				<div class="col-xl-12">
					<div class="card">
						<div class="card-header">
							Work Information
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-xl-4">
									<div class="form-group">
										<label>Source of Funds <span class="text-danger">*</span></label>
										<input name="sof" class="form-control" placeholder="City" value="<?=isset($post['sof']) ? $post['sof'] : ""?>" disabled>
										<span class="text-danger"><?=form_error('sof')?></span>
									</div>
								</div>
								<div class="col-xl-4">
									<div class="form-group">
										<label>Nature of Work <span class="text-danger">*</span></label>
										<input name="now" class="form-control" placeholder="City" value="<?=isset($post['now']) ? $post['now'] : ""?>" disabled>
										<span class="text-danger"><?=form_error('now')?></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div><br>
			<div class="row">
				<div class="col-xl-12">
					<div class="card">
						<div class="card-header">
							Business Information
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-xl-4">
									<div class="form-group">
										<label>Business Types <span class="text-danger">*</span></label>
										<input name="biz-type" class="form-control" placeholder="Business Type" value="<?=isset($post['biz-type']) ? $post['biz-type'] : ""?>" disabled>
										<span class="text-danger"><?=form_error('biz-type')?></span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xl-12">
									<label>Files:</span></label><br>
									<?=isset($post['files']) ? $post['files'] : ""?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div><br>
			<div class="row">
				<div class="col-xl-12">
					<div class="card">
						<div class="card-header">
							Identification
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-xl-4">
									<div class="form-group">
										<label>ID Types <span class="text-danger">*</span></label>
										<input name="id-type" class="form-control" placeholder="City" value="<?=isset($post['id-type']) ? $post['id-type'] : ""?>" disabled>
										<span class="text-danger"><?=form_error('id-type')?></span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xl-4">
									<div class="form-group">
										<label>ID Number <span class="text-danger">*</span></label>
										<input name="id-no" class="form-control" placeholder="ID Number" value="<?=isset($post['id-no']) ? $post['id-no'] : ""?>" disabled>
										<span class="text-danger"><?=form_error('id-no')?></span>
									</div>
								</div>
								<div class="col-xl-4">
									<div class="form-group">
										<label>Expiration Date <span class="text-danger">*</span></label>
										<input name="exp-date" class="form-control" placeholder="Expiration Date" value="<?=isset($post['exp-date']) ? $post['exp-date'] : ""?>" disabled>
										<span class="text-danger"><?=form_error('exp-date')?></span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xl-12">
									<div class="form-group">
										<label>ID Photo Front <span class="text-danger">*</span></label><br>
										<span>
											<?php if(isset($post['id-front'])) { ?>
												<a href="<?=$post['id-front']?>" target="_blank">View ID Photo Front</a>
											<?php } else { echo "* No ID Photo Front"; }?>
										</span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xl-12">
									<div class="form-group">
										<label>ID Photo Back <span class="text-danger">*</span></label><br>
										<span>
											<?php if(isset($post['id-back'])) { ?>
												<a href="<?=$post['id-back']?>" target="_blank">View ID Photo Back</a>
											<?php } else { echo "* No ID Photo Back"; }?>
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div><br>
			<div class="row">
				<div class="col-xl-12">
					<div class="form-control">
						<input type="checkbox" id="status" name="status" value="1" <?=isset($post["status"]) ? $post["status"] : ""?>>
						<label for="status">&nbsp; <b>Check to approve account.</bb></label>
					</div>
				</div>
			</div><br>
			<div class="row">
				<div class="col-xl-4">
					<div class="form-group">
						<button type="submit" class="btn btn-block btn-warning"><b>APPROVE</b></button>
					</div>
				</div>
			</div>
		</form>
  	</div>
</div>