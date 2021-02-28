<div class="row"> 
	<div class="col-md-4"><br/>
		<form role="form" action="<?=isset($form_url) ? $form_url : '#'?>" method="POST" enctype="multipart/form-data">
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
						<div class="col-md-12">
							<div class="form-group">
								<label><b>CONFIRMATION</b> </label>
								<input name="confirmation" class="form-control" placeholder="Plese type DELETE to confirm">
								<span class="text-danger"><?=form_error('confirm')?></span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<button type="submit" class="btn btn-block btn-danger">DELETE</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>