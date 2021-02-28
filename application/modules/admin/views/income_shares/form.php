<div class="row"> 
	<div class="col-md-12">
		<?=(isset($notification) ? (!empty($notification) ? $notification : '' ) : '') ?>
	</div>     
</div>
<div class="row"> 
	<div class="col-lg-12"><br/>
		<form role="form" action="<?=isset($form_url) ? $form_url : '#'?>" method="POST" enctype="multipart/form-data">
			<div class="row">
				<div class="col-lg-4">
					<div class="card">
						<div class="card-header">
							<h3>Income Shares Mode</h3>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-lg-12">
									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<div class="custom-control custom-radio">
													<input type="radio" id="radio1" name="income-mode" class="custom-control-input" <?=isset($ig_mode) ? ($ig_mode == "1" ? 'checked="checked"' : "") : ''?> value="1">
													<label class="custom-control-label" for="radio1"><h4>Fixed</h4></label>
												</div>
												<div class="custom-control custom-radio">
													<input type="radio" id="radio2" name="income-mode" class="custom-control-input" <?=isset($ig_mode) ? ($ig_mode == "2" ? 'checked="checked"' : "") : ''?> value="2">
													<label class="custom-control-label" for="radio2"><h4>Percentage</h4></label>
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
									<th>Name</th>
									<th>Email Address</th>
									<th>Mobile No.</th>
									<th>Income Limit (Fixed)</th>
									<th>Income Amount</th>
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
						<button type="submit" name="save" class="btn btn-block btn-success">SAVE</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>