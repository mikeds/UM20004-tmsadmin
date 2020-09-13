<div class="row">
  	<div class="col-xl-6">
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
							<div class="row ">
								<div class="col-xl-12">
									<div class="form-group">
										<label>Are you sure that you want to delete <?=isset($transaction_fee) ? $transaction_fee : ""?></b>?</label>
										<input name="proceed" class="form-control" placeholder="Proceed" value="<?=isset($post['proceed']) ? $post['proceed'] : ""?>">
										<label><span class="text-danger">Please type "PROCEED" and click DELETE button to continue</span></label>
										<span class="text-danger"><?=form_error('proceed')?></span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xl-6">
									<div class="form-group">
										<button type="submit" class="btn btn-block btn-danger">DELETE</button>
									</div>
								</div>
								<div class="col-xl-6">
									<div class="form-group">
										<a class="btn btn-block btn-warning" href="<?=isset($cancel_url) ? $cancel_url : "#"?>" role="button">Cancel</a>
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