<div class="site-index">
    <div class="jumbotron">
        <h1>Infogadget products</h1>
    </div>

    <div class="body-content">
        <div class="row">
            <?php foreach ($types as $type):?>
            <div class="col-lg-3">
                <a class="btn btn-lg btn-default" href="<?=\yii\helpers\Url::to(['product/type', 'id'=>$type->id])?>">
                        <?=$type->title?>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
