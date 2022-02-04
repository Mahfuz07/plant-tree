<?php
?>



<div class="container-fluid">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <img alt="news image" class="mt-3" width="350" height="190" src="<?php echo $news['news_image']?>">
                    <h4 class="card-widget__title text-dark mt-3"><?php echo $news['title']?></h4>
                </div>
            </div>
            <div class="card-footer border-0 bg-transparent">
                <div class="row center">
                    <div><?php echo $news['content']?></div>
                </div>
            </div>
        </div>
    </div>
</div>
