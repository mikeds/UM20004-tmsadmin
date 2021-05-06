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
    	<form role="form" action="<?=isset($form_url) ? $form_url : '#'?>" method="POST">
			<div class="row">
				<div class="col-xl-12">
					<div class="card">
						<div class="card-header">
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-xl-12">
									<div class="form-group">
										<h5 style="line-height: 1.5em;">Are you sure you want to reject the request of <span class="text-info"><?=isset($post['first-name']) ? $post['first-name'] : ""?> <?=isset($post['last-name']) ? $post['last-name'] : ""?> </span>with the following info : email address  <span class="text-info"><?=isset($post['email-address']) ? $post['email-address'] : ""?></span> and contact number <span class="text-info"><?=isset($post['mobile-no']) ? $post['mobile-no'] : ""?></span> ?</h5>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xl-4">
									<div class="form-group">
										<label>Reason for Disapproval <span class="text-danger">*</span></label>
										<?=isset($reason_for_disapproval) ? $reason_for_disapproval : ""?>
										<span class="text-danger"><?=form_error('reason-for-disapproval')?></span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xl-12">
									<div class="form-group">
										<label for="exampleFormControlTextarea1">Description</label>
										<textarea class="form-control" id="disapproval-desc" name="disapproval-desc" rows="3" placeholder="Description"><?=isset($post['disapproval-desc']) ? $post['disapproval-desc'] : ""?></textarea>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xl-4">
									<div class="form-group">
										<label>Please type CONFIRM and click the submit button<span class="text-danger">*</span></label>
										<input name="confirm-text" class="form-control text-uppercase" placeholder="CONFIRM" value="<?=isset($post['confirm-text']) ? $post['confirm-text'] : ""?>">
										<span class="text-danger"><?=form_error('confirm-text')?></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div><br>
			<div class="row">
				<div class="col-xl-4">
					<div class="form-group">
						<button type="submit" class="btn btn-block btn-warning"><b>SUBMIT</b></button>
					</div>
				</div>
			</div>
		</form>
  	</div>
</div>