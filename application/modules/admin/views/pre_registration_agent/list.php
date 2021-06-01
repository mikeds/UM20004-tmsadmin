<div class="row"> 
	<div class="col-md-12">
		<?=(isset($notification) ? (!empty($notification) ? $notification : '' ) : '') ?>
	</div>
</div>

<div class="row"> 
	<div class="col-md-12"><br/>
		<div class="card">
			<div class="card-body">
			<form role="form" action="<?=isset($form_url) ? $form_url : '#'?>" method="POST">
				<div class="row">
					<div class="col-xl-4">	
						<div class="form-group">
							<label>Search Agent Request </label>
							<input name="search_term" class="form-control" placeholder="Search Agent Request" value="<?=isset($post['search_term']) ? $post['search_term'] : ""?>">
							<span class="text-danger"><?=form_error('ref-id')?></span>
						</div>
					</div>
				</div>
				<div class="col-xs-4">
					<div class="col-xl-4">
						<div class="form-group">
							<button type="submit" class="btn btn-block btn-success">Search</button>
						</div>
					</div>				
				</div>
			</div>
			</form>
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