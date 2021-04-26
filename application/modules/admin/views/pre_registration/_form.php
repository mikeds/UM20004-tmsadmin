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
										<label>First Name <span class="text-danger">*</span></label>
										<input name="first-name" class="form-control" placeholder="First Name" value="<?=isset($post['first-name']) ? $post['first-name'] : ""?>" readonly>
										<span class="text-danger"><?=form_error('first-name')?></span>
									</div>
								</div>
								<div class="col-xl-4">
									<div class="form-group">
										<label>Middle Name </label>
										<input name="middle-name" class="form-control" placeholder="Middle Name" value="<?=isset($post['middle-name']) ? $post['middle-name'] : ""?>" readonly>
										<span class="text-danger"><?=form_error('middle-name')?></span>
									</div>
								</div>
								<div class="col-xl-4">
									<div class="form-group">
										<label>Last Name <span class="text-danger">*</span></label>
										<input name="last-name" class="form-control" placeholder="Last Name" value="<?=isset($post['last-name']) ? $post['last-name'] : ""?>" readonly>
										<span class="text-danger"><?=form_error('last-name')?></span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xl-4">
									<div class="form-group">
										<label>Mobile No. </label>
										<input name="mobile-no" class="form-control" placeholder="Mobile No." value="<?=isset($post['mobile-no']) ? $post['mobile-no'] : ""?>" readonly>
										<span class="text-danger"><?=form_error('mobile-no')?></span>
									</div>
								</div>
								<div class="col-xl-4">
									<div class="form-group">
										<label>Email Address <span class="text-danger">*</span></label>
										<input name="email-address" class="form-control" placeholder="Email Address" value="<?=isset($post['email-address']) ? $post['email-address'] : ""?>" readonly>
										<span class="text-danger"><?=form_error('email-address')?></span>
									</div>
								</div>
							</div>
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
						</div>
					</div>
				</div>
			</div>
		</form>
  	</div>
</div>