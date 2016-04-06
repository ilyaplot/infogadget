<div class="site-index">

    <div class="jumbotron">
        <h1>Infogadget products</h1>
    </div>

    <div class="body-content">

        <div class="row">
            <?php foreach ($products as $product):?>
            <div class="col-lg-12">
                <h2><?=$product->type->title?> <?=$product->brand->title?> <?=$product->title?> <?=!empty($product->model) ? "({$product->model})" : ''?></h2>
                <?php if ($product->images): ?>
                    <img class="thumbnail" src="<?=$product->images[0]->uri?>"/>
                <?php endif;?>
                <?php if ($product->options !== '[]'):?>
                <table class="table table-striped">
                    <?php foreach (json_decode($product->options, true) as $option):?>
                    <tr>
                        <td><?=$option['title']?></td>
                        <td><?=$option['value']?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                <?php endif;?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
