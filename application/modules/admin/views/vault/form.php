<div class="row">
  	<div class="col-xl-4">
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
								<div class="col-xl-12">
									<div class="form-group">
										<label>Amount <span class="text-danger">*</span></label>
										<input type="number" min="1000" name="amount" class="form-control" placeholder="Amount" value="<?=isset($post['amount']) ? $post['amount'] : ""?>">
										<span class="text-info"><small>Funds is not more than 500,000. You may repeat this process to add funds again.</small></span>
										<span class="text-danger"><?=form_error('amount')?></span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xl-12">
									<div class="form-group">
										<label>Password <span class="text-danger">*</span></label>
										<input type="password" name="password" class="form-control" placeholder="Password" value="<?=isset($post['password']) ? $post['password'] : ""?>">
										<span class="text-danger"><?=form_error('password')?></span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xl-12">
									<div class="form-group">
										<button type="submit" class="btn btn-block btn-danger">Add Funds</button>
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