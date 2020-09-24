<div class="row"> 
	<div class="col-md-12">
		<?=(isset($notification) ? (!empty($notification) ? $notification : '' ) : '') ?>
	</div>
</div>
<div class="row"> 
	<div class="col-md-12"><br/>
		<div class="card">
			<div class="card-header">
				<?=isset($title) ? $title : ""?>
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
								<label>TX ID </label>
								<input name="tx-id" class="form-control" placeholder="TX ID" value="<?=isset($post['tx-id']) ? $post['tx-id'] : ""?>">
								<span class="text-danger"><?=form_error('tx-id')?></span>
							</div>
						</div>
						<div class="col-xl-4">
							<div class="form-group">
								<label>Sender Ref ID </label>
								<input name="ref-id" class="form-control" placeholder="Sender Ref ID" value="<?=isset($post['ref-id']) ? $post['ref-id'] : ""?>">
								<span class="text-danger"><?=form_error('ref-id')?></span>
							</div>
						</div>
						<div class="col-xl-4">
							<div class="form-group">
								<label>TX Amount </label>
								<input type="number" name="amount" class="form-control" placeholder="TX Amount" value="<?=isset($post['amount']) ? $post['amount'] : ""?>">
								<span class="text-danger"><?=form_error('amount')?></span>
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
				Ledger Data
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