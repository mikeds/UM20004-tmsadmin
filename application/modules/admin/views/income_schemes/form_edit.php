<div class="row">
  	<div class="col-xl-8">
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
								<div class="col-xl-6">
									<div class="form-group">
										<label>Scheme </label>
										<?=isset($scheme) ? $scheme : ""?>
										<span class="text-danger"><?=form_error('scheme')?></span>
									</div>
								</div>
								<div class="col-xl-6">
									<div class="form-group">
										<label>Position </label>
										<div class="position-wrapper"><?=isset($position) ? $position : ""?></div>
										<span class="text-danger"><?=form_error('position')?></span>
										<input type="hidden" id="scheme-merchant-id" value="<?=isset($scheme_merchant_id) ? $scheme_merchant_id : ""?>">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xl-4">
									<div class="form-group">
										<button type="submit" class="btn btn-block btn-primary">MOVE</button>
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