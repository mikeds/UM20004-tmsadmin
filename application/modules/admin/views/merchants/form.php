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
										<label>Merchant Code </label>
										<input name="merchant-code" class="form-control" placeholder="Merchant Code" value="<?=isset($post['merchant-code']) ? $post['merchant-code'] : ""?>">
										<span class="text-danger"><?=form_error('merchant-code')?></span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xl-8">
									<div class="form-group">
										<label>Business Name <span class="text-danger">*</span></label>
										<input name="business-name" class="form-control" placeholder="Business Name" value="<?=isset($post['business-name']) ? $post['business-name'] : ""?>">
										<span class="text-danger"><?=form_error('business-name')?></span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xl-12">
									<div class="form-group">
										<label>Business Address</label>
										<input name="address" class="form-control" placeholder="Business Address" value="<?=isset($post['address']) ? $post['address'] : ""?>">
										<span class="text-danger"><?=form_error('address')?></span>
									</div>
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
								<div class="col-xl-4">
									<div class="form-group">
										<label>Contact Person </label>
										<input name="contact-person" class="form-control" placeholder="Contact Person" value="<?=isset($post['contact-person']) ? $post['contact-person'] : ""?>">
										<span class="text-danger"><?=form_error('contact-person')?></span>
									</div>
								</div>
								<div class="col-xl-4">
									<div class="form-group">
										<label>Contact No. </label>
										<input name="contact-no" class="form-control" placeholder="Contact No." value="<?=isset($post['contact-no']) ? $post['contact-no'] : ""?>">
										<span class="text-danger"><?=form_error('contact-no')?></span>
									</div>
								</div>
							</div>
							<?php if (isset($is_update)) { ?>
							<div class="row">
								<div class="col-xl-12">
									<div class="form-control">
										<input type="checkbox" id="status" name="status" value="1" <?=isset($post["status"]) ? $post["status"] : ""?>>
										<label for="status">&nbsp; Uncheck to deactivate account.</label>
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