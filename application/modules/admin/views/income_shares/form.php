<div class="row"> 
	<div class="col-md-12">
		<?=(isset($notification) ? (!empty($notification) ? $notification : '' ) : '') ?>
	</div>     
</div>
<div class="row"> 
	<div class="col-md-6"><br/>
		<form role="form" action="<?=isset($form_url) ? $form_url : '#'?>" method="POST" enctype="multipart/form-data">
			<div class="card">
				<div class="card-header">
					<?=isset($title) ? $title : ""?>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-8">
									<div class="form-group">
										<label>Transaction Type </label>
										<?=isset($tx_types) ? $tx_types : ""?>
										<span class="text-danger"><?=form_error('tx-types')?></span>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>&nbsp; </label>
										<button type="submit" name="search" class="btn btn-block btn-warning">SELECT</button>
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

<div class="row"> 
	<div class="col-md-12"><br/>
		<form role="form" action="<?=isset($form_url) ? $form_url : '#'?>" method="POST" enctype="multipart/form-data">
			<div class="row">
				<div class="col-md-2">
					<div class="card">
						<div class="card-header">
							Income Share Option
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-md-12">
									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<div class="custom-control custom-radio">
													<input type="radio" id="radio1" name="income-type" class="custom-control-input" <?=isset($is_type) ? ($is_type == "1" ? 'checked="checked"' : "") : ''?> value="1">
													<label class="custom-control-label" for="radio1">Fixed</label>
												</div>
												<div class="custom-control custom-radio">
													<input type="radio" id="radio2" name="income-type" class="custom-control-input" <?=isset($is_type) ? ($is_type == "2" ? 'checked="checked"' : "") : ''?> value="2">
													<label class="custom-control-label" for="radio2">Percentage</label>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row"> 
				<div class="col-md-12"><br/>
					<div class="table-responsive">
						<table class="table table-dark table-bordered">
							<thead>
								<tr>
									<th>Income Amount</th>
									<th>Name</th>
									<th>Email Address</th>
									<th>Mobile No.</th>
								</tr>
							</thead>
							<tbody>
								<?=isset($list) ? $list : ""?>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-4"><br>
					<div class="form-group">
						<input type="hidden" name="tx-type" value="<?=isset($tx_type) ? $tx_type : ""?>">
						<button type="submit" name="save" class="btn btn-block btn-success">SAVE</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>