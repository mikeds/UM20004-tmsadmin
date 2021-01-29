<div class="row"> 
	<div class="col-md-12"><br/>
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
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label>Parent List </label>
										<?=isset($members) ? $members : ""?>
										<span class="text-danger"><?=form_error('members')?></span>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>Email Address </label>
										<input name="email-address" class="form-control" placeholder="Email Address" value="<?=isset($post['email-address']) ? $post['email-address'] : ""?>">
										<span class="text-danger"><?=form_error('email-address')?></span>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>&nbsp; </label>
										<button type="submit" class="btn btn-block btn-primary">SUBMIT</button>
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
		<div id="tree-view">

		</div>
	</div>
</div>