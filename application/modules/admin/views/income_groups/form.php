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
								<label>Email Address </label>
								<input name="email-address" class="form-control" placeholder="Email Address" value="<?=isset($post['email-address']) ? $post['email-address'] : ""?>">
								<span class="text-danger"><?=form_error('email-address')?></span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<button type="submit" class="btn btn-block btn-primary">SUBMIT</button>
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
		<div class="card">
			<div class="card-header">
				Income Group Members
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