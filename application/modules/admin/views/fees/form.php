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
								<div class="col-xl-4">
									<div class="form-group">
										<label>From Value </label>
										<input type="number" min="1" name="from" class="form-control" placeholder="From Value" value="<?=isset($post['from']) ? $post['from'] : ""?>">
										<span class="text-danger"><?=form_error('from')?></span>
									</div>
								</div>
								<div class="col-xl-4">
									<div class="form-group">
										<label>To Value </label>
										<input type="number" min="2" name="to" class="form-control" placeholder="to" value="<?=isset($post['to']) ? $post['to'] : ""?>">
										<span class="text-danger"><?=form_error('to')?></span>
									</div>
								</div>
								<div class="col-xl-4">
									<div class="form-group">
										<label>Amout Fee </label>
										<input type="number" min="1" name="fee-amount" class="form-control" placeholder="Fee Amount" value="20">
										<span class="text-danger"><?=form_error('fee-amount')?></span>
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
						Transaction Fees
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