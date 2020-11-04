<div class="row"> 
	<div class="col-md-12">
		<?=(isset($notification) ? (!empty($notification) ? $notification : '' ) : '') ?>
	</div>
</div>
<div class="row"> 
	<div class="col-md-12"><br/>
		<div class="card">
			<div class="card-header">
				Ledger Filter
			</div>
			<div class="card-body">
				<form role="form" action="<?=isset($form_url) ? $form_url : '#'?>" method="POST" enctype="multipart/form-data">
					<div class="row"> 
						<div class="col-md-12">
							<?=(isset($notification) ? (!empty($notification) ? $notification : '' ) : '') ?>
						</div>     
					</div>
					<div class="row">
						<div class="col-xl-4">
							<div class="form-group">
								<label>Merchant List </label>
								<?=isset($merchants) ? $merchants : ""?>
								<span class="text-danger"><?=form_error('merchant')?></span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xl-4">
							<div class="form-group">
								<label>Date From </label>
								<input type="date" name="from" class="form-control" placeholder="Date From" value="<?=isset($post['from']) ? $post['from'] : ""?>">
								<span class="text-danger"><?=form_error('from')?></span>
							</div>
						</div>
						<div class="col-xl-4">
							<div class="form-group">
								<label>Date To </label>
								<input type="date" name="to" class="form-control" placeholder="Date To" value="<?=isset($post['to']) ? $post['to'] : ""?>">
								<span class="text-danger"><?=form_error('to')?></span>
							</div>
						</div>
						<div class="col-xl-4">
							<div class="form-group">
								<label>Sorted By</label>
								<?=isset($sort) ? $sort : ""?>
								<span class="text-danger"><?=form_error('sort')?></span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xl-4">
							<div class="form-group">
								<button type="submit" class="btn btn-block btn-success">Search</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<div class="row"> 
	<div class="col-md-12"><br/>
		<div class="card">
			<div class="card-header">
				<?=isset($title) ? $title : ""?>
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