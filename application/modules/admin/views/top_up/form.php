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
							<div class="row ">
								<div class="col-xl-12">
									<div class="form-group">
										<label>Transaction ID </label>
										<input name="transaction-id" class="form-control" value="<?=isset($post['transaction-id']) ? $post['transaction-id'] : ""?>" disabled>
										<span class="text-danger"><?=form_error('transaction-id')?></span>
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
										<button type="submit" class="btn btn-block btn-danger">APPROVE</button>
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