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
								<div class="col-xl-6">
									<div class="form-group">
										<label>Merchant </label>
										<?=isset($merchants) ? $merchants : ""?>
										<span class="text-danger"><?=form_error('merchant')?></span>
									</div>
								</div>
								<div class="col-xl-3">
									<div class="form-group">
										<label>Type </label>
										<?=isset($type) ? $type : ""?>
										<span class="text-danger"><?=form_error('type')?></span>
									</div>
								</div>
								<div class="col-xl-3">
									<div class="form-group">
										<label>Value </label>
										<input type="number" set="any" min="1" name="value" class="form-control" placeholder="Value" value="<?=isset($post['value']) ? $post['value'] : ""?>">
										<span class="text-danger"><?=form_error('value')?></span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xl-4">
									<div class="form-group">
										<button type="submit" class="btn btn-block btn-primary">ADD</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
  	</div>
	<div class="col-xl-6">
		<div class="row">
			<div class="col-xl-12">
				<div class="card">
					<div class="card-header">
						Merchant List
					</div>
					<div class="card-body">
						<?php
							if(isset($listing)){
								foreach ($listing as $list) {
									echo $list;
								}
							}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>