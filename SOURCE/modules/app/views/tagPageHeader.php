<div class="page-header-content header-elements-md-inline">
	<div class="page-title d-flex">
		<h4><a href="<?= Yii::$app->request->referrer ?>"><i class="icon-arrow-left52 mr-2"></i></a><?= $pageTitle ?></h4>
		<a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
	</div>

	<div class="header-elements d-none mb-3 mb-md-0">
		<div class="d-flex justify-content-center">
			<?php if(isset($headerElements) && $headerElements) :?>
				<?php foreach($headerElements as $element): ?>
				<?= $element ?>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
</div>